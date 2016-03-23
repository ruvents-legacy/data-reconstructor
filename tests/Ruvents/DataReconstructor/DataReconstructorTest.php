<?php

namespace Ruvents\DataReconstructor;

use Symfony\Component\Yaml\Yaml;

class DataReconstructorTest extends \PHPUnit_Framework_TestCase
{
    private function createReconstructor($configName = 'config')
    {
        $configContents = file_get_contents(__DIR__."/Fixtures/$configName.yml");
        $config = Yaml::parse($configContents);

        return new DataReconstructor($config);
    }

    public function testSimpleClass()
    {
        $className = 'Ruvents\DataReconstructor\Fixtures\TestClass';
        $data = [
            'property1' => 'value1',
            'property2' => 'value2',
            'property3' => 'value3',
        ];

        $object = $this->createReconstructor()->reconstruct($data, $className);

        for ($i = 1; $i <= 3; $i++) {
            $this->assertAttributeEquals($data["property$i"], "property$i", $object);
        }
    }
}
