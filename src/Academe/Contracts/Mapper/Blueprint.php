<?php

namespace Academe\Contracts\Mapper;

use Academe\Academe;

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
     * @return null|mixed
     */
    public function generatePrimaryKey();

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
     * @return null|string
     */
    public function mapperClass();

    /**
     * @return \Academe\Model;
     */
    public function model();

}
