<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Academe;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Mapper\Blueprint;
use Academe\Entity;
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
     * @param Entity[]|mixed $entities
     * @return Entity[]|mixed
     */
    public function associate($entities)
    {
        $dictionary = $this->buildDictionary();

        $hostPrimaryKey = $this->hostBlueprint->primaryKey();

        foreach ($entities as $entity) {
            $key                         = $entity[$hostPrimaryKey];
            $entity[$this->relationName] = $this->getRelationResult($dictionary, $key);
        }

        return $entities;
    }

    /**
     * @return array
     */
    protected function buildDictionary()
    {
        $dictionary = [];

        $guestPrimaryKey = $this->guestBlueprint->primaryKey();

        foreach ($this->results as $result) {
            $dictionary[$result[$guestPrimaryKey]][] = $result;
        }

        return $dictionary;
    }

    /**
     * @param $dictionary
     * @param $key
     * @return Entity[]
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
     * @param array                      $transactions
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations,
                                array $transactions = [])
    {
        if ($this->loaded) {
            return $this;
        }

        $pivotMapper = $academe->getMapper($this->relation->getBondClass());
        $guestMapper = $academe->getMapper(get_class($this->guestBlueprint));

        $hostPrimaryKey = $this->hostBlueprint->primaryKey();

        $hostPrimaryKeyValues = array_map(function ($entity) use ($hostPrimaryKey) {
            return $entity[$hostPrimaryKey];
        }, $entities);

        // Fetch pivot entities
        $pivotMapper->involve($transactions);

        $pivotEntities = $pivotMapper->execute(
            $academe->getWriter()
                ->fresh()
                ->involve($transactions)
                ->in($this->hostField, $hostPrimaryKeyValues)
                ->all([$this->guestField])
        );

        $guestPrimaryKeyValues = array_map(function ($pivotEntity) {
            return $pivotEntity[$this->guestField];
        }, $pivotEntities);

        // Fetch guest entities
        $fluentStatement = $this->makeLimitedFluentStatement($academe)
            ->in($guestMapper->getPrimaryKey(), $guestPrimaryKeyValues);

        $constrain($fluentStatement);

        $guestMapper->involve($transactions);

        $executable = $fluentStatement->upgrade()
            ->involve($transactions)
            ->with($nestedRelations)
            ->all();

        $this->results = $guestMapper->execute($executable);
        $this->loaded  = true;

        return $this;
    }

}