<?php

namespace Academe\Database\MongoDB;

use Academe\Actions\Aggregate;
use Academe\Constant\ConnectionConstant;
use Academe\Contracts\CastManager;
use Academe\Contracts\Action\Conditionable;
use Academe\Contracts\Connection\Formation;
use Academe\Contracts\Action\Directable;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Connection\Builder as BuilderContract;
use Academe\Contracts\Connection\Action;
use Academe\Contracts\Raw;
use Academe\Database\BaseBuilder;
use Academe\MongoDB\Statement\MongoDBManualUpdate;
use Academe\Support\ArrayHelper;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Academe\Contracts\Accumulation;
use Academe\Accumulations;
use Academe\Exceptions\RuntimeException;
use Academe\Exceptions\BadMethodCallException;
use Academe\Database\Traits\GroupParsingHelper;

class MongoDBBuilder extends BaseBuilder implements BuilderContract
{
    use GroupParsingHelper;

    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * @var \Academe\Database\MongoDB\ConditionResolver
     */
    protected $conditionResolver;

    /**
     * MySQLGrammar constructor.
     */
    public function __construct()
    {
        $this->conditionResolver = new ConditionResolver();
    }

    /**
     * @param                                      $subject
     * @param \Academe\Contracts\Connection\Action $action
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MongoDB\MongoDBQuery
     */
    public function parse($subject, Action $action, CastManager $castManager = null)
    {
        // parse[Method], for example: $this->parseSelect()
        $method = 'parse' . ucfirst($action->getName());

        return $this->{$method}($action, $subject, $castManager);
    }

    /**
     * @param Action|Conditionable|Directable $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseSelect(Action $action, $subject, CastManager $castManager = null)
    {
        $formation = $action->getFormation();
        $conditionGroup = $action->getConditionGroup();
        $projections = $this->fieldsToProjections($action->getParameters());
        $operation = 'find';
        $collection = $subject;

        $filters = $this->conditionResolver->resolveConditionGroup($conditionGroup, $castManager);

        $options = $this->resolveFormation($formation);

        if (!empty($projections)) {
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

                $sort[$field] = $direction === 'desc' ? -1 : 1;
            }

            $options['sort'] = $sort;
        }

        return $options;
    }

    /**
     * @param Action $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseDelete(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup) = $action->getParameters();

        $operation = 'deletemany';
        $collection = $subject;

        $filters = $this->conditionResolver->resolveConditionGroup($conditionGroup, $castManager);

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
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseAggregate(Action $action, $subject, CastManager $castManager = null)
    {
        $pipeline = [];

        $conditionGroup = $action->getConditionGroup();
        $operation = 'aggregate';
        $collection = $subject;

        list($method, $field) = $action->getParameters();

        // Match State，filter result
        $matchStage = $this->conditionResolver->resolveConditionGroup($conditionGroup, $castManager);
        if (!empty($matchStage)) {
            $pipeline[] = ['$match' => $matchStage];
        }

        $pipeline = array_merge($pipeline, $this->getAggregatePipeline($action));

        return new MongoDBQuery(
            $operation,
            $collection,
            [$pipeline],
            false,
            [
                'field' => $field,
            ]
        );
    }

    /**
     * @param \Academe\Contracts\Connection\Action|Conditionable $action
     * @param                                                    $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseGroup(Action $action, $subject, CastManager $castManager = null)
    {
        list($aggregation, $values) = $action->getParameters();
        $normalizedAggregation = $this->normalizeAggregationArray($aggregation);

        $formation = $action->getFormation();

        $pipelines = [];

        $conditionGroup = $action->getConditionGroup();
        $operation = 'group';
        $collection = $subject;

        // $match stage
        $matchStage = $this->conditionResolver->resolveConditionGroup($conditionGroup, $castManager);
        if (!empty($matchStage)) {
            $pipelines[] = ['$match' => $matchStage];
        }

        // $group stage
        $groupStage = $this->makeGroupPipelineStage($normalizedAggregation, $values);
        $pipelines[] = ['$group' => $groupStage];

        // $project stage
        $projectStage = $this->makeProjectPipelineStage($normalizedAggregation, $values);
        $pipelines[] = ['$project' => $projectStage];

        // Formation
        $options = $this->resolveFormation($formation);
        if (isset($options['sort'])) {
            $pipelines[] = ['$sort' => $options['sort']];
        }
        if (isset($options['limit'])) {
            $pipelines[] = ['$limit' => $options['limit']];
        }
        if (isset($options['skip'])) {
            $pipelines[] = ['$skip' => $options['skip']];
        }

        return new MongoDBQuery(
            $operation,
            $collection,
            [$pipelines],
            false
        );
    }

    /**
     * @param array $normalizedAggregation
     * @param array $values
     * @return array
     */
    protected function makeGroupPipelineStage($normalizedAggregation, $values)
    {
        $pipeline = [];

        $id = ArrayHelper::mapWithKeys($normalizedAggregation, function ($value, $field) {
            if (is_array($value)) {
                $expression = $value[0] instanceof Raw ?
                    $value[0]->getRaw() : "\${$value[0]}";

                return [$field => $expression];
            }

            return [$field => "\${$field}"];
        });
        $pipeline['_id']  = $id;

        $toBeMerged = ArrayHelper::mapWithKeys($values, function ($accumulation, $field) {
            if ($accumulation instanceof Accumulation) {
                return [$field => $this->resolveAccumulation($accumulation)];
            }

            if (!is_array($accumulation)) {
                throw new BadMethodCallException("Accumulation parameter is illegal");
            }

            $expression = $accumulation[0] instanceof Raw ?
                $accumulation[0]->getRaw() : $this->resolveAccumulation($accumulation[0]);

            return [$field => $expression];
        });
        $pipeline = array_merge($toBeMerged, $pipeline);

        return $pipeline;
    }

    protected function resolveAccumulation(Accumulation $accumulation)
    {
        $class = get_class($accumulation);
        switch ($class) {
            case Accumulations\Count::class:
                return ['$count' => 1];
                break;
            case Accumulations\Max::class:
                return ['$max' => "\${$accumulation->getField()}"];
                break;
            case Accumulations\Min::class:
                return ['$max' => "\${$accumulation->getField()}"];
                break;
            case Accumulations\Sum::class:
                return ['$max' => "\${$accumulation->getField()}"];
                break;
            case Accumulations\Avg::class:
                return ['$max' => "\${$accumulation->getField()}"];
                break;
            default:
                throw new RuntimeException("undefined Accumulation class: \"{$class}\"");
                break;
        }
    }

    /**
     * @param array $normalizedAggregation
     * @param array $values
     * @return array
     */
    protected function makeProjectPipelineStage($normalizedAggregation, $values)
    {
        $pipeline = [];

        $pipeline['_id'] = 0;

        $aggregations = ArrayHelper::mapWithKeys($normalizedAggregation, function ($aggregation, $field) {
            return [$field => "\$_id.{$field}"];
        });
        $values = ArrayHelper::mapWithKeys($values, function ($aggregation, $field) {
            return [$field => "\${$field}"];
        });

        return array_merge($aggregations, $values, $pipeline);
    }

    /**
     * @param Action $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseInsert(Action $action, $subject, CastManager $castManager = null)
    {
        list($attributes) = $action->getParameters();

        if ($castManager) {
            $attributes = $this->castAttributes(
                $castManager,
                $attributes,
                ConnectionConstant::TYPE_MONGODB
            );
        }

        $operation = 'insertone';
        $collection = $subject;

        return new MongoDBQuery(
            $operation,
            $collection,
            [$attributes],
            false
        );
    }

    /**
     * @param Action $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseUpdate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $attributes) = $action->getParameters();

        $castedAttributes = [];
        foreach ($attributes as $field => $value) {
            $castedAttributes[$field] = $value instanceof Raw ?
                $value->getRaw() : $castManager->castIn($field, $value, ConnectionConstant::TYPE_MONGODB);
        }

        $operation = 'updatemany';
        $collection = $subject;

        $filters = $this->conditionResolver->resolveConditionGroup($conditionGroup, $castManager);

        return new MongoDBQuery(
            $operation,
            $collection,
            [$filters, ['$set' => $castedAttributes]],
            false
        );
    }

    /**
     * @param Action $action
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
            ConnectionConstant::TYPE_MONGODB,
            $castManager
        );

        $operation = 'update';
        $collection = $subject;

        $filters = $this->conditionResolver->resolveConditionGroup($conditionGroup, $castManager);

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
     * @param Action|Conditionable $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MongoDB\MongoDBQuery
     */
    protected function parseCalculate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $field, $operator, $value) = $action->getParameters();

        $operation = 'updatemany';
        $collection = $subject;

        // to Filters
        $filters = $this->conditionResolver->resolveConditionGroup($conditionGroup, $castManager);

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
        return $operator === '+' ? $value : (-1 * $value);
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
}
