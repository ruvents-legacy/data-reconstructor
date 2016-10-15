<?php

namespace Ruvents\DataReconstructor\Fixtures;

class TestClass
{
    public $public;

    private $private;

    private $magic;

    public function __set($property, $value)
    {
        if ($property === 'magic') {
            $this->magic = $value;
        }
    }

    public function setPrivate($value)
    {
        $this->private = $value;
    }
}
