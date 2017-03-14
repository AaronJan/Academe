<?php

namespace Academe\Support;

class ArrayHelper
{
    /**
     * From Laravel Collection.
     *
     * @param array    $array
     * @param callable $callback
     * @return array
     */
    static public function map(array $array, callable $callback)
    {
        return array_map($callback, $array, array_keys($array));
    }

    /**
     * From Laravel Collection.
     *
     * @param array    $array
     * @param callable $callback
     * @return array
     */
    static public function mapWithKeys(array $array, callable $callback)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

}