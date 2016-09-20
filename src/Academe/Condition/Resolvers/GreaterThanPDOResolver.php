<?php

namespace Academe\Condition\Resolvers;

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
     * @return array
     */
    static public function resolve($connectionType,
                                   GreaterThan $greaterThan,
                                   CastManager $castManager = null)
    {
        list($name, $value) = $greaterThan->getParameters();

        if ($castManager) {
            $value = $castManager->castIn($name, $value, $connectionType);
        }

        return [(static::wrap($name) . ' > ?'), [$value]];
    }
}