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

}