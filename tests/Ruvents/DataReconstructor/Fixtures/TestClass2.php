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
    private $int;

    /**
     * @var \Ruvents\DataReconstructor\Fixtures\TestClass3
     */
    public $class3;

    /**
     * @var \DateTime
     */
    public $date;

    /**
     * @param int $int
     * @return $this
     */
    public function setInt($int)
    {
        $this->int = $int;

        return $this;
    }
}
