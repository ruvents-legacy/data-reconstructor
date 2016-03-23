<?php

namespace Ruvents\DataReconstructor\Fixtures;

use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\DataReconstructor\ReconstructInterface;

/**
 * Class TestClass3
 * @package Ruvents\DataReconstructor\Fixtures
 */
class TestClass3 implements ReconstructInterface
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array
     */
    private $map;

    /**
     * @inheritdoc
     */
    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map)
    {
        $this->data = $data;
        $this->map = $map;

        return false;
    }
}
