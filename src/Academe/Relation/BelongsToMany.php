<?php

namespace Academe\Relation;

use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Relation;
use Academe\Contracts\Academe;
use Academe\Relation\Handlers\BelongsToManyRelationHandler;

class BelongsToMany implements Relation
{
    /**
     * @var string
     */
    protected $bondClass;

    /**
     * @var bool
     */
    protected $isHost;

    /**
     * @var null|\Academe\Contracts\Connection\Condition|\Academe\ConditionGroup
     */
    protected $pivotCondition;

    /**
     * @var null|\Academe\Contracts\Connection\Condition|\Academe\ConditionGroup
     */
    protected $guestCondition;

    /**
     * BelongsToMany constructor.
     *
     * @param      $bondClass
     * @param bool $isHost
     */
    public function __construct($bondClass, $isHost)
    {
        $this->bondClass = $bondClass;
        $this->isHost    = $isHost;
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param string                           $relationName
     * @param Academe                          $academe
     * @return \Academe\Relation\Contracts\RelationHandler
     */
    public function makeHandler(Mapper $hostMapper, $relationName, Academe $academe)
    {
        return new BelongsToManyRelationHandler($this, $relationName);
    }

    /**
     * @return string
     */
    public function getBondClass()
    {
        return $this->bondClass;
    }

    /**
     * @return bool
     */
    public function isHost()
    {
        return $this->isHost;
    }

    /**
     * @param \Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\Condition $condition
     * @return $this
     */
    public function setPivotCondition($condition)
    {
        $this->pivotCondition = $condition;

        return $this;
    }

    /**
     * @return \Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\Condition|null
     */
    public function getPivotCondition()
    {
        return $this->pivotCondition;
    }

    /**
     * @param \Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\Condition $condition
     * @return $this
     */
    public function setGuestCondition($condition)
    {
        $this->guestCondition = $condition;

        return $this;
    }

    /**
     * @return \Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\Condition|null
     */
    public function getGuestCondition()
    {
        return $this->guestCondition;
    }

}

