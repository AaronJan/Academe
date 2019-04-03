<?php

namespace Academe\Database\Traits;

trait GroupParsingHelper
{
    /**
     * @param array $aggregation
     * @return array
     */
    protected function normalizeAggregationArray(array $aggregation)
    {
        return array_reduce(
            array_keys($aggregation),
            function ($carry, $key) use ($aggregation) {
                $fieldName = is_numeric($key) ? $aggregation[$key] : $key;
                $carry[$fieldName] = $aggregation[$key];

                return $carry;
            },
            []
        );
    }
}

