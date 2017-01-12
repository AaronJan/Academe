<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\ContainsAll;
use Academe\Contracts\CastManager;

class ContainsAllMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param ContainsAll                         $containsAll
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   ContainsAll $containsAll,
                                   CastManager $castManager = null)
    {
        list($field, $values) = $containsAll->getParameters();

        if (empty($values)) {
            return ["0 = 1", []];
        }

        if ($castManager) {
            $values = array_map(function ($value) use ($field, $connectionType, $castManager) {
                return $castManager->castIn($field, $value, $connectionType);
            }, $values);
        }

        return [$field => ['$all' => $values]];
    }
}