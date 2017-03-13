<?php

namespace Academe\Support;

class ArrayHelper
{
    /**
     * @param array    $array
     * @param callable $callback
     * @return array
     */
    static public function map($array, callable $callback)
    {
        return array_map($callback, $array, array_keys($array));
    }
}