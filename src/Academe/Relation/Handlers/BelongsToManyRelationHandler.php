<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Academe;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Mapper\Blueprint;
use Academe\Relation\BelongsToMany;

class BelongsToManyRelationHandler extends BaseRelationHandler
{
    /**
     * @var \Academe\Relation\BelongsToMany
     */
    protected $relation;

    /**
     * @var null|\Closure
     */
    protected $tweaker = null;

    /**
     * @var ConditionGroup|null
     */
    protected $conditionGroup = null;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var array
     */
    protected $groupedResults = [];

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var Blueprint
     */
    protected $hostBlueprint;

    /**
     * @var Blueprint
     */
    protected $guestBlueprint;

    /**
     * @var string
     */
    protected $hostField;

    /**
     * @var string
     */
    protected $guestField;

    /**
     * @var string
     */
    protected $pivotField;

    /**
     * BelongsToManyRelationHandler constructor.
     *
     * @param \Academe\Relation\BelongsToMany $relation
     * @param                                 $relationName
     */
    public function __construct(BelongsToMany $relation, $relationName)
    {
        $this->relation     = $relation;
        $this->relationName = $relationName;

        $this->setupBlueprintClasses($relation);
    }

    /**
     * @param \Academe\Relation\BelongsToMany $relation
     */
    protected function setupBlueprintClasses(BelongsToMany $relation)
    {
        $academe = $this->getAcademe();
        $bond    = $academe->getBond($this->relation->getBondClass());

        $this->pivotField = $bond->pivotField();

        if ($relation->isHost()) {
            $this->hostField      = $bond->hostKeyField();
            $this->guestField     = $bond->guestKeyField();
            $this->hostBlueprint  = $academe->getBlueprint($bond->hostBlueprintClass());
            $this->guestBlueprint = $academe->getBlueprint($bond->guestBlueprintClass());
        } else {
            $this->hostField      = $bond->guestKeyField();
            $this->guestField     = $bond->hostKeyField();
            $this->hostBlueprint  = $academe->getBlueprint($bond->guestBlueprintClass());
            $this->guestBlueprint = $academe->getBlueprint($bond->hostBlueprintClass());
        }
    }

    /**
     * @return string
     */
    public function getHostKeyField()
    {
        return $this->hostBlueprint->primaryKey();
    }

    /**
     * @param array[]|mixed $entities
     * @return array[]|mixed
     */
    public function associate($entities)
    {
        $hostPrimaryKey = $this->hostBlueprint->primaryKey();

        $dictionary = $this->buildPivotDictionary(
            $this->results,
            $this->pivotField,
            $this->hostField
        );

        foreach ($entities as $entity) {
            $key                         = $entity[$hostPrimaryKey];
            $entity[$this->relationName] = $this->getRelationResult($dictionary, $key);
        }

        return $entities;
    }

    /**
     * @return array
     */
    protected function buildPivotDictionary($entities, $pivotField, $key)
    {
        $dictionary = [];

        foreach ($entities as $entity) {
            $dictionary[$entity[$pivotField][$key]][] = $entity;
        }

        return $dictionary;
    }

    /**
     * @return array
     */
    protected function buildDictionary($entities, $key)
    {
        $dictionary = [];

        foreach ($entities as $entity) {
            $dictionary[$entity[$key]][] = $entity;
        }

        return $dictionary;
    }

    /**
     * @param $dictionary
     * @param $key
     * @return array[]
     */
    protected function getRelationResult($dictionary, $key)
    {
        return isset($dictionary[$key]) ? $dictionary[$key] : [];
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

        $pivotMapper = $academe->getMapper($this->relation->getBondClass());
        $guestMapper = $academe->getMapper(get_class($this->guestBlueprint));

        $hostPrimaryKey  = $this->hostBlueprint->primaryKey();
        $guestPrimaryKey = $guestMapper->getPrimaryKey();

        $hostPrimaryKeyValues = array_map(function ($entity) use ($hostPrimaryKey) {
            return $entity[$hostPrimaryKey];
        }, $entities);

        // Fetch pivot entities

        $pivotEntities = $pivotMapper->query()
            ->in($this->hostField, $hostPrimaryKeyValues)
            ->all();

        $guestPrimaryKeyValues = array_map(function ($pivotEntity) {
            return $pivotEntity[$this->guestField];
        }, $pivotEntities);

        // Fetch guest entities
        $statement = $this->makeLimitedFluentStatement($academe)
            ->in($guestPrimaryKey, $guestPrimaryKeyValues);

        $constrain($statement);

        $guestEntities = $guestMapper->query()
            ->loadFrom($statement)
            ->with($nestedRelations)
            ->all();

        $pivotDictionary = $this->buildDictionary($pivotEntities, $this->guestField);
        $pivotField      = $academe->getBond($this->relation->getBondClass())->pivotField();

        foreach ($guestEntities as $guestEntity) {
            $guestEntity[$pivotField] = isset($pivotDictionary[$guestEntity[$guestPrimaryKey]]) ?
                reset($pivotDictionary[$guestEntity[$guestPrimaryKey]]) :
                null;
        }

        $this->results = $guestEntities;
        $this->loaded  = true;

        return $this;
    }

}