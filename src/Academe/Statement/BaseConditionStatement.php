<?php

namespace Academe\Statement;

use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\ConditionMaker;

abstract class BaseConditionStatement
{
    /**
     * @var \Academe\Contracts\ConditionMaker|null
     */
    protected $conditionMaker;

    /**
     * @var Condition[]
     */
    protected $conditions = [];

    /**
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     */
    public function setConditionMaker(ConditionMaker $conditionMaker)
    {
        $this->conditionMaker = $conditionMaker;
    }

    /**
     * @return \Academe\Contracts\ConditionMaker
     */
    public function getConditionMaker()
    {
        return $this->conditionMaker;
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
     * @param $field
     * @param $value
     * @return $this
     */
    public function equal($field, $value)
    {
        $this->addCondition($this->conditionMaker->equal($field, $value));

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function notEqual($field, $value)
    {
        $this->addCondition($this->conditionMaker->notEqual($field, $value));

        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function isNull($field)
    {
        $this->addCondition($this->conditionMaker->isNull($field));

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function greaterThan($field, $value)
    {
        $this->addCondition($this->conditionMaker->greaterThan($field, $value));

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function greaterThanOrEqual($field, $value)
    {
        $this->addCondition($this->conditionMaker->greaterThanOrEqual($field, $value));

        return $this;
    }

    /**
     * @param $field
     * @param $values
     * @return $this
     */
    public function in($field, $values)
    {
        $this->addCondition($this->conditionMaker->in($field, $values));

        return $this;
    }

    /**
     * @param $field
     * @param $values
     * @return $this
     */
    public function notIn($field, $values)
    {
        $this->addCondition($this->conditionMaker->notIn($field, $values));

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function lessThan($field, $value)
    {
        $this->addCondition($this->conditionMaker->lessThan($field, $value));

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function lessThanOrEqual($field, $value)
    {
        $this->addCondition($this->conditionMaker->lessThanOrEqual($field, $value));

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @param $matchMode
     * @return $this
     */
    public function like($field, $value, $matchMode)
    {
        $this->addCondition($this->conditionMaker->like($field, $value, $matchMode));

        return $this;
    }

    /**
     * @param $field
     * @param $divisor
     * @param $remainder
     * @return $this
     */
    public function mod($field, $divisor, $remainder)
    {
        $this->addCondition($this->conditionMaker->mod($field, $divisor, $remainder));

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
        return $this->conditionMaker->group($this->getConditions());
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

    /**
     * @return bool
     */
    public function isLoose()
    {
        return false;
    }

}
