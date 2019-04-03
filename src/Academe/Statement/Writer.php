<?php

namespace Academe\Statement;

use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Writer as WriterContract;
use Academe\Raw;
use Academe\Transaction;
use Academe\MongoDB\Statement\InstructionStatement as MongoDBInstructionStatement;
use Academe\Accumulations\AccumulationMaker;

class Writer implements WriterContract
{
    /**
     * @var \Academe\Contracts\ConditionMaker
     */
    protected $conditionMaker;

    /**
     * Writer constructor.
     *
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     */
    public function __construct(ConditionMaker $conditionMaker)
    {
        $this->conditionMaker = $conditionMaker;
    }

    /**
     * @param null|integer $isolationLevel
     * @return \Academe\Transaction
     */
    public function newTransaction($isolationLevel = null)
    {
        return new Transaction($isolationLevel);
    }

    /**
     * @param       $method
     * @param array $parameters
     * @return ConditionStatement
     */
    protected function makeConditionStatement($method, array $parameters)
    {
        $statement = new ConditionStatement($this->conditionMaker);

        return call_user_func_array([$statement, $method], $parameters);
    }

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function equal($field, $value)
    {
        return $this->makeConditionStatement('equal', func_get_args());
    }

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function notEqual($field, $value)
    {
        return $this->makeConditionStatement('notEqual', func_get_args());
    }

    /**
     * @param $field
     * @return \Academe\Statement\ConditionStatement
     */
    public function isNull($field)
    {
        return $this->makeConditionStatement('isNull', func_get_args());
    }

    /**
     * @param $field
     * @return \Academe\Statement\ConditionStatement
     */
    public function notNull($field)
    {
        return $this->makeConditionStatement('notNull', func_get_args());
    }

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function greaterThan($field, $value)
    {
        return $this->makeConditionStatement('greaterThan', func_get_args());
    }

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function greaterThanOrEqual($field, $value)
    {
        return $this->makeConditionStatement('greaterThanOrEqual', func_get_args());
    }

    /**
     * @param $field
     * @param $values
     * @return \Academe\Statement\ConditionStatement
     */
    public function in($field, $values)
    {
        return $this->makeConditionStatement('in', func_get_args());
    }

    /**
     * @param $field
     * @param $values
     * @return \Academe\Statement\ConditionStatement
     */
    public function notIn($field, $values)
    {
        return $this->makeConditionStatement('notIn', func_get_args());
    }

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function lessThan($field, $value)
    {
        return $this->makeConditionStatement('lessThan', func_get_args());
    }

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function lessThanOrEqual($field, $value)
    {
        return $this->makeConditionStatement('lessThanOrEqual', func_get_args());
    }

    /**
     * @param $field
     * @param $value
     * @param $matchMode
     * @return \Academe\Statement\ConditionStatement
     */
    public function like($field, $value, $matchMode)
    {
        return $this->makeConditionStatement('like', func_get_args());
    }

    /**
     * @param $field
     * @param $divisor
     * @param $remainder
     * @return \Academe\Statement\ConditionStatement
     */
    public function mod($field, $divisor, $remainder)
    {
        return $this->makeConditionStatement('mod', func_get_args());
    }

    /**
     * @param $field
     * @param $size
     * @return \Academe\Statement\ConditionStatement
     */
    public function sizeIs($field, $size)
    {
        return $this->makeConditionStatement('sizeIs', func_get_args());
    }

    /**
     * @param      $field
     * @param bool $isExists
     * @return \Academe\Statement\ConditionStatement
     */
    public function fieldExists($field, $isExists)
    {
        return $this->makeConditionStatement('fieldExists', func_get_args());
    }

    /**
     * @param $field
     * @param $typeAlias
     * @return \Academe\Statement\ConditionStatement
     */
    public function typeIs($field, $typeAlias)
    {
        return $this->makeConditionStatement('typeIs', func_get_args());
    }

    /**
     * @param $field
     * @param $values
     * @return \Academe\Statement\ConditionStatement
     */
    public function containsAll($field, $values)
    {
        return $this->makeConditionStatement('containsAll', func_get_args());
    }

    /**
     * @param \Academe\Contracts\Connection\Condition|\Academe\Contracts\Connection\Condition[]|\Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\ConditionGroup[] $conditions
     * @return \Academe\Statement\ConditionStatement
     */
    public function any($conditions)
    {
        return $this->makeConditionStatement('any', func_get_args());
    }

    /**
     * @param \Academe\Contracts\Connection\Condition|\Academe\Contracts\Connection\Condition[]|\Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\ConditionGroup[] $conditions
     * @return \Academe\Statement\ConditionStatement
     */
    public function must($conditions)
    {
        return $this->makeConditionStatement('must', func_get_args());
    }

    /**
     * @return \Academe\Statement\InstructionStatement
     */
    public function query()
    {
        return new InstructionStatement($this->conditionMaker);
    }

    /**
     * @return \Academe\MongoDB\Statement\InstructionStatement
     */
    public function queryAsMongoDB()
    {
        return new MongoDBInstructionStatement($this->conditionMaker);
    }

    /**
     * @param mixed $raw
     * @return \Academe\Contracts\Raw|\Academe\Raw
     */
    public function raw($raw)
    {
        return new Raw($raw);
    }

    /**
     * @return AccumulationMaker
     */
    public function accumulation()
    {
        return new AccumulationMaker();
    }
}