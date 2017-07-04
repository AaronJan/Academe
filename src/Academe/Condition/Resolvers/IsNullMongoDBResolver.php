<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\Equal;
use Academe\Contracts\CastManager;

class IsNullMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param Equal                               $equal
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType, Equal $equal, CastManager $castManager = null)
    {
        list($name) = $equal->getParameters();

        return [$name => null]; // `$eq` needs MongoDB >= `3`
    }
}