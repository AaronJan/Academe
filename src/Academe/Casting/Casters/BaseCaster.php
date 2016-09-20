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
     * @var array
     */
    protected $options = [];

    /**
     * StringCaster constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param      $value
     * @param      $connectionType
     * @return mixed
     */
    public function castIn($value, $connectionType)
    {
        $method = static::$connectionTypeToCastInMethodMap[$connectionType];

        return call_user_func_array([$this, $method], [$value]);
    }

    /**
     * @param      $value
     * @param      $connectionType
     * @return mixed
     */
    public function castOut($value, $connectionType)
    {
        $method = static::$connectionTypeToCastOutMethodMap[$connectionType];

        return call_user_func_array([$this, $method], [$value]);
    }
}