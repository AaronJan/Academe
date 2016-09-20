<?php

namespace Academe\Database\MySQL;

use Academe\Actions\Select;
use Academe\Contracts\CastManager;
use Academe\Contracts\Conditionable;
use Academe\Contracts\Connection\Formation;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Connection\Builder as BuilderContract;
use Academe\Contracts\Connection\Action;
use Academe\Database\BaseBuilder;
use Academe\Traits\SQLValueWrapper;

class MySQLBuilder extends BaseBuilder implements BuilderContract
{
    use SQLValueWrapper;

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
     * @param \Academe\Contracts\CastManager|null  $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MySQL\MySQLQuery
     */
    public function parse($subject, Action $action, CastManager $castManager = null)
    {
        // parse[Method], for example: $this->parseSelect()
        $method = 'parse' . ucfirst($action->getName());

        return $this->$method($action, $subject, $castManager);
    }

    /**
     * @param Action|Select                       $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query|\Academe\Database\MySQL\MySQLQuery
     */
    protected function parseSelect(Action $action, $subject, CastManager $castManager = null)
    {
        $conditionGroup = $action->getConditionGroup();
        $formation      = $action->getFormation();
        $SQL            = implode(' ', [
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

        $SQL        = '';
        $parameters = [];

        list($orderSQL, $orderParameters) = $this->resolveOrders($formation->getOrders());
        list($limitSQL, $limitParameters) = $this->resolveLimit($formation->getLimit());

        $SQL        = implode(' ', [$orderSQL, $limitSQL]);
        $parameters = array_merge($parameters, $orderParameters, $limitParameters);

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

        $SQL        = '';
        $parameters = [];

        list($limitation, $offset) = $limit;

        if ($offset !== null) {
            $SQL = 'LIMIT ' . ((int) $offset) . ((int) $limitation);
        } else {
            $SQL = 'LIMIT ' . ((int) $limitation);
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

        $SQL        = '';
        $parameters = [];

        if (! empty($orders)) {
            $SQL = 'ORDER BY ';

            $orderSQLs = [];
            foreach ($orders as $order) {
                $orderSQLs[] = $this->getOrderSQLPart($order);
            }

            $SQL .= implode(' ,', $orderSQLs);
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

        $fieldPart    = self::wrap($field);
        $directionPart = $direction === 'desc' ? 'DESC' : 'ASC';

        return "{$fieldPart}, {$directionPart}";
    }

    /**
     * @param Action                              $action
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

        list($conditionSQL, $parameters) = $this->analyseConditionClause($conditionGroup);

        return new MySQLQuery(
            'delete',
            "{$SQL}{$conditionSQL}",
            $parameters,
            true
        );
    }

    /**
     * @param Action                              $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseInsert(Action $action, $subject, CastManager $castManager = null)
    {
        list($attributes) = $action->getParameters();

        ksort($attributes);

        if ($castManager) {
            $attributes = $this->castAttributes($castManager, $attributes, Connection::TYPE_MYSQL);
        }

        $attributeNameSQLPart = '(' . $this->columnize(array_keys($attributes)) . ')';
        $valueSQLPart         = '(' . implode(', ', array_pad([], count($attributes), '?')) . ')';

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
     * @param Action                              $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseUpdate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $attributes) = $action->getParameters();

        ksort($attributes);

        if ($castManager) {
            $attributes = $this->castAttributes($castManager, $attributes, Connection::TYPE_MYSQL);
        }

        $parameters = array_values($attributes);

        $columns = [];
        foreach ($attributes as $key => $value) {
            $columns[] = self::wrap($key) . ' = ?';
        }

        list($conditionSQL, $conditionParameters) = $this->analyseConditionClause($conditionGroup);

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
     * @param Action|Conditionable                $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseAggregate(Action $action, $subject, CastManager $castManager = null)
    {
        list($method, $column) = $action->getParameters();

        $conditionGroup = $action->getConditionGroup();
        $function       = $this->getAggregateSQLFunction($action);
        $countColumn    = static::wrap($column);

        $SQL = implode(' ', [
            "SELECT {$function}({$countColumn}) as `aggregation`",
            $this->compileFrom(($this->tablePrefix . $subject)),
        ]);

        list($conditionSQL, $parameters) = $this->analyseConditionClause($conditionGroup);

        $lockSQL = $this->compileLockable($action);

        return new MySQLQuery(
            'aggregate',
            "{$SQL}{$conditionSQL}{$lockSQL}",
            $parameters,
            false
        );
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
     * @param ConditionGroup|null                 $conditionGroup
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    protected function analyseConditionClause(ConditionGroup $conditionGroup = null,
                                              CastManager $castManager = null)
    {
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
     * @param Action|Conditionable                $action
     * @param                                     $subject
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Database\MySQL\MySQLQuery
     */
    protected function parseCalculate(Action $action, $subject, CastManager $castManager = null)
    {
        list($conditionGroup, $column, $operator, $value) = $action->getParameters();

        $parameters = [$value];
        $setClause  = self::wrap($column) . ' = ?';

        list($conditionSQL, $conditionParameters) = $this->analyseConditionClause($conditionGroup);

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
     * @param ConditionGroup|null                 $conditionGroup
     * @param bool                                $withParentheses
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function resolveConditionGroup(ConditionGroup $conditionGroup,
                                          $withParentheses = false,
                                          CastManager $castManager = null)
    {
        if ($conditionGroup === null) {
            return ['', []];
        }

        $SQLs            = [];
        $paramtersArrays = [];
        $conjunction     = $conditionGroup->isStrict() ? ' AND ' : ' OR ';

        foreach ($conditionGroup->getConditions() as $condition) {
            if ($condition instanceof ConditionGroup) {
                $needParentheses = $condition->getConditionCount() > 1;

                list($SQL, $parameters) = $this->resolveConditionGroup($condition, $needParentheses);
            } else {
                $commandUnit = $condition->parse(Connection::TYPE_MYSQL, $castManager);
                list($SQL, $parameters) = $commandUnit->getRaw();
            }

            $SQLs[]            = $SQL;
            $paramtersArrays[] = $parameters;
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
