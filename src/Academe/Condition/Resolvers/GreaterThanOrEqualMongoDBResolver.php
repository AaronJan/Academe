<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\GreaterThanOrEqual;
use Academe\Contracts\CastManager;

class GreaterThanOrEqualMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param GreaterThanOrEqual                  $greaterThanOrEqual
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\CommandUnit
     */
    static public function resolve($connectionType,
                                   GreaterThanOrEqual $greaterThanOrEqual,
                                   CastManager $castManager = null)
    {
        list($name, $value) = $greaterThanOrEqual->getParameters();

        if ($castManager) {
            $value = $castManager->castIn($name, $value, $connectionType);
        }

        return new CommandUnit(
            $connectionType,
            [$name => ['$gte' => $value]]
        );
    }
}