<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\FieldExists;
use Academe\Contracts\CastManager;

class FieldExistsMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param \Academe\Condition\FieldExists      $fieldExists
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   FieldExists $fieldExists,
                                   CastManager $castManager = null)
    {
        list($field, $isExists) = $fieldExists->getParameters();

        return [$field => ['$exists' => $isExists]];
    }
}