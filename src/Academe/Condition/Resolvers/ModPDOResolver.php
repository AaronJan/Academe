<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\Mod;
use Academe\Traits\SQLValueWrapper;

class ModPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param          $connectionType
     * @param Mod      $mod
     * @return array
     */
    static public function resolve($connectionType, Mod $mod)
    {
        list($name, $divisor, $remainder) = $mod->getParameters();

        return [('MOD(' . static::wrap($name) . ", ?) = ?"), [$divisor, $remainder]];
    }
}