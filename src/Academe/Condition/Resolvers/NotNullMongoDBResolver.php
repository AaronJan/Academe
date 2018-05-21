<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\NotNull;
use Academe\Contracts\CastManager;

class NotNullMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param NotNull $notNull
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType, NotNull $notNull, CastManager $castManager = null)
    {
        list($field) = $notNull->getParameters();

        return [$field => ['$ne' => null]];
    }
}