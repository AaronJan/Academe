<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\LessThan;
use Academe\Contracts\CastManager;

class LessThanMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param LessThan                            $lessThan
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   LessThan $lessThan,
                                   CastManager $castManager = null)
    {
        list($name, $value) = $lessThan->getParameters();

        if ($castManager) {
            $value = $castManager->castIn($name, $value, $connectionType);
        }

        return [$name => ['$lt' => $value]];
    }
}