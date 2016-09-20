<?php

namespace Academe\Database;

use Academe\Contracts\CastManager;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Exceptions\LogicException;

abstract class BaseBuilder
{
    /**
     * @param \Academe\Contracts\CastManager $castManager
     * @param array                          $attributes
     * @param                                $connectionType
     * @return array
     */
    protected function castAttributes(CastManager $castManager,
                                      array $attributes,
                                      $connectionType)
    {
        $castedAttributes = [];

        foreach ($attributes as $field => $value) {
            $castedAttributes[$field] = $castManager->castIn($field, $value, $connectionType);
        }

        return $castedAttributes;
    }

    /**
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     */
    protected function validateConditionGroup(ConditionGroup $conditionGroup)
    {
        if ($conditionGroup->getConditionCount() === 0) {
            $message = "ConditionGroup must have at least one Condition.";

            throw new LogicException($message);
        }
    }
}