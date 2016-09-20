<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\Mod;
use Academe\Traits\SQLValueWrapper;

class ModPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param          $connectionType
     * @param Mod      $mod
     * @return CommandUnit
     */
    static public function resolve($connectionType, Mod $mod)
    {
        list($name, $divisor, $remainder) = $mod->getParameters();

        return new CommandUnit(
            $connectionType,
            [('MOD(' . static::wrap($name) . ", ?) = ?"), [$divisor, $remainder]]
        );
    }
}