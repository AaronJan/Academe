<?php

namespace Academe\Contracts;

interface CastManager
{
    /**
     * @param string $attribute
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castIn($attribute, $value, $connectionType);

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castOut($attribute, $value, $connectionType);
}