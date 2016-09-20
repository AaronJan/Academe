<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\NotIn;
use Academe\Contracts\CastManager;

class NotInMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param NotIn                               $notIn
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   NotIn $notIn,
                                   CastManager $castManager = null)
    {
        list($name, $values) = $notIn->getParameters();

        if ($castManager) {
            $values = array_map(function ($value) use ($name, $connectionType, $castManager) {
                return $castManager->castIn($name, $value, $connectionType);
            }, $values);
        }

        return [$name => ['$nin' => $values]];
    }
}