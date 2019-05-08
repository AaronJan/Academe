<?php

namespace Academe\Traits;

use Academe\Contracts\Raw;

trait SQLValueWrapper
{
    /**
     * @param string|Raw $value
     * @return string
     */
    static protected function wrap($value)
    {
        if ($value instanceof Raw) {
            return $value->getRaw();
        }

        if ($value === '*') {
            return $value;
        }

        return '`' . str_replace('`', '', $value) . '`';
    }
}