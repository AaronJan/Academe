<?php

namespace Academe\Contracts;

interface Caster
{
    /**
     * To database value
     *
     * @param      $value
     * @param      $connectionType
     * @return mixed
     */
    public function castIn($value, $connectionType);

    /**
     * Decode from database value
     *
     * @param      $value
     * @param      $connectionType
     * @return mixed
     */
    public function castOut($value, $connectionType);
}
