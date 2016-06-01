<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Classes\SchoolEvent;

/**
 * Class SchoolManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SchoolManager extends DTOManager
{
    /**
     * @param int $schoolId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return SchoolEvent[]
     */
    public function findEventsForSchool($schoolId, \DateTime $from, \DateTime $to)
    {
        return $this->getRepository()->findEventsForSchool($schoolId, $from, $to);
    }
}
