<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Caster as CasterContract;

abstract class BaseCaster implements CasterContract
{
    /**
     * @var array
     */
    static protected $connectionTypeToCastInMethodMap = [];

    /**
     * @var array
     */
    static protected $connectionTypeToCastOutMethodMap = [];

    /**
     * @param      $value
     * @param      $connectionType
     * @return mixed
     */
    public function castIn($value, $connectionType)
    {
        if ($value === null) {
            return $value;
        }

        $method = static::$connectionTypeToCastInMethodMap[$connectionType];

        return call_user_func_array([$this, $method], [$connectionType, $value]);
    }

    /**
     * @param      $value
     * @param      $connectionType
     * @return mixed
     */
    public function castOut($value, $connectionType)
    {
        if ($value === null) {
            return $value;
        }

        $method = static::$connectionTypeToCastOutMethodMap[$connectionType];

        return call_user_func_array([$this, $method], [$connectionType, $value]);
    }
}