<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\In;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class InPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param                                     $connectionType
     * @param In                                  $in
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   In $in,
                                   CastManager $castManager = null)
    {
        list($name, $values) = $in->getParameters();

        if (empty($values)) {
            return ["0 = 1", []];
        }

        $valueHolder = implode(', ', array_pad([], count($values), '?'));

        if ($castManager) {
            $values = array_map(function ($value) use ($name, $connectionType, $castManager) {
                return $castManager->castIn($name, $value, $connectionType);
            }, $values);
        }

        return [(static::wrap($name) . " IN ({$valueHolder})"), $values];
    }
}