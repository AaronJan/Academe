<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\Mod;

class ModMongoDBResolver
{
    /**
     * @param       $connectionType
     * @param Mod   $mod
     * @return array
     */
    static public function resolve($connectionType, Mod $mod)
    {
        list($name, $divisor, $remainder) = $mod->getParameters();

        return [$name => ['$mod' => [$divisor, $remainder]]];
    }
}