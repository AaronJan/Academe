<?php

namespace Academe\Contracts;

interface CastManager
{
    /**
     * @param string $field
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castIn($field, $value, $connectionType);

    /**
     * @param string $field
     * @param mixed  $value
     * @param int    $connectionType
     * @return mixed
     */
    public function castOut($field, $value, $connectionType);
}