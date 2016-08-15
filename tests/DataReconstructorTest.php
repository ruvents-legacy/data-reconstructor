<?php

namespace Ruvents\DataReconstructor;

class DataReconstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $reconstructor = new DataReconstructor();
        $className = __NAMESPACE__.'\Fixtures\TestClass';
        $data = array(
            'publicProperty' => 'publicValue',
            'setterProperty' => 'Value',
            'magicProperty' => 'Value',
            'nonexistentProperty' => 'nonexistentValue',
        );

        $object = $reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('publicValue', 'publicProperty', $object);
        $this->assertAttributeEquals('setterValue', 'setterProperty', $object);
        $this->assertAttributeEquals('magicValue', 'magicProperty', $object);
        $this->assertObjectNotHasAttribute('nonexistentProperty', $object);
    }

    public function testDateTime()
    {
        $reconstructor = new DataReconstructor();

        $date = date('c');

        $object = $reconstructor->reconstruct($date, 'DateTime');

        $this->assertEquals($date, $object->format('c'));
    }

    public function testInterface()
    {
        $reconstructor = new DataReconstructor();
        $className = __NAMESPACE__.'\Fixtures\TestInterfaceClass';
        $data = array('property' => 'value');

        $object = $reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('changed', 'property', $object);
    }

    public function testNestedClasses()
    {
        $reconstructor = new DataReconstructor(array('map' => array(
            __NAMESPACE__.'\Fixtures\TestClassLevel1' => array(
                'level2' => __NAMESPACE__.'\Fixtures\TestClassLevel2',
            ),
            __NAMESPACE__.'\Fixtures\TestClassLevel2' => array(
                'level2' => __NAMESPACE__.'\Fixtures\TestClassLevel3[]',
            ),
        )));

        $object = $reconstructor->reconstruct(array(
            'level2' => array(
                'level3' => array(
                    array('property' => 0),
                    array('property' => 1),
                ),
            ),
        ), __NAMESPACE__.'\Fixtures\TestClassLevel1');

        $this->assertEquals(0, $object->level2->level3[0]['property']);
        $this->assertEquals(1, $object->level2->level3[1]['property']);
    }
}
