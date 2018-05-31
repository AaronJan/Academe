<?php

namespace Academe\Casting;

use Academe\Contracts\Caster;
use Academe\Contracts\CastManager as CastManagerContract;
use Academe\Support\ArrayHelper;

class CastManager implements CastManagerContract
{
    /**
     * @var Caster[]
     */
    protected $castRules;

    /**
     * CastBox constructor.
     *
     * @param array $castRules
     */
    public function __construct(array $castRules)
    {
        $this->castRules = $castRules;
    }

    /**
     * @param string $field
     * @return \Academe\Contracts\Caster|null
     */
    public function getCaster($field)
    {
        return $this->castRules[$field] ?? null;
    }

    /**
     * @param string $field
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castIn($field, $value, $connectionType)
    {
        return $this->cast('castIn', $field, $value, $connectionType);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castOut($field, $value, $connectionType)
    {
        return $this->cast('castOut', $field, $value, $connectionType);
    }

    /**
     * @param string $method
     * @param string $field
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    protected function cast($method, $field, $value, $connectionType)
    {
        if (isset($this->castRules[$field])) {
            $caster = $this->castRules[$field];

            $value = $caster->{$method}($value, $connectionType);
        }

        return $value;
    }

    /**
     * @param $attributes
     * @param $connectionType
     * @return array
     */
    public function castInAttributes($attributes, $connectionType)
    {
        return ArrayHelper::mapWithKeys(
            (array) $attributes,
            $this->makeCastAttributesCallback('castIn', $connectionType)
        );
    }

    /**
     * @param $attributes
     * @param $connectionType
     * @return array
     */
    public function castOutAttributes($attributes, $connectionType)
    {
        return ArrayHelper::mapWithKeys(
            (array) $attributes,
            $this->makeCastAttributesCallback('castOut', $connectionType)
        );
    }

    /**
     * @param $method
     * @param $connectionType
     * @return \Closure
     */
    protected function makeCastAttributesCallback($method, $connectionType)
    {
        return function ($value, $field) use ($method, $connectionType) {
            $casted = $this->{$method}($field, $value, $connectionType);

            return [$field => $casted];
        };
    }

}