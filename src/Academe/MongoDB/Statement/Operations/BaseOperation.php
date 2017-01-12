<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;
use Academe\MongoDB\Contracts\Operation;

abstract class BaseOperation implements Operation
{
    /**
     * @param                                $field
     * @param                                $values
     * @param                                $connectionType
     * @param \Academe\Contracts\CastManager $castManager
     * @return array
     */
    protected function castEachValue($field, $values, $connectionType, CastManager $castManager)
    {
        return array_map(function ($eachValue) use ($field, $connectionType, $castManager) {
            return $castManager->castIn($field, $eachValue, $connectionType);
        }, $values);
    }
}