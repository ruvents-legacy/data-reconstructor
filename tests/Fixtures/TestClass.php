<?php

namespace Ruvents\DataReconstructor\Fixtures;

class TestClass
{
    public $publicProperty;

    private $setterProperty;

    protected $magicProperty;

    public function setSetterProperty($value)
    {
        $this->setterProperty = 'setter'.$value;
    }

    public function __set($property, $value)
    {
        if ($property === 'magicProperty') {
            $this->$property = 'magic'.$value;
        }
    }
}
