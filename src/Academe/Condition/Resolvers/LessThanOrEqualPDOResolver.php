<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\LessThanOrEqual;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class LessThanOrEqualPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param                            $connectionType
     * @param LessThanOrEqual            $lessThanOrEqual
     * @param \Academe\Contracts\CastManager $castManager
     * @return \Academe\CommandUnit
     */
    static public function resolve($connectionType,
                                   LessThanOrEqual $lessThanOrEqual,
                                   CastManager $castManager = null)
    {
        list($name, $value) = $lessThanOrEqual->getParameters();

        if ($castManager) {
            $value = $castManager->castIn($name, $value, $connectionType);
        }

        return new CommandUnit(
            $connectionType,
            [(static::wrap($name) . ' <= ?'), [$value]]
        );
    }
}