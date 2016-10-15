<?php

namespace Ruvents\DataReconstructor\Helper;

use ReflectionClass;

class AccessorHelperTest extends \PHPUnit_Framework_TestCase
{
    private $accessorHelper;

    public function setUp()
    {
        $this->accessorHelper = $this->getObjectForTrait('Ruvents\\DataReconstructor\\Helper\\AccessorHelper');
    }

    public function testIsTypeStrict()
    {
        $this->assertFalse($this->callMethod($this->accessorHelper, 'isAccessorMethod', ['method']));
        $this->assertTrue($this->callMethod($this->accessorHelper, 'isAccessorMethod', ['method()']));
    }

    protected function callMethod($obj, $name, array $args)
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
