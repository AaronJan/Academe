<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\Equal;
use Academe\Contracts\CastManager;

class EqualMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param Equal                               $equal
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\CommandUnit
     */
    static public function resolve($connectionType, Equal $equal, CastManager $castManager = null)
    {
        list($name, $expect) = $equal->getParameters();

        if ($castManager) {
            $expect = $castManager->castIn($name, $expect, $connectionType);
        }

        return new CommandUnit(
            $connectionType,
            [$name => $expect] // $eq指定，需要MongoDB > 3
        );
    }
}