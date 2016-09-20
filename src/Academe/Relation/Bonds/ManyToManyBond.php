<?php

namespace Academe\Relation\Bonds;

use Academe\Relation\Contracts\Bond;
use Academe\Relation\Managers\ManyToManyRelationManager;

abstract class ManyToManyBond implements Bond
{
    /**
     * @return array
     */
    public function castRules()
    {
        return [];
    }

    /**
     * @return mixed
     */
    final public function managerClass()
    {
        return ManyToManyRelationManager::class;
    }

}