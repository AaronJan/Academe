<?php

namespace Academe;

use Academe\Relation\Contracts\Bond as BondContract;

abstract class BaseBond implements BondContract
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
     * @return null|string
     */
    public function managerClass()
    {
        return null;
    }

    /**
     * @return string
     */
    public function pivotField()
    {
        return 'pivot';
    }

    /**
     * @return \Academe\Model
     */
    public function model()
    {
        return new Model();
    }

}