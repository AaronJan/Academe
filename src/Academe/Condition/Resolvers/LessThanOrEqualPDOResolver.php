<?php

namespace Academe\Condition\Resolvers;

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
     * @return array
     */
    static public function resolve($connectionType,
                                   LessThanOrEqual $lessThanOrEqual,
                                   CastManager $castManager = null)
    {
        list($name, $value) = $lessThanOrEqual->getParameters();

        if ($castManager) {
            $value = $castManager->castIn($name, $value, $connectionType);
        }

        return [(static::wrap($name) . ' <= ?'), [$value]];
    }
}