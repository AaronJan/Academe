<?php

namespace Academe\Support;

use Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer;

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

    /**
     * Modified from Laravel's.
     *
     * @param $array
     * @param $depth
     * @return mixed
     */
    static public function flatten($array, $depth = \INF)
    {
        return array_reduce($array, function ($result, $item) use ($depth) {
            if (! is_array($item)) {
                return array_merge($result, [$item]);
            } elseif ($depth === 1) {
                return array_merge($result, array_values($item));
            } else {
                return array_merge($result, static::flatten($item, ($depth - 1)));
            }
        }, []);
    }

}