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
     * @return array
     */
    public function customs()
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
     * @return string
     */
    public function pivotField()
    {
        return 'pivot';
    }
}