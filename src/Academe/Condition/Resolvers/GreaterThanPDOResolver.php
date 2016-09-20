<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\GreaterThan;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class GreaterThanPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param                                     $connectionType
     * @param GreaterThan                         $greaterThan
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\CommandUnit
     */
    static public function resolve($connectionType,
                                   GreaterThan $greaterThan,
                                   CastManager $castManager = null)
    {
        list($name, $value) = $greaterThan->getParameters();

        if ($castManager) {
            $value = $castManager->castIn($name, $value, $connectionType);
        }

        return new CommandUnit(
            $connectionType,
            [(static::wrap($name) . ' > ?'), [$value]]
        );
    }
}