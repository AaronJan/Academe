<?php

namespace Academe\Relation\Handlers;

use Academe\Constant\TransactionConstant;
use Academe\Contracts\Academe;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\BelongsTo;
use Academe\Support\ArrayHelper;
use Academe\Model;

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
     * @param Model[] $entities
     */
    public function associate($entities)
    {
        foreach ($entities as $entity) {
            $entity->setRelation(
                $this->relationName,
                $this->getAssociatedResult(ArrayHelper::get($entity, $this->foreignKey))
            );
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
     * @param int|null                   $lockLevel
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations,
                                $lockLevel = TransactionConstant::LOCK_UNSET)
    {
        if ($this->loaded) {
            return $this;
        }

        $foreignKey = $this->foreignKey;
        $otherKey   = $this->otherKey;

        $parentKeyAttributes = array_map(function ($entity) use ($foreignKey) {
            return ArrayHelper::get($entity, $foreignKey);
        }, $entities);

        $parentMapper = $academe->getMapper($this->relation->getParentBlueprintClass());

        $fluentStatement = $this->applyConditionIfExisted(
            $this->makeLimitedFluentStatement($academe),
            $this->relation->getCondition()
        );

        $constrain($fluentStatement);

        $executable = $fluentStatement
            ->in($otherKey, $parentKeyAttributes)
            ->upgrade()
            ->setLockLevel($lockLevel)
            ->with($nestedRelations)
            ->all();

        $this->results = $parentMapper->execute($executable);
        $this->loaded  = true;

        return $this;
    }

}