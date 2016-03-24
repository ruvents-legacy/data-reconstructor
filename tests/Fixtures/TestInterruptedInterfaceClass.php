<?php

namespace Ruvents\DataReconstructor\Fixtures;

use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\DataReconstructor\ReconstructInterface;

class TestInterruptedInterfaceClass implements ReconstructInterface
{
    public $property;
    
    public $emptyProperty;

    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map)
    {
        $this->property = $data['property'];

        return false;
    }
}
