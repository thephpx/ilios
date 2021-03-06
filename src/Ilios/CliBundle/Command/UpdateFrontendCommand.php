<?php

namespace Ilios\CliBundle\Command;

use Alchemy\Zippy\Zippy;
use Ilios\CoreBundle\Service\Config;
use Ilios\CoreBundle\Service\Fetch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use Ilios\CoreBundle\Service\Filesystem;

/**
 * Pull down asset archive from AWS and extract it so
 * assets can be served from the API host.
 *
 * Class UpdateFrontendCommand
 * @package Ilios\CliBUndle\Command
 */
class UpdateFrontendCommand extends Command implements CacheWarmerInterface
{
    /**
     * @var string
     */
    const FRONTEND_DIRECTORY = '/ilios/frontend/';
    const ARCHIVE_FILE_NAME = 'frontend.tar.gz';
    const UNPACKED_DIRECTORY = '/deploy-dist/';

    const STAGING_CDN_ASSET_DOMAIN = 'https://frontend-archive-staging.iliosproject.org/';
    const PRODUCTION_CDN_ASSET_DOMAIN = 'https://frontend-archive-production.iliosproject.org/';

    const STAGING = 'stage';
    const PRODUCTION = 'prod';

    /**
     * @var Fetch
     */
    protected $fetch;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Zippy
     */
    protected $zippy;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $temporaryFileStorePath;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $environment;

    public function __construct(
        Fetch $fetch,
        Filesystem $fs,
        Config $config,
        Zippy $zippy,
        $kernelCacheDir,
        $kernelProjectDir,
        $apiVersion,
        $environment
    ) {
        $this->fetch = $fetch;
        $this->fs = $fs;
        $this->config = $config;
        $this->zippy = $zippy;
        $this->cacheDir = $kernelCacheDir;
        $this->apiVersion = $apiVersion;
        $this->environment = $environment;

        $this->temporaryFileStorePath = $kernelProjectDir . '/var/tmp/frontend-update-files';
        if (!$this->fs->exists($this->temporaryFileStorePath)) {
            $this->fs->mkdir($this->temporaryFileStorePath);
        }

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:update-frontend')
            ->setDescription('Updates the frontend to the latest version.')
            ->addOption(
                'staging-build',
                null,
                InputOption::VALUE_NONE,
                'Pull a staging build of the frontend'
            )
            ->addOption(
                'at-version',
                null,
                InputOption::VALUE_REQUIRED,
                'Request a specific version of the frontend (instead of the default active one)'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stagingBuild = $input->getOption('staging-build');
        $versionOverride = $input->getOption('at-version');
        $environment = $stagingBuild?self::STAGING:self::PRODUCTION;

        $this->downloadAndExtractArchive($environment, $versionOverride);

        $message = 'Frontend updated successfully';
        if ($stagingBuild) {
            $message .= ' from staging build';
        }
        if ($versionOverride) {
            $message .= ' to version ' . $versionOverride;
        }
        $output->writeln("<info>{$message}!</info>");
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        try {
            $version = false;
            $releaseVersion = $this->config->get('frontend_release_version');
            $keepFrontendUpdated = $this->config->get('keep_frontend_updated');
            if (!$keepFrontendUpdated) {
                $version = $releaseVersion;
            }
            $this->downloadAndExtractArchive(self::PRODUCTION, $version);
        } catch (\Exception $e) {
            if ($this->environment === 'prod') {
                throw new \Exception(
                    'Unable to load the frontend.  Please try again or let the Ilios Team know about this issue:' .
                    $e->getMessage()
                );
            }
            print "Unable to load frontend.  Please run ilios:maintenance:update-frontend again. \n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @param string $environment
     * @param string|bool $versionOverride
     *
     * @throws \Exception
     */
    protected function downloadAndExtractArchive($environment = 'prod', $versionOverride = false)
    {
        $fileName = $this->apiVersion . '/' . self::ARCHIVE_FILE_NAME;
        if ($versionOverride) {
            $fileName .= ':' . $versionOverride;
        }
        $url = self::PRODUCTION_CDN_ASSET_DOMAIN;
        if ($environment === self::STAGING) {
            $url = self::STAGING_CDN_ASSET_DOMAIN;
        }
        $string = $this->fetch->get($url . $fileName);
        $archiveDir = $this->temporaryFileStorePath;
        $archivePath = $archiveDir . '/' . self::ARCHIVE_FILE_NAME;
        $this->fs->dumpFile($archivePath, $string);

        $archive = $this->zippy->open($archivePath);
        $archive->extract($archiveDir);
        $frontendPath = $this->cacheDir . self::FRONTEND_DIRECTORY;
        $this->fs->remove($frontendPath);
        $this->fs->rename($archiveDir . self::UNPACKED_DIRECTORY, $frontendPath);
    }
}
