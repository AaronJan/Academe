<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\IsNull;
use Academe\Contracts\CastManager;

class IsNullMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param IsNull                              $isNull
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType, IsNull $isNull, CastManager $castManager = null)
    {
        list($name) = $isNull->getParameters();

        return [$name => null]; // `$eq` needs MongoDB >= `3`
    }
}