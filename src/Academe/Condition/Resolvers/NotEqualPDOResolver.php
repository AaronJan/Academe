<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\NotEqual;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class NotEqualPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param                                     $connectionType
     * @param NotEqual                            $notEqual
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   NotEqual $notEqual,
                                   CastManager $castManager = null)
    {
        list($name, $notExpect) = $notEqual->getParameters();

        if ($castManager) {
            $notExpect = $castManager->castIn($name, $notExpect, $connectionType);
        }

        return [(static::wrap($name) . ' != ?'), [$notExpect]];
    }
}