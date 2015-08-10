<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class IlmSessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $dt->setDate(2016, 1, 1);
        $arr[] = array(
            'id' => 1,
            'hours' => $this->faker->randomDigitNotNull,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['3'],
            'instructorGroups' => [],
            'instructors' => [],
            'learners' => [],
            'session' => 4
        );
        $dt->modify('+1 month');
        $arr[] = array(
            'id' => 2,
            'hours' => $this->faker->randomDigitNotNull,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => ['3'],
            'instructors' => [],
            'learners' => [],
            'session' => 5
        );

        $dt->modify('+1 month');
        $arr[] = array(
            'id' => 3,
            'hours' => $this->faker->randomDigitNotNull,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => [],
            'instructors' => ['1'],
            'learners' => [],
            'session' => 6
        );

        $dt->modify('+1 month');
        $arr[] = array(
            'id' => 4,
            'hours' => $this->faker->randomDigitNotNull,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => [],
            'instructorGroups' => [],
            'instructors' => [],
            'learners' => ['1'],
            'session' => 7
        );

        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'id' => 5,
            'hours' => $this->faker->randomDigitNotNull,
            'dueDate' => $dt->format('c'),
            'learnerGroups' => ['1', '2'],
            'instructorGroups' => ['1', '2'],
            'instructors' => ['1', '2'],
            'learners' => ['1', '2']
        );
    }

    public function createInvalid()
    {
        return [];
    }
}