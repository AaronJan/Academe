<?php

namespace Academe\Instructions;

use Academe\Actions\Group as GroupAction;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;
use Academe\Instructions\Traits;
use Academe\Contracts\Mapper\Instructions\Group as GroupContract;
use Academe\Contracts\CastManager as CastManagerContract;
use Academe\Support\ArrayHelper;
use Academe\Contracts\Caster;
use Academe\Contracts\Accumulation;
use Academe\Casting\CastManager;
use Academe\Formation;

class Group extends BaseExecutable implements GroupContract
{
    use Traits\Lockable, Traits\Sortable, Traits\CastRecord;

    /**
     * @var array
     */
    protected $aggregation;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var Connection\ConditionGroup|null
     */
    protected $conditionGroup = null;

    /**
     * @param array $aggregation
     * @param array $values
     * @param Connection\ConditionGroup|null $conditionGroup
     */
    public function __construct(
        $aggregation,
        $values,
        Connection\ConditionGroup $conditionGroup = null
    ) {
        $this->aggregation = $aggregation;
        $this->values = $values;

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
        $query = $this->makeQuery($connection, $mapper);

        $records = $connection->run($query);

        // Resemble a new CastManager
        $castManager = new CastManager(array_merge(
            $this->getCastRulesForAggregations($this->aggregation, $mapper->getCastManager()),
            $this->getCastRulesForValues($this->values)
        ));

        return $this->castRecordsUsingCastManager($records, $castManager, $mapper->getConnection()->getType());
    }

    /**
     * @param array $aggregations
     * @param CastManagerContract $castManager
     * @return array
     */
    protected function getCastRulesForAggregations(array $aggregations, CastManagerContract $castManager = null)
    {
        $rules = ArrayHelper::mapWithKeys($aggregations, function ($aggregation, $key) use ($castManager) {
            $field = is_numeric($key) ? $aggregation : $key;

            if (
                is_array($aggregation)
                && isset($aggregation[1])
                && $aggregation[1] instanceof Caster
            ) {
                $caster = $aggregation[1];
            } else {
                $caster = $castManager->getCaster($field);
            }

            return [$field => $caster];
        });

        return array_filter( $rules, function ($caster) {
            return $caster !== null;
        });
    }

    /**
     * @param array $values
     * @return array
     */
    protected function getCastRulesForValues(array $values) {
        $rules = ArrayHelper::mapWithKeys( $values, function ($value, $field) {
            if ($value instanceof Accumulation) {
                return [$field => $value->getCaster()];
            }

            if (
                is_array($value)
                && isset($value[1])
                && $value[1] instanceof Caster
            ) {
                return [$field => $value[1]];
            }

            return [$field => null];
        });

        return array_filter($rules, function ($caster) {
            return $caster !== null;
        });
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection, Mapper $mapper)
    {
        $action = new GroupAction($this->aggregation, $this->values);
        $action->setLock($this->lockLevel);

        $formation = (new Formation())->setOrders($this->orders);
        $action->setFormation($formation);

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
