<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\GreaterThanOrEqual;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class GreaterThanOrEqualPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param                                     $connectionType
     * @param GreaterThanOrEqual                  $greaterThanOrEqual
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   GreaterThanOrEqual $greaterThanOrEqual,
                                   CastManager $castManager = null)
    {
        list($name, $value) = $greaterThanOrEqual->getParameters();

        if ($castManager) {
            $value = $castManager->castIn($name, $value, $connectionType);
        }

        return [(static::wrap($name) . ' >= ?'), [$value]];
    }
}