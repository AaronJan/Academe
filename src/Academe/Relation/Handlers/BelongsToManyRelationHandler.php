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
    protected $pivotResults = [];

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
     * @param array[]|mixed $entities
     * @return array[]|mixed
     */
    public function associate($entities)
    {
        $dictionary      = $this->buildPivotedDictionary(
            $this->pivotResults,
            $this->results,
            $this->hostField,
            $this->guestField,
            $this->guestBlueprint->primaryKey()
        );
        $pivotDictionary = $this->buildPivotDictionary($this->pivotResults, $this->hostField, $this->guestField);

        $entities = $this->attachRelations($entities, $dictionary, $pivotDictionary);

        return $entities;
    }

    /**
     * @param array $hostEntities
     * @param       $guestDictionary
     * @param       $pivotDictionary
     * @return array
     */
    protected function attachRelations(array $hostEntities, $guestDictionary, $pivotDictionary)
    {
        $relationName   = $this->relationName;
        $hostPrimaryKey = $this->hostBlueprint->primaryKey();

        return array_map(function ($entity) use (
            $hostPrimaryKey,
            $guestDictionary,
            $pivotDictionary,
            $relationName
        ) {
            $hostPrimaryValue = $entity[$hostPrimaryKey];

            $entity[$relationName] = $this->attachPivotEntityToRelation(
                $this->cloneEntities(
                    $this->getRelationResult($guestDictionary, $hostPrimaryValue)
                ),
                $pivotDictionary,
                $hostPrimaryValue
            );

            return $entity;
        }, $hostEntities);
    }

    /**
     * @param array $entities
     * @return array
     */
    protected function cloneEntities(array $entities)
    {
        return array_map(function ($entity) {
            return clone $entity;
        }, $entities);
    }

    /**
     * @param array $relationEntities
     * @param       $pivotDictionary
     * @param       $hostPrimaryValue
     * @return array
     */
    protected function attachPivotEntityToRelation(array $relationEntities,
                                                   $pivotDictionary,
                                                   $hostPrimaryValue)
    {
        $pivotField      = $this->pivotField;
        $guestPrimaryKey = $this->guestBlueprint->primaryKey();

        return array_map(function ($relationEntity) use (
            $pivotDictionary,
            $pivotField,
            $hostPrimaryValue,
            $guestPrimaryKey
        ) {
            $relationEntity[$pivotField] = $this->getRelationResult(
                $pivotDictionary,
                $this->getPivotDictionaryKey($hostPrimaryValue, $relationEntity[$guestPrimaryKey])
            );

            return $relationEntity;
        }, $relationEntities);
    }

    /**
     * @param array $pivotEntities
     * @param array $entities
     * @param       $hostField
     * @param       $guestField
     * @param       $guestPrimaryKey
     * @return array
     */
    protected function buildPivotedDictionary(
        array $pivotEntities,
        array $entities,
        $hostField,
        $guestField,
        $guestPrimaryKey)
    {
        $dictionary         = [];
        $pivotMapDictionary = $this->buildDictionary($pivotEntities, $guestField);


        foreach ($entities as $entity) {
            $entityPrimaryValue = $entity[$guestPrimaryKey];

            $maps = $pivotMapDictionary[$entityPrimaryValue] ?? [];

            foreach ($maps as $map) {
                $dictionary[$map[$hostField]][] = $entity;
            }
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

        $guestPrimaryKeyValues = array_unique(
            array_map(function ($pivotEntity) {
                return $pivotEntity[$this->guestField];
            }, $pivotEntities)
        );

        // Fetch guest entities
        $statement = $this->makeLimitedFluentStatement($academe)
            ->in($guestPrimaryKey, $guestPrimaryKeyValues);

        $constrain($statement);

        $guestEntities = $guestMapper->query()
            ->loadFrom($statement)
            ->with($nestedRelations)
            ->all();

        $this->results      = $guestEntities;
        $this->pivotResults = $pivotEntities;
        $this->loaded       = true;

        return $this;
    }

    /**
     * @param $hostPrimaryValue
     * @param $guestPrimaryValue
     * @return string
     */
    protected function getPivotDictionaryKey($hostPrimaryValue, $guestPrimaryValue)
    {
        return "{$hostPrimaryValue}_{$guestPrimaryValue}";
    }

    /**
     * @param array $pivotEntities
     * @param       $hostField
     * @param       $guestField
     * @return array
     */
    protected function buildPivotDictionary(array $pivotEntities, $hostField, $guestField)
    {
        $dictionary = [];

        foreach ($pivotEntities as $pivotEntity) {
            $key              = $this->getPivotDictionaryKey(
                $pivotEntity[$hostField],
                $pivotEntity[$guestField]
            );
            $dictionary[$key] = $pivotEntity;
        }

        return $dictionary;
    }

}