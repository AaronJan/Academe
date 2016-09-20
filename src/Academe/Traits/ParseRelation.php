<?php

namespace Academe\Traits;

trait ParseRelation
{
    /**
     * Parse a list of relations into individuals.
     *
     * @param  array $relations
     * @return array
     */
    protected function parseRelations(array $relations)
    {
        $results = [];

        foreach ($relations as $name => $constraints) {
            if (is_numeric($name)) {
                $f = function () {
                    //
                };

                list($name, $constraints) = [$constraints, $f];
            }

            // We need to separate out any nested includes. Which allows the developers
            // to load deep relationships using "dots" without stating each level of
            // the relationship with its own key in the array of eager load names.
            $results = $this->parseNested($name, $results);

            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Parse the nested relationships in a relation.
     *
     * @param  string $name
     * @param  array  $results
     * @return array
     */
    protected function parseNested($name, $results)
    {
        $progress = [];

        // If the relation has already been set on the result array, we will not set it
        // again, since that would override any constraints that were already placed
        // on the relationships. We will only set the ones that are not specified.
        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;

            if (! isset($results[$last = implode('.', $progress)])) {
                $results[$last] = function () {
                    //
                };
            }
        }

        return $results;
    }

    /**
     * @param array $relations
     * @return array
     */
    protected function groupRelations(array $relations)
    {
        ksort($relations);

        $groupedRelations = [];

        foreach ($relations as $name => $constrain) {
            //仅处理单层的Relation,获得其下属的所有Relation

            $dotPosition = strpos($name, '.');

            if ($dotPosition === false) {
                $groupedRelations[$name]['constrain'] = $constrain;
                $groupedRelations[$name]['nested']    = [];
            } else {
                $groupName = mb_substr($name, 0, $dotPosition);
                $subName   = mb_substr($name, ($dotPosition + 1));

                $groupedRelations[$groupName]['nested'][$subName] = $constrain;
            }
        }

        return $groupedRelations;
    }

}


