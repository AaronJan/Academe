<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\TypeIs;
use Academe\Contracts\CastManager;

class TypeIsMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param \Academe\Condition\TypeIs           $typeIs
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   TypeIs $typeIs,
                                   CastManager $castManager = null)
    {
        list($field, $typeAlias) = $typeIs->getParameters();

        return [$field => ['$type' => $typeAlias]];
    }
}