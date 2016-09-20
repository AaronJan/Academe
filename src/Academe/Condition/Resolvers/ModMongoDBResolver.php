<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\Mod;

class ModMongoDBResolver
{
    /**
     * @param       $connectionType
     * @param Mod   $mod
     * @return CommandUnit
     */
    static public function resolve($connectionType, Mod $mod)
    {
        list($name, $divisor, $remainder) = $mod->getParameters();

        return new CommandUnit(
            $connectionType,
            [$name => ['$mod' => [$divisor, $remainder]]]
        );
    }
}