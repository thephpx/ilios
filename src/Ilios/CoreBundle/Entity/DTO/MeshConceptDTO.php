<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class MeshConceptDTO
 *
 * @IS\DTO
 */
class MeshConceptDTO
{
    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     * @deprecated
     */
    public $umlsUid;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $preferred;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $scopeNote;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $casn1Name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $registryNumber;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     * @deprecated
     */
    public $semanticTypes;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $createdAt;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $updatedAt;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $descriptors;

    /**
     * MeshConceptDTO constructor.
     * @param $id
     * @param $name
     * @param $umlsUid
     * @param $preferred
     * @param $scopeNote
     * @param $cash1Name
     * @param $registryNumber
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct(
        $id,
        $name,
        $umlsUid,
        $preferred,
        $scopeNote,
        $casn1Name,
        $registryNumber,
        $createdAt,
        $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->umlsUid = $umlsUid;
        $this->preferred = $preferred;
        $this->scopeNote = $scopeNote;
        $this->casn1Name = $casn1Name;
        $this->registryNumber = $registryNumber;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        $this->semanticTypes = [];
        $this->terms = [];
        $this->descriptors = [];
    }
}
