<?php

namespace Academe\Condition\Resolvers;

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
     * @return array
     */
    static public function resolve($connectionType, Equal $equal, CastManager $castManager = null)
    {
        list($name, $expect) = $equal->getParameters();

        if ($castManager) {
            $expect = $castManager->castIn($name, $expect, $connectionType);
        }

        if ($expect === null) {
            return [(static::wrap($name) . ' IS NULL'), []];
        }

        return [(static::wrap($name) . ' = ?'), [$expect]];
    }
}