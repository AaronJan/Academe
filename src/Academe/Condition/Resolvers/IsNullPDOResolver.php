<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\Equal;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class IsNullPDOResolver
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
        list($field) = $equal->getParameters();

        return [(static::wrap($field) . ' IS NULL'), []];
    }
}