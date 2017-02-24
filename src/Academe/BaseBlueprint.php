<?php

namespace Academe;

use Academe\Contracts\Mapper\Blueprint as BlueprintContract;

abstract class BaseBlueprint implements BlueprintContract
{
    /**
     * @return string|null
     */
    public function connectionName()
    {
        return null;
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [];
    }

    /**
     * @return null|string
     */
    public function mapperClass()
    {
        return null;
    }

    /**
     * @return \Academe\Model
     */
    public function model()
    {
        return new Model();
    }

}