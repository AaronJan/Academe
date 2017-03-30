<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Academe;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\BelongsTo;

class BelongsToRelationHandler extends BaseRelationHandler
{
    /**
     * @var BelongsTo
     */
    protected $relation;

    /**
     * @var ConditionGroup|null
     */
    protected $conditionGroup = null;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var array
     */
    protected $groupedResults = [];

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $otherKey;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * BelongsToRelationHandler constructor.
     *
     * @param BelongsTo                        $relation
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param string                           $relationName
     */
    public function __construct(BelongsTo $relation, Mapper $hostMapper, $relationName)
    {
        $this->relation     = $relation;
        $this->hostMapper   = $hostMapper;
        $this->relationName = $relationName;
        $this->foreignKey   = $relation->getForeignKey();
        $this->otherKey     = $relation->getOtherKey();
    }

    /**
     * @param array[]|mixed $entities
     */
    public function associate($entities)
    {
        foreach ($entities as $entity) {
            $entity[$this->relationName] = $this->getAssociatedResult($entity[$this->foreignKey]);
        }
    }

    /**
     * @param $parentKeyField
     * @return array
     */
    protected function getAssociatedResult($parentKeyField)
    {
        if (! isset($this->groupedResults[$parentKeyField])) {
            $matchedResult = null;
            $otherKey      = $this->otherKey;

            foreach ($this->results as $result) {
                // Type must be exactlly matched,
                // You may need to cast the attribute.
                if ($result[$otherKey] === $parentKeyField) {
                    $matchedResult = $result;
                    break;
                }
            }

            $this->groupedResults[$parentKeyField] = $matchedResult;
        }

        return $this->groupedResults[$parentKeyField];
    }

    /**
     * @param                            $entities
     * @param \Closure                   $constrain
     * @param \Academe\Contracts\Academe $academe
     * @param array                      $nestedRelations
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations)
    {
        if ($this->loaded) {
            return $this;
        }

        $foreignKey = $this->foreignKey;
        $otherKey   = $this->otherKey;

        $parentKeyAttributes = array_map(function ($entity) use ($foreignKey) {
            return $entity[$foreignKey];
        }, $entities);

        $parentMapper = $academe->getMapper($this->relation->getParentBlueprintClass());

        $fluentStatement = $this->makeLimitedFluentStatement($academe)->in($otherKey, $parentKeyAttributes);

        $constrain($fluentStatement);

        $executable = $fluentStatement
            ->upgrade()
            ->with($nestedRelations)
            ->all();

        $this->results = $parentMapper->execute($executable);
        $this->loaded  = true;

        return $this;
    }

}