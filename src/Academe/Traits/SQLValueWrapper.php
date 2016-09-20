<?php

namespace Academe\Traits;

trait SQLValueWrapper
{
    /**
     * @param string $value
     * @return string
     */
    static protected function wrap($value)
    {
        if ($value === '*') {
            return $value;
        }

        return '`' . str_replace('`', '', $value) . '`';
    }
}