<?php

namespace Academe\Database\MongoDB;

use Academe\Actions\Aggregate;
use Academe\Contracts\CastManager;
use Academe\Contracts\Conditionable;
use Academe\Contracts\Connection\Formation;
use Academe\Contracts\Directable;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Connection\Builder as BuilderContract;
use Academe\Contracts\Connection\Action;
use Academe\Database\BaseBuilder;
use Academe\MongoDB\Statement\MongoDBManualUpdate;

class MongoDBBuilder extends BaseBuilder implements BuilderContract
{
    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * MySQLGrammar constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param                                      $subject
     * @param \Academe\Contracts\Connection\Action $action
     * @param \Academe\Contracts\CastManager|null  $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MongoDB\MongoDBQuery
     */
    public function parse($subject, Action $action, CastManager $castManager = null)
    {
        // parse[Method], for example: $this->parseSelect()
        $method = 'parse' . ucfirst($action->getName());

        return $this->{$method}($action, $subject, $castManager);
    }

    /**
     * @param Action|Conditionable|Directable     $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseSelect(Action $action, $subject, CastManager $castManager = null)
    {
        $formation      = $action->getFormation();
        $conditionGroup = $action->getConditionGroup();
        $projections    = $this->fieldsToProjections($action->getParameters());
        $operation      = 'find';
        $collection     = $subject;

        $filters = $this->resolveConditionGroup($conditionGroup, $castManager);

        $options = $this->resolveFormation($formation);

        if (! empty($projections)) {
            $options['projection'] = $projections;
        }

        return new MongoDBQuery(
            $operation,
            $collection,
            [$filters, $options],
            false
        );
    }

    /**
     * @param Formation|null $formation
     * @return array
     */
    protected function resolveFormation(Formation $formation = null)
    {
        if ($formation === null) {
            return [];
        }

        $options = array_merge(
            [],
            $this->resolveFormationLimit($formation->getLimit()),
            $this->resolveFormationOrders($formation->getOrders())
        );

        return $options;
    }

    /**
     * @param $limit
     * @return array
     */
    protected function resolveFormationLimit($limit)
    {
        $options = [];

        if ($limit !== null) {
            list($number, $offset) = $limit;

            $options['limit'] = $number;

            if ($offset !== null) {
                $options['skip'] = $offset;
            }
        }

        return $options;
    }

    /**
     * @param $orders
     * @return array
     */
    protected function resolveFormationOrders($orders)
    {
        $options = [];

        if ($orders !== null) {
            $sort = [];

            foreach ($orders as $order) {
                list($field, $direction) = $order;

                $sort[$field] = $direction === 'desc' ? - 1 : 1;
            }

            $options['sort'] = $sort;
        }

        return $options;
    }

    /**
     * @param Action                              $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseDelete(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup) = $action->getParameters();

        $operation  = 'deletemany';
        $collection = $subject;

        $filters = $this->resolveConditionGroup($conditionGroup, $castManager);

        return new MongoDBQuery(
            $operation,
            $collection,
            [$filters],
            true
        );
    }

    /**
     * @param \Academe\Contracts\Connection\Action|Conditionable $action
     * @param                                                    $subject
     * @param \Academe\Contracts\CastManager|null                $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseAggregate(Action $action, $subject, CastManager $castManager = null)
    {
        $pipeline = [];

        $conditionGroup = $action->getConditionGroup();
        $operation      = 'aggregate';
        $collection     = $subject;

        // Match State，filter result
        $matchStage = $this->resolveConditionGroup($conditionGroup, $castManager);
        if (! empty($matchStage)) {
            $pipeline[] = ['$match' => $matchStage];
        }

        $pipeline = array_merge($pipeline, $this->getAggregatePipeline($action));

        return new MongoDBQuery(
            $operation,
            $collection,
            [$pipeline],
            false
        );
    }

    /**
     * @param Action                              $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseInsert(Action $action, $subject, CastManager $castManager = null)
    {
        list($attributes) = $action->getParameters();

        if ($castManager) {
            $attributes = $this->castAttributes($castManager, $attributes, Connection::TYPE_MONGODB);
        }

        $operation  = 'insertone';
        $collection = $subject;

        return new MongoDBQuery(
            $operation,
            $collection,
            [$attributes],
            false
        );
    }

    /**
     * @param Action                              $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseUpdate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $attributes) = $action->getParameters();

        if ($castManager) {
            $attributes = $this->castAttributes($castManager, $attributes, Connection::TYPE_MONGODB);
        }

        $operation  = 'updatemany';
        $collection = $subject;

        // 将条件解析为filters
        $filters = $this->resolveConditionGroup($conditionGroup, $castManager);

        return new MongoDBQuery(
            $operation,
            $collection,
            [$filters, ['$set' => $attributes]],
            false
        );
    }

    /**
     * @param Action                              $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseAdvanceupdate(Action $action, $subject, CastManager $castManager = null)
    {
        /**
         * @var $mongodbManualUpdate MongoDBManualUpdate
         */

        list($conditionGroup, $mongodbManualUpdate) = $action->getParameters();

        $compiledUpdateParameters = $mongodbManualUpdate->compileToUpdateParameters(
            Connection::TYPE_MONGODB,
            $castManager
        );

        $operation  = 'update';
        $collection = $subject;

        // 将条件解析为filters
        $filters = $this->resolveConditionGroup($conditionGroup, $castManager);

        return new MongoDBQuery(
            $operation,
            $collection,
            [
                $filters,
                $compiledUpdateParameters['update'],
                $compiledUpdateParameters['options'],
            ],
            false
        );
    }

    /**
     * @param Action $action
     * @return array
     */
    protected function getAggregatePipeline(Action $action)
    {
        $piepeline = [];

        list($method, $field) = $action->getParameters();

        if ($method === Aggregate::METHOD_COUNT) {
            if ($field !== '*') {
                $piepeline[] = [
                    '$match' => [
                        $field => [
                            '$exists' => true,
                        ],
                    ],
                ];
            }

            $piepeline[] = [
                '$group' => [
                    '_id'   => null,
                    'value' => [
                        '$sum' => 1,
                    ],
                ],
            ];
        } else {
            $piepeline[] = [
                '$group' => [
                    '_id'   => null,
                    'value' => [
                        "\${$method}" => "\${$field}",
                    ],
                ],
            ];
        }

        return $piepeline;
    }

    /**
     * @param Action|Conditionable                $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseCalculate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $field, $operator, $value) = $action->getParameters();

        $operation  = 'updatemany';
        $collection = $subject;

        // to Filters
        $filters = $this->resolveConditionGroup($conditionGroup, $castManager);

        return new MongoDBQuery(
            $operation,
            $collection,
            [
                $filters,
                ['$inc' => [$field => $this->getValueForIncOperation($operator, $value)]],
            ],
            false
        );
    }

    /**
     * @param $operator
     * @param $value
     * @return int|number
     */
    protected function getValueForIncOperation($operator, $value)
    {
        return $operator === '+' ? $value : (- 1 * $value);
    }

    /**
     * @param $fields
     * @return array
     */
    protected function fieldsToProjections($fields)
    {
        $projections = [];

        foreach ($fields as $field) {
            if ($field !== '*') {
                $projections[$field] = 1;
            }
        }

        return $projections;
    }

    /**
     * @param ConditionGroup|null                 $conditionGroup
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function resolveConditionGroup(ConditionGroup $conditionGroup = null,
                                          CastManager $castManager = null)
    {
        if (
            $conditionGroup === null ||
            $conditionGroup->getConditionCount() === 0
        ) {
            return [];
        }

        $useNested  = false;
        $subQueries = [];

        foreach ($conditionGroup->getConditions() as $condition) {
            if ($condition instanceof ConditionGroup) {
                if ($condition->getConditionCount() > 1) {
                    $useNested = true;
                }

                $subQueries[] = $this->resolveConditionGroup($condition, $castManager);
            } else {
                $subQueries[] = $condition->parse(Connection::TYPE_MONGODB, $castManager);
            }
        }

        if ($conditionGroup->isStrict()) {
            return $useNested ?
                ['$and' => $subQueries] :
                static::collapseQueries($subQueries);
        } else {
            return ['$or' => $subQueries];
        }
    }

    /**
     * @param array $queries
     * @return array mixed
     */
    static public function collapseQueries(array $queries)
    {
        $collapsed = [];

        foreach ($queries as $query) {
            reset($query);

            $field    = key($query);
            $criteria = $query[$field];

            if (isset($collapsed[$field])) {
                $collapsed[$field] = array_merge($collapsed[$field], $criteria);
            } else {
                $collapsed[$field] = $criteria;
            }
        }

        return $collapsed;
    }

}
