<?php

namespace Ruvents\DataReconstructor\Fixtures;

use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\DataReconstructor\ReconstructableInterface;

class TestInterfaceClass implements ReconstructableInterface
{
    public $property;
    
    public function __construct(&$data, DataReconstructor $dataReconstructor, array $map)
    {
        $data['property'] = 'changed';
    }
}
