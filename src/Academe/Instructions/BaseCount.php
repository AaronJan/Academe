<?php

namespace Academe\Instructions;

use Academe\Actions\Aggregate;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;
use Academe\Instructions\Traits\Lockable;
use Academe\Contracts\Mapper\Instructions\Count as CountContract;

class BaseCount extends BaseExecutable implements CountContract
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
     * Count constructor.
     *
     * @param string                         $field
     * @param Connection\ConditionGroup|null $conditionGroup
     */
    public function __construct($field = '*',
                                Connection\ConditionGroup $conditionGroup = null)
    {
        $this->field = $field;

        if ($conditionGroup) {
            $this->setConditionGroup($conditionGroup);
        }
    }

    /**
     * @param $conditionGroup
     */
    protected function setConditionGroup(Connection\ConditionGroup $conditionGroup)
    {
        $this->conditionGroup = $conditionGroup;
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $connection = $mapper->getConnection();
        $query      = $this->makeQuery($connection, $mapper);

        $count = $connection->run($query);

        return $count;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param \Academe\Contracts\Mapper\Mapper         $mapper
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection, Mapper $mapper)
    {
        $action = $this->makeCountAggregateAction();

        $this->setLockIfNotBeenSet($action, $connection->getTransactionSelectLockLevel());

        if ($this->conditionGroup) {
            $action = $action->setConditionGroup($this->conditionGroup);
        }

        return $connection->makeBuilder()
            ->parse($mapper->getSubject(), $action, $mapper->getCastManager());
    }

    /**
     * @return \Academe\Actions\Aggregate
     */
    protected function makeCountAggregateAction()
    {
        $action = new Aggregate('count', $this->field);

        $action->setLock($this->lockLevel);

        return $action;
    }

}
