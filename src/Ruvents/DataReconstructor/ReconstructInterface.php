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
     * @param mixed             $data
     * @param array             $map
     * @return false|void
     */
    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map);
}
