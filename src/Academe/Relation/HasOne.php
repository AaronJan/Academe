<?php

namespace Academe\Relation;

use Academe\Contracts\Academe;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Relation;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Relation\Handlers\HasOneRelationHandler;

class HasOne implements Relation
{
    /**
     * @var string
     */
    protected $childBlueprintClass;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    /**
     * @var null|\Academe\Contracts\Connection\Condition|\Academe\ConditionGroup
     */
    protected $condition;

    /**
     * HasOne constructor.
     *
     * @param $childBlueprintClass
     * @param $foreignKey
     * @param $localKey
     */
    public function __construct($childBlueprintClass, $foreignKey, $localKey)
    {
        $this->childBlueprintClass = $childBlueprintClass;
        $this->foreignKey          = $foreignKey;
        $this->localKey            = $localKey;
    }

    /**
     * @return string
     */
    public function getChildBlueprintClass()
    {
        return $this->childBlueprintClass;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * @param string  $relationName
     * @param Academe $academe
     * @return RelationHandler
     */
    public function makeHandler(Mapper $hostMapper, $relationName, Academe $academe)
    {
        return new HasOneRelationHandler($this, $hostMapper, $relationName);
    }

    /**
     * @param \Academe\Contracts\Connection\Condition|\Academe\ConditionGroup $condition
     * @return $this
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return \Academe\ConditionGroup|\Academe\Contracts\Connection\Condition|null
     */
    public function getCondition()
    {
        return $this->condition;
    }

}

