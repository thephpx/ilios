<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\ArchivableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LockableEntityInterface;

/**
 * Interface ProgramYearInterface
 * @package Ilios\CoreBundle\Entity
 */
interface ProgramYearInterface extends IdentifiableEntityInterface, LockableEntityInterface, ArchivableEntityInterface
{
    /**
     * @param int $startYear
     */
    public function setStartYear($startYear);

    /**
     * @return int
     */
    public function getStartYear();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd);

    /**
     * @return boolean
     */
    public function isPublishedAsTbd();

    /**
     * @param ProgramInterface $program
     */
    public function setProgram(ProgramInterface $program);

    /**
     * @return ProgramInterface
     */
    public function getProgram();

    /**
     * @param Collection $directors
     */
    public function setDirectors(Collection $directors);

    /**
     * @param UserInterface $director
     */
    public function addDirector(UserInterface $director);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getDirectors();

    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies);

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency);

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies();

    /**
     * @param Collection $disciplines
     */
    public function setDisciplines(Collection $disciplines);

    /**
     * @param DisciplineInterface $discipline
     */
    public function addDiscipline(DisciplineInterface $discipline);

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines();

    /**
     * @param Collection $objectives
     */
    public function setObjectives(Collection $objectives);

    /**
     * @param ObjectiveInterface $objective
     */
    public function addObjective(ObjectiveInterface $objective);

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getObjectives();

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent);

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent();
}