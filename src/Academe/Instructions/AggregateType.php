<?php

namespace Academe\Instructions;

use Academe\Actions\Aggregate;
use Academe\Aggregation;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;
use Academe\Instructions\Traits\Lockable;

abstract class AggregateType extends BaseExecutable
{
    use Lockable;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var Connection\ConditionGroup|null
     */
    protected $conditionGroup = null;

    /**
     * SelectionType constructor.
     *
     * @param string $field
     * @param Connection\ConditionGroup|null $conditionGroup
     */
    public function __construct($field, Connection\ConditionGroup $conditionGroup = null)
    {
        $this->field = $field;

        if ($conditionGroup) {
            $this->setConditionGroup($conditionGroup);
        }
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @param $conditionGroup
     */
    protected function setConditionGroup(Connection\ConditionGroup $conditionGroup)
    {
        $this->conditionGroup = $conditionGroup;
    }

    /**
     * @param $method
     * @return \Academe\Actions\Aggregate|\Academe\Contracts\Connection\Action
     */
    protected function makeAction($method)
    {
        $action = new Aggregate($method, $this->field);

        $action->setLock($this->lockLevel);

        return $action;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param string $subject
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @param string $method
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection, $subject, Mapper $mapper, $method)
    {
        $action = $this->makeAction($method);

        $this->setLockIfNotBeenSet($action, $connection->getTransactionSelectLockLevel());

        if ($this->conditionGroup) {
            $action = $action->setConditionGroup($this->conditionGroup);
        }

        return $connection->makeBuilder()->parse($subject, $action, $mapper->getCastManager());
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @param $method
     * @return \Academe\Aggregation
     */
    protected function getAggregation(Mapper $mapper, $method)
    {
        $connection = $mapper->getConnection();
        $query = $this->makeQuery($connection, $mapper->getSubject(), $mapper, $method);
        $record = $connection->run($query);

        $castedRecord = $mapper->getCastManager()->castOutAttributes(
            $record,
            $mapper->getConnection()->getType()
        );

        return new Aggregation($castedRecord, $this->field);
    }
}