<?php

namespace Ruvents\DataReconstructor;

interface DataReconstructorInterface
{
    /**
     * @param mixed  $data
     * @param string $type
     * @return mixed
     */
    public function reconstruct($data, $type);
}
