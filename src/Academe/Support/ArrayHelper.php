<?php

namespace Academe\Support;

use Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer;

class ArrayHelper
{
    /**
     * @param $value
     * @return bool
     */
    static public function accessible($value)
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    /**
     * @param $array
     * @param $key
     * @return bool
     */
    static public function exists($array, $key)
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * @param array|\ArrayAccess $array
     * @param                    $key
     * @param null $default
     * @return array|mixed|null
     */
    static public function get($array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * From Laravel Collection.
     *
     * @param array $array
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
     * @param array $array
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

    /**
     * @param array $array
     * @return mixed
     */
    public static function first($array)
    {
        foreach ($array as $each) {
            return $each;
        }

        return null;
    }

}