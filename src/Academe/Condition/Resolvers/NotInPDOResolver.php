<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\NotIn;
use Academe\Contracts\CastManager;
use Academe\Traits\SQLValueWrapper;

class NotInPDOResolver
{
    use SQLValueWrapper;

    /**
     * @param                                     $connectionType
     * @param NotIn                               $notIn
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\CommandUnit
     */
    static public function resolve($connectionType,
                                   NotIn $notIn,
                                   CastManager $castManager = null)
    {
        list($name, $values) = $notIn->getParameters();

        if (empty($values)) {
            return new CommandUnit(
                $connectionType,
                ["1 = 1", []]
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
            [(static::wrap($name) . " NOT IN ({$valueHolder})"), $values]
        );
    }
}