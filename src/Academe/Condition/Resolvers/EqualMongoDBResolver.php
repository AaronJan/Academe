<?php

namespace Academe\Condition\Resolvers;

use Academe\Casting\Casters\GroupCaster;
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

        $fieldCaster = $castManager === null ? null : $castManager->getCaster($name);
        if ($fieldCaster === null) {
            return [$name => ['$eq' => $expect]];
        }

        return [
            $name => [
                '$eq' => $fieldCaster instanceof GroupCaster ?
                    $fieldCaster->getCaster()->castIn($expect, $connectionType) :
                    $fieldCaster->castIn($expect, $connectionType)
            ]
        ];
    }
}