<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
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
     * @return \Academe\CommandUnit
     */
    static public function resolve($connectionType,
                                   In $in,
                                   CastManager $castManager = null)
    {
        list($name, $values) = $in->getParameters();

        if (empty($values)) {
            return new CommandUnit(
                $connectionType,
                ["0 = 1", []]
            );
        }

        $valueHolder = implode(', ', array_pad([], count($values), '?'));

        if ($castManager) {
            $values = array_map(function ($value) use ($name, $connectionType, $castManager) {
                return $castManager->castIn($name, $value, $connectionType);
            }, $values);
        }

        return new CommandUnit(
            $connectionType,
            [(static::wrap($name) . " IN ({$valueHolder})"), $values]
        );
    }
}