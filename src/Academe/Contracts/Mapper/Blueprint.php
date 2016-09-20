<?php

namespace Academe\Contracts\Mapper;

interface Blueprint
{
    /**
     * @return string|null
     */
    public function connectionName();

    /**
     * @return string
     */
    public function primaryKey();

    /**
     * @return string
     */
    public function subject();

    /**
     * @return array
     */
    public function relations();

    /**
     * @return array
     */
    public function castRules();

    /**
     * @return array
     */
    public function customs();

    /**
     * @return null|string
     */
    public function mapperClass();

}
