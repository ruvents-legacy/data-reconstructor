<?php

namespace Ruvents\DataReconstructor;

/**
 * Interface ReconstructInterface
 * @package Ruvents\DataReconstructor
 */
interface ReconstructInterface
{
    /**
     * @param mixed             $data
     * @param DataReconstructor $dataReconstructor
     */
    public function reconstruct($data, DataReconstructor $dataReconstructor);
}
