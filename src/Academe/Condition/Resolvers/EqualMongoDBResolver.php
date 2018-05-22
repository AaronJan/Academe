<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\Equal;
use Academe\Contracts\CastManager;

class EqualMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param Equal $equal
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType, Equal $equal, CastManager $castManager = null)
    {
        list($name, $expect) = $equal->getParameters();

        if ($castManager) {
            $expect = $castManager->castIn($name, $expect, $connectionType);
        }

        return [$name => ['$eq' => $expect]];
    }
}