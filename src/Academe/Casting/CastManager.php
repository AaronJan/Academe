<?php

namespace Academe\Casting;

use Academe\Contracts\Caster;
use Academe\Contracts\CastManager as CastManagerContract;

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
     * @param string $attribute
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castIn($attribute, $value, $connectionType)
    {
        return $this->cast('castIn', $attribute, $value, $connectionType);
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castOut($attribute, $value, $connectionType)
    {
        return $this->cast('castOut', $attribute, $value, $connectionType);
    }

    /**
     * @param string $method
     * @param string $attribute
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    protected function cast($method, $attribute, $value, $connectionType)
    {
        if (isset($this->castRules[$attribute])) {
            $caster = $this->castRules[$attribute];

            $value = $caster->{$method}($value, $connectionType);
        }

        return $value;
    }

}