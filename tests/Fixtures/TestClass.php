<?php

namespace Ruvents\DataReconstructor\Fixtures;

class TestClass
{
    public $property1;

    private $property2;

    protected $property3;

    public function setProperty2($property2)
    {
        $this->property2 = $property2;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}
