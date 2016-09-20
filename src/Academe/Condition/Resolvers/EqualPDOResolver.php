<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\Equal;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class EqualPDOResolver
{
    use SQLValueWrapper;

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
            [(static::wrap($name) . ' = ?'), [$expect]]
        );
    }
}