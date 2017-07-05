<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\IsNull;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class IsNullPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param                                     $connectionType
     * @param IsNull                              $isNull
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType, IsNull $isNull, CastManager $castManager = null)
    {
        list($field) = $isNull->getParameters();

        return [(static::wrap($field) . ' IS NULL'), []];
    }
}