<?php

namespace Ruvents\DataReconstructor\Helper;

trait AccessorHelper
{
    /**
     * @param string $accessor
     * @return bool
     */
    private function isAccessorMethod($accessor)
    {
        return substr($accessor, -2) === '()';
    }
}
