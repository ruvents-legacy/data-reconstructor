<?php

namespace Ruvents\DataReconstructor\Fixtures;

class TestClass
{
    /**
     * @var \Ruvents\DataReconstructor\Fixtures\TestClass2
     */
    protected $class2;
    
    /**
     * @var self[]
     */
    public $class1;

    public function setClass2(TestClass2 $class2)
    {
        $this->class2 = $class2;

        return $this;
    }
}
