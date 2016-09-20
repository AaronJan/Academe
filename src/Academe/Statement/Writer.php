<?php

namespace Academe\Statement;

use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Writer as WriterContract;

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
    public function greaterThaniOrEqual($field, $value)
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
     * @param Condition[]|ConditionGroup[] $conditions
     * @return \Academe\Statement\ConditionStatement
     */
    public function any(array $conditions)
    {
        return $this->makeConditionStatement('any', func_get_args());
    }

    /**
     * @param Condition[]|ConditionGroup[] $conditions
     * @return \Academe\Statement\ConditionStatement
     */
    public function must(array $conditions)
    {
        return $this->makeConditionStatement('must', func_get_args());
    }

    /**
     * @return \Academe\Statement\InstructionStatement
     */
    public function fresh()
    {
        return new InstructionStatement($this->conditionMaker);
    }

}