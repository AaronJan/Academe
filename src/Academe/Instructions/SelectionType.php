<?php

namespace Academe\Instructions;

use Academe\Actions\Select;
use Academe\Contracts\CastManager;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;
use Academe\Formation;
use Academe\Instructions\Traits\Lockable;
use Academe\Instructions\Traits\Sortable;

abstract class SelectionType extends BaseExecutable
{
    use Lockable, Sortable;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var Connection\ConditionGroup|null
     */
    protected $conditionGroup = null;

    /**
     * SelectionType constructor.
     *
     * @param array                          $fields
     * @param Connection\ConditionGroup|null $conditionGroup
     */
    public function __construct($fields = ['*'],
                                Connection\ConditionGroup $conditionGroup = null)
    {
        $this->fields = $fields;

        if ($conditionGroup) {
            $this->setConditionGroup($conditionGroup);
        }
    }

    /**
     * @param $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

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
     * @return \Academe\Actions\Select
     */
    protected function makeSelectAction()
    {
        $action = new Select($this->fields);

        $action->setLock($this->lockLevel);

        return $action;
    }

    /**
     * @return Select
     */
    protected function makeSelectActionWithFormation()
    {
        $action    = $this->makeSelectAction();
        $formation = (new Formation())->setOrders($this->orders);

        $action->setFormation($formation);

        return $action;
    }

    /**
     * @param Connection\Connection            $connection
     * @param                                  $subject
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection, $subject, Mapper $mapper)
    {
        $action = $this->makeSelectActionWithFormation();

        if ($this->conditionGroup) {
            $action = $action->setConditionGroup($this->conditionGroup);
        }

        return $connection->makeBuilder()->parse($subject, $action, $mapper->getCastManager());
    }

    /**
     * @param Mapper $mapper
     * @return array
     */
    protected function getEntities(Mapper $mapper)
    {
        $connection = $mapper->getConnection();
        $query      = $this->makeQuery($connection, $mapper->getSubject(), $mapper);
        $records    = $connection->run($query);

        $castedRecords = $this->castRecords($records, $mapper);

        return $castedRecords;
    }

    /**
     * @param array                            $records
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @return array
     */
    protected function castRecords(array $records, Mapper $mapper)
    {
        $castManager    = $mapper->getCastManager();
        $connectionType = $mapper->getConnection()->getType();

        return array_map(function ($record) use ($castManager, $connectionType) {
            return $this->castRecord($record, $castManager, $connectionType);
        }, $records);
    }

    /**
     * @param                                $record
     * @param \Academe\Contracts\CastManager $castManager
     * @param                                $connectionType
     * @return array
     */
    protected function castRecord($record, CastManager $castManager, $connectionType)
    {
        $castedRecord = [];

        foreach ($record as $field => $value) {
            $castedRecord[$field] = $castManager->castOut($field, $value, $connectionType);
        }

        return $castedRecord;
    }

}