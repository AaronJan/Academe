<?php

namespace Academe\Database\MongoDB;

use Academe\Contracts\Connection\ConditionGroup;
use Academe\Constant\ConnectionConstant;
use Academe\Contracts\CastManager;
use Academe\Support\ArrayHelper;

class ConditionResolver
{
    /**
     * @param ConditionGroup|null $conditionGroup
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function resolveConditionGroup(ConditionGroup $conditionGroup = null,
                                          CastManager $castManager = null
    )
    {
        if (
            $conditionGroup === null ||
            $conditionGroup->getConditionCount() === 0
        ) {
            return [];
        }

        // un-nest redundant ConditionGroup
        if ($this->isRedundantConditionGroup($conditionGroup)) {
            return $this->resolveConditionGroup(ArrayHelper::first($conditionGroup->getConditions()), $castManager);
        }

        $useNested  = $conditionGroup->getConditionCount() > 1;
        $subQueries = [];

        foreach ($conditionGroup->getConditions() as $condition) {
            if ($condition instanceof ConditionGroup) {
                $child = $this->resolveConditionGroup($condition, $castManager);
                if (! empty($child)) {
                    $subQueries[] = $child;
                }
            } else {
                $subQueries[] = $condition->parse(ConnectionConstant::TYPE_MONGODB, $castManager);
            }
        }

        if ($conditionGroup->isStrict()) {
            return $useNested ?
                ['$and' => $subQueries] :
                static::collapseQueries($subQueries);
        } else {
            return ['$or' => $subQueries];
        }
    }

    /**
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return bool
     */
    protected function isRedundantConditionGroup(ConditionGroup $conditionGroup)
    {
        if ($conditionGroup->getConditionCount() != 1) {
            return false;
        }

        $first = ArrayHelper::first($conditionGroup->getConditions());

        return $first instanceof ConditionGroup;
    }

    /**
     * @param array $queries
     * @return array mixed
     */
    static public function collapseQueries(array $queries)
    {
        return array_reduce($queries, function ($collapsed, $query) {
            reset($query);
            $field    = key($query);
            $criteria = $query[$field];

            $collapsed[$field] = $criteria;

            return $collapsed;
        }, []);
    }

}