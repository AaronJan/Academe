<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\In;
use Academe\Contracts\CastManager;

class InMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param In                                  $in
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   In $in,
                                   CastManager $castManager = null)
    {
        list($name, $values) = $in->getParameters();

        if ($castManager) {
            $values = array_map(function ($value) use ($name, $connectionType, $castManager) {
                return $castManager->castIn($name, $value, $connectionType);
            }, $values);
        }

        return [$name => ['$in' => $values]];
    }
}