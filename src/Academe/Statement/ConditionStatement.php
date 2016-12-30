<?php

namespace Academe\Statement;

use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Statement;
use Academe\Exceptions\BadMethodCallException;

class ConditionStatement implements Statement, ConditionGroup
{
    /**
     * @var \Academe\Contracts\ConditionMaker
     */
    protected $conditionMaker;

    /**
     * @var Condition[]
     */
    protected $conditions = [];

    /**
     * FluentStatement constructor.
     *
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     */
    public function __construct(ConditionMaker $conditionMaker)
    {
        $this->conditionMaker = $conditionMaker;
    }

    /**
     * @param null|Condition|ConditionGroup $condition
     * @return $this
     */
    public function apply($condition)
    {
        $this->addCondition($condition);

        return $this;
    }

    /**
     * @return InstructionStatement
     */
    public function upgrade()
    {
        $fluentStatement = new InstructionStatement($this->getConditionMaker());

        return $fluentStatement->loadFrom($this);
    }

    /**
     * @return \Academe\Contracts\ConditionMaker
     */
    public function getConditionMaker()
    {
        return $this->conditionMaker;
    }

    /**
     * @return \Academe\Contracts\Connection\Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param Condition|ConditionGroup $condition
     */
    protected function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function equal($attribute, $value)
    {
        $this->addCondition($this->conditionMaker->equal($attribute, $value));

        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function notEqual($attribute, $value)
    {
        $this->addCondition($this->conditionMaker->notEqual($attribute, $value));

        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function greaterThan($attribute, $value)
    {
        $this->addCondition($this->conditionMaker->greaterThan($attribute, $value));

        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function greaterThanOrEqual($attribute, $value)
    {
        $this->addCondition($this->conditionMaker->greaterThanOrEqual($attribute, $value));

        return $this;
    }

    /**
     * @param $attribute
     * @param $values
     * @return $this
     */
    public function in($attribute, $values)
    {
        $this->addCondition($this->conditionMaker->in($attribute, $values));

        return $this;
    }

    /**
     * @param $attribute
     * @param $values
     * @return $this
     */
    public function notIn($attribute, $values)
    {
        $this->addCondition($this->conditionMaker->notIn($attribute, $values));

        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function lessThan($attribute, $value)
    {
        $this->addCondition($this->conditionMaker->lessThan($attribute, $value));

        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function lessThanOrEqual($attribute, $value)
    {
        $this->addCondition($this->conditionMaker->lessThanOrEqual($attribute, $value));

        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $matchMode
     * @return $this
     */
    public function like($attribute, $value, $matchMode)
    {
        $this->addCondition($this->conditionMaker->like($attribute, $value, $matchMode));

        return $this;
    }

    /**
     * @param $attribute
     * @param $divisor
     * @param $remainder
     * @return $this
     */
    public function mod($attribute, $divisor, $remainder)
    {
        $this->addCondition($this->conditionMaker->mod($attribute, $divisor, $remainder));

        return $this;
    }

    /**
     * @param Condition|ConditionGroup|Condition[]|ConditionGroup[] $conditions
     * @return $this
     */
    public function any($conditions)
    {
        if (! is_array($conditions)) {
            $conditions = func_get_args();
        }

        $conditionGroup = $this->conditionMaker->group($conditions, false);

        $this->addCondition($conditionGroup);

        return $this;
    }

    /**
     * @param Condition|ConditionGroup|Condition[]|ConditionGroup[] $conditions
     * @return $this
     */
    public function must($conditions)
    {
        if (! is_array($conditions)) {
            $conditions = func_get_args();
        }

        $conditionGroup = $this->conditionMaker->group($conditions);

        $this->addCondition($conditionGroup);

        return $this;
    }

    /**
     * @return \Academe\Contracts\Connection\ConditionGroup
     */
    public function compileConditionGroup()
    {
        return $this->conditionMaker->group($this->conditions);
    }

    /**
     * @return int
     */
    public function getConditionCount()
    {
        return count($this->conditions);
    }

    /**
     * @return bool
     */
    public function isStrict()
    {
        return true;
    }

    public function asStrict()
    {
        throw new BadMethodCallException("Not allowed.");
    }

    /**
     * @return bool
     */
    public function isLoose()
    {
        return false;
    }

    public function asLoose()
    {
        throw new BadMethodCallException("Not allowed.");
    }
}

