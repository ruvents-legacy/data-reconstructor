<?php

namespace Ruvents\DataReconstructor;

class DataReconstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoOptions()
    {
        $reconstructor = new DataReconstructor();

        $className = 'Ruvents\DataReconstructor\Fixtures\TestClass';
        $data = [
            'publicProperty' => 'publicValue',
            'setterProperty' => 'Value',
            'magicProperty' => 'Value',
            'nonexistentProperty' => 'nonexistentValue',
        ];

        $object = $reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('publicValue', 'publicProperty', $object);
        $this->assertAttributeEquals('setterValue', 'setterProperty', $object);
        $this->assertAttributeEquals('magicValue', 'magicProperty', $object);
        $this->assertObjectNotHasAttribute('nonexistentProperty', $object);
    }
}
