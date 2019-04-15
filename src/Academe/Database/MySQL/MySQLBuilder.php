<?php

namespace Academe\Database\MySQL;

use Academe\Actions\Aggregate;
use Academe\Actions\Group;
use Academe\Actions\Select;
use Academe\Constant\ConnectionConstant;
use Academe\Contracts\CastManager;
use Academe\Contracts\Connection\Formation;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Connection\Builder as BuilderContract;
use Academe\Contracts\Connection\Action;
use Academe\Contracts\Raw;
use Academe\Database\BaseBuilder;
use Academe\Traits\SQLValueWrapper;
use Academe\Database\Traits\GroupParsingHelper;
use Academe\Support\ArrayHelper;
use Academe\Contracts\Accumulation;
use Academe\Accumulations;
use Academe\Exceptions\RuntimeException;
use Academe\Exceptions\BadMethodCallException;

class MySQLBuilder extends BaseBuilder implements BuilderContract
{
    use SQLValueWrapper, GroupParsingHelper;

    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * MySQLBuilder constructor.
     *
     * @param string $tablePrefix
     */
    public function __construct($tablePrefix = '')
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @param                                      $subject
     * @param \Academe\Contracts\Connection\Action $action
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MySQL\MySQLQuery
     */
    public function parse($subject, Action $action, CastManager $castManager = null)
    {
        $method = 'parse' . ucfirst($action->getName());

        return $this->$method($action, $subject, $castManager);
    }

    /**
     * @param Action|Select $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MySQL\MySQLQuery
     */
    protected function parseSelect(Action $action, $subject, CastManager $castManager = null)
    {
        $conditionGroup = $action->getConditionGroup();
        $formation = $action->getFormation();
        $SQL = implode(' ', [
            "SELECT",
            $this->columnize($action->getParameters()),
            $this->compileFrom(($this->tablePrefix . $subject)),
        ]);

        list($conditionSQL, $conditionParameters) = $this->analyseConditionClause($conditionGroup, $castManager);
        list($formationSQL, $formationParameters) = $this->analyseFormationClause($formation);

        $lockSQL = $this->compileLockable($action);

        return new MySQLQuery(
            'select',
            "{$SQL}{$conditionSQL}{$formationSQL}{$lockSQL}",
            array_merge($conditionParameters, $formationParameters),
            false
        );
    }

    /**
     * @param Formation|null $formation
     * @return array
     */
    protected function analyseFormationClause(Formation $formation = null)
    {
        if ($formation === null) {
            return ['', []];
        }

        $SQL = '';
        $parameters = [];

        list($orderSQL, $orderParameters) = $this->resolveOrders($formation->getOrders());
        list($limitSQL, $limitParameters) = $this->resolveLimit($formation->getLimit());

        $SQL = implode(' ', [$orderSQL, $limitSQL]);
        $parameters = array_merge($parameters, $orderParameters, $limitParameters);
        $SQL = mb_strlen($SQL) > 0 ? " {$SQL}" : $SQL;

        return [$SQL, $parameters];
    }

    /**
     * @param array|null $limit
     * @return array
     */
    protected function resolveLimit($limit)
    {
        if ($limit === null) {
            return ['', []];
        }

        $SQL = '';
        $parameters = [];

        list($limitation, $offset) = $limit;

        if ($offset !== null) {
            $SQL = 'LIMIT ' . ((int)$offset) . ', ' . ((int)$limitation);
        } else {
            $SQL = 'LIMIT ' . ((int)$limitation);
        }

        return [$SQL, $parameters];
    }

    /**
     * @param array|null $orders
     * @return array
     */
    protected function resolveOrders($orders)
    {
        if ($orders === null) {
            return ['', []];
        }

        $SQL = '';
        $parameters = [];

        if (!empty($orders)) {
            $SQL = 'ORDER BY ';

            $orderSQLs = [];
            foreach ($orders as $order) {
                $orderSQLs[] = $this->getOrderSQLPart($order);
            }

            $SQL .= implode(', ', $orderSQLs);
        }

        return [$SQL, $parameters];
    }

    /**
     * @param array $order
     * @return string
     */
    protected function getOrderSQLPart($order)
    {
        list($field, $direction) = $order;

        $fieldPart = self::wrap($field);
        $directionPart = $direction === 'desc' ? 'DESC' : 'ASC';

        return "{$fieldPart} {$directionPart}";
    }

    /**
     * @param Action $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseDelete(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup) = $action->getParameters();
        $SQL = implode(' ', [
            "DELETE",
            $this->compileFrom(($this->tablePrefix . $subject)),
        ]);

        list($conditionSQL, $parameters) = $this->analyseConditionClause($conditionGroup, $castManager);

        return new MySQLQuery(
            'delete',
            "{$SQL}{$conditionSQL}",
            $parameters,
            true
        );
    }

    /**
     * @param Action $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseInsert(Action $action, $subject, CastManager $castManager = null)
    {
        list($attributes) = $action->getParameters();

        ksort($attributes);

        if ($castManager) {
            $attributes = $this->castAttributes($castManager, $attributes, ConnectionConstant::TYPE_MYSQL);
        }

        $attributeNameSQLPart = '(' . $this->columnize(array_keys($attributes)) . ')';
        $valueSQLPart = '(' . implode(', ', array_pad([], count($attributes), '?')) . ')';

        $SQL = implode(' ', [
            "INSERT INTO",
            static::wrap($this->tablePrefix . $subject),
            $attributeNameSQLPart,
            'VALUES',
            $valueSQLPart,
        ]);

        $parameters = array_values($attributes);

        return new MySQLQuery(
            'insert',
            "{$SQL}",
            $parameters,
            true
        );
    }

    /**
     * @param Action $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseUpdate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $attributes) = $action->getParameters();

        ksort($attributes);

        $columns = [];
        $parameters = [];
        foreach ($attributes as $key => $value) {
            if ($value instanceof Raw) {
                $columns[] = self::wrap($key) . " = {$value->getRaw()}";
            } else {
                $columns[] = self::wrap($key) . ' = ?';
                $parameters[] = $castManager->castIn($key, $value, ConnectionConstant::TYPE_MYSQL);
            }
        }

        list($conditionSQL, $conditionParameters) = $this->analyseConditionClause($conditionGroup, $castManager);

        $SQL = implode(' ', [
            'UPDATE',
            static::wrap($this->tablePrefix . $subject),
            'SET',
            implode(' ,', $columns),
        ]);

        return new MySQLQuery(
            'update',
            "{$SQL}{$conditionSQL}",
            array_merge($parameters, $conditionParameters),
            true
        );
    }

    /**
     * @param Action|Aggregate $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseAggregate(Action $action, $subject, CastManager $castManager = null)
    {
        list($method, $field) = $action->getParameters();

        $conditionGroup = $action->getConditionGroup();
        $function = $this->getAggregateSQLFunction($action);
        $aggregateField = static::wrap($field);

        $SQL = implode(' ', [
            "SELECT {$function}({$aggregateField}) as `aggregation`",
            $this->compileFrom(($this->tablePrefix . $subject)),
        ]);

        list($conditionSQL, $parameters) = $this->analyseConditionClause($conditionGroup, $castManager);

        $lockSQL = $this->compileLockable($action);

        return new MySQLQuery(
            'aggregate',
            "{$SQL}{$conditionSQL}{$lockSQL}",
            $parameters,
            false,
            [
                'field' => $field,
            ]
        );
    }

    /**
     * @param Action|Group $action
     * @param $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseGroup(Action $action, $subject, CastManager $castManager = null)
    {
        list($aggregation, $values) = $action->getParameters();
        $normalizedAggregation = $this->normalizeAggregationArray($aggregation);
        $formation = $action->getFormation();

        $conditionGroup = $action->getConditionGroup();
        list($conditionSQL, $parameters) = $this->analyseConditionClause($conditionGroup, $castManager);
        list($formationSQL, $formationParameters) = $this->analyseFormationClause($formation);

        $fromSQL = $this->compileFrom(($this->tablePrefix . $subject));
        $lockSQL = $this->compileLockable($action);

        $selectSQL = $this->resolveSelectSQLForGroup($normalizedAggregation, $values);
        $groupBySQL = $this->resolveGroupBySQLForGroup($normalizedAggregation);

        return new MySQLQuery(
            'group',
            "{$selectSQL} {$fromSQL}{$conditionSQL} {$groupBySQL}{$formationSQL}{$lockSQL}",
            array_merge($parameters, $formationParameters),
            false
        );
    }

    /**
     * @param array $normalizedAggregation
     * @param array $values
     * @return string
     */
    protected function resolveSelectSQLForGroup($normalizedAggregation, $values)
    {
        $SQL = 'SELECT ';

        $aggregationFields = ArrayHelper::map($normalizedAggregation, function ($value, $field) {
            if (is_array($value)) {
                $expression = $value[0] instanceof Raw ?
                    $value[0]->getRaw() : $value[0];

                return "`{$expression}` AS `{$field}`";
            }

            return "`{$field}` AS `{$field}`";
        });

        $valueFields = ArrayHelper::map($values, function ($accumulation, $field) {
            if ($accumulation instanceof Accumulation) {
                return "{$this->resolveAccumulation($accumulation)} AS `{$field}`";
            }

            if (!is_array($accumulation)) {
                throw new BadMethodCallException("Accumulation parameter is illegal");
            }

            $expression = $accumulation[0] instanceof Raw ?
                $accumulation[0]->getRaw() : $this->resolveAccumulation($accumulation[0]);

            return "{$expression} AS `{$field}`";
        });

        $SQL .= implode(', ', array_merge($aggregationFields, $valueFields));

        return $SQL;
    }

    protected function resolveAccumulation(Accumulation $accumulation)
    {
        $class = get_class($accumulation);
        switch ($class) {
            case Accumulations\Count::class:
                return "COUNT(*)";
                break;
            case Accumulations\Max::class:
                return "MAX(`{$accumulation->getField()}`)";
                break;
            case Accumulations\Min::class:
                return "MIN(`{$accumulation->getField()}`)";
                break;
            case Accumulations\Sum::class:
                return "SUM(`{$accumulation->getField()}`)";
                break;
            case Accumulations\Avg::class:
                return "AVG(`{$accumulation->getField()}`)";
                break;
            default:
                throw new RuntimeException("undefined Accumulation class: \"{$class}\"");
                break;
        }
    }

    /**
     * @param array $normalizedAggregation
     * @return string
     */
    protected function resolveGroupBySQLForGroup($normalizedAggregation)
    {
        $SQL = 'GROUP BY ';

        $aggregationFields = ArrayHelper::map($normalizedAggregation, function ($value, $field) {
            if (is_array($value)) {
                $expression = $value[0] instanceof Raw ?
                    $value[0]->getRaw() : $value[0];

                return $expression;
            }

            return "`{$field}`";
        });

        $SQL .= implode(', ', $aggregationFields);

        return $SQL;
    }

    /**
     * @param \Academe\Actions\Traits\BeLockable|Action $action
     * @return string
     */
    protected function compileLockable($action)
    {
        $SQL = '';

        switch ($action->getLockLevel()) {
            case 1:
                $SQL = ' LOCK IN SHARE MODE';
                break;
            case 2:
                $SQL = ' FOR UPDATE';
                break;
            default:
        }

        return $SQL;
    }

    /**
     * count, min, max, avg, sum
     *
     * @param Action $action
     * @return string
     */
    protected function getAggregateSQLFunction(Action $action)
    {
        list($method) = $action->getParameters();

        return strtoupper($method);
    }

    /**
     * @param ConditionGroup|null $conditionGroup
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    protected function analyseConditionClause(
        ConditionGroup $conditionGroup = null,
        CastManager $castManager = null
    ) {
        // Every query have at least one ConditionGroup parameter, it's null at
        // default, should ignore it.
        if ($conditionGroup === null) {
            return ['', []];
        }

        list($conditionSQL, $parameters) = $this->resolveConditionGroup($conditionGroup, false, $castManager);

        if (strlen($conditionSQL) > 0) {
            $conditionSQL = " WHERE {$conditionSQL}";
        }

        return [$conditionSQL, $parameters];
    }

    /**
     * @param Action|\Academe\Contracts\Action\Conditionable $action
     * @param                                                $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseCalculate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $column, $operator, $value) = $action->getParameters();

        $parameters = [$value];
        $setClause = self::wrap($column) . ' = ?';

        list($conditionSQL, $conditionParameters) = $this->analyseConditionClause($conditionGroup, $castManager);

        $SQL = implode(' ', [
            'UPDATE',
            $this->compileFrom(($this->tablePrefix . $subject)),
            'SET',
            $setClause,
        ]);

        return new MySQLQuery(
            'update',
            "{$SQL}{$conditionSQL}",
            array_merge($parameters, $conditionParameters),
            true
        );
    }

    /**
     * @param $columns
     * @return string
     */
    protected function columnize($columns)
    {
        return implode(', ', array_map([__CLASS__, 'wrap'], $columns));
    }

    /**
     * @param $subject
     * @return string
     */
    protected function compileFrom($subject)
    {
        $table = static::wrap($subject);

        return "FROM {$table}";
    }

    /**
     * @param ConditionGroup|null $conditionGroup
     * @param bool $withParentheses
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function resolveConditionGroup(
        ConditionGroup $conditionGroup,
        $withParentheses = false,
        CastManager $castManager = null
    ) {
        // Ignore ConditionGroup that have no Condition in it.
        if ($conditionGroup->getConditionCount() === 0) {
            return ['', []];
        }

        $SQLs = [];
        $paramtersArrays = [];
        $conjunction = $conditionGroup->isStrict() ? ' AND ' : ' OR ';

        foreach ($conditionGroup->getConditions() as $condition) {
            if ($condition instanceof ConditionGroup) {
                $needParentheses = $condition->getConditionCount() > 1;

                list($SQL, $parameters) = $this->resolveConditionGroup(
                    $condition,
                    $needParentheses,
                    $castManager
                );
            } else {
                list($SQL, $parameters) = $condition->parse(
                    ConnectionConstant::TYPE_MYSQL,
                    $castManager
                );
            }

            if ($SQL !== '') {
                $SQLs[] = $SQL;
                $paramtersArrays[] = $parameters;
            }
        }

        $SQL = implode($conjunction, $SQLs);

        if ($withParentheses) {
            $SQL = "($SQL)";
        }

        return [
            $SQL,
            static::flattenArray($paramtersArrays),
        ];
    }

    /**
     * @param $array
     * @return array
     */
    static protected function flattenArray($array)
    {
        $return = [];

        array_walk_recursive($array, function ($x) use (&$return) {
            $return[] = $x;
        });

        return $return;
    }
}
