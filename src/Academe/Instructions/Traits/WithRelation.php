<?php

namespace Academe\Instructions\Traits;

use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Relation;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Traits\ParseRelation;

trait WithRelation
{
    use ParseRelation;

    /**
     * @var RelationHandler[]
     */
    protected $relations = [];

    /**
     * @param                   $entities
     * @param RelationHandler[] $loadedRelations
     */
    protected function associateRelations($entities, $loadedRelations)
    {
        foreach ($loadedRelations as $relation) {
            $relation->associate($entities);
        }
    }

    /**
     * @param                                  $entities
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @return array
     */
    protected function getLoadedRelations($entities,
                                          Mapper $hostMapper)
    {
        $loadedRelations = [];

        $groupedRelations = $this->groupRelations($this->relations);

        foreach ($groupedRelations as $relationName => $detail) {
            $constrain = $detail['constrain'];
            $nested    = $detail['nested'];

            $loadedRelations[] = $hostMapper->relation($relationName)->loadResults(
                $entities,
                $constrain,
                $hostMapper->getAcademe(),
                $nested
            );
        }

        return $loadedRelations;
    }

    /**
     * @param Relation|array $relations
     * @return $this
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $eagers = $this->parseRelations($relations);

        $this->relations = array_merge($this->relations, $eagers);

        return $this;
    }
}