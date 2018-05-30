<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\ElementMatch;
use Academe\Contracts\CastManager;
use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Database\MongoDB\ConditionResolver;

class ElementMatchMongoDBResolver
{
    /**
     * @param $connectionType
     * @param \Academe\Condition\ElementMatch $condition
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   ElementMatch $condition,
                                   CastManager $castManager = null)
    {
        $conditionResolver = new ConditionResolver();

        /* @var $condition Condition|ConditionGroup */
        list($field, $condition) = $condition->getParameters();

        $resolved = $conditionResolver->resolveConditionGroup($condition, $castManager);

        return [$field => ['$elemMatch' => $resolved]];
    }
}