<?php

namespace Ruvents\DataReconstructor;

/**
 * Interface ReconstructInterface
 * @package Ruvents\DataReconstructor
 */
interface ReconstructInterface
{
    /**
     * @param DataReconstructor $dataReconstructor
     * @param array             $data
     */
    public function reconstruct(DataReconstructor $dataReconstructor, array $data);
}
