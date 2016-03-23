<?php

namespace Ruvents\DataReconstructor\Fixtures;

/**
 * Class TestClass2
 * @package Ruvents\DataReconstructor\Fixtures
 */
class TestClass2
{
    /**
     * @var int
     */
    public $int;

    /**
     * @var \Ruvents\DataReconstructor\Fixtures\TestClass3
     */
    public $class3;

    /**
     * @var \DateTime
     */
    public $date;

    public function setDate($date)
    {
        $this->date = new \DateTime($date);

        return $this;
    }
}
