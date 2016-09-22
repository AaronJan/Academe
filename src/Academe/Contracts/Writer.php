<?php

namespace Academe\Contracts;

interface Writer
{
    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function equal($field, $value);

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function notEqual($field, $value);

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function greaterThan($field, $value);

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function greaterThaniOrEqual($field, $value);

    /**
     * @param $field
     * @param $values
     * @return \Academe\Statement\ConditionStatement
     */
    public function in($field, $values);

    /**
     * @param $field
     * @param $values
     * @return \Academe\Statement\ConditionStatement
     */
    public function notIn($field, $values);

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function lessThan($field, $value);

    /**
     * @param $field
     * @param $value
     * @return \Academe\Statement\ConditionStatement
     */
    public function lessThanOrEqual($field, $value);

    /**
     * @param $field
     * @param $value
     * @param $matchMode
     * @return \Academe\Statement\ConditionStatement
     */
    public function like($field, $value, $matchMode);

    /**
     * @param $field
     * @param $divisor
     * @param $remainder
     * @return \Academe\Statement\ConditionStatement
     */
    public function mod($field, $divisor, $remainder);

    /**
     * @param \Academe\Contracts\Connection\Condition|\Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\Condition[]|\Academe\Contracts\Connection\ConditionGroup[] $conditions
     * @return \Academe\Statement\ConditionStatement
     */
    public function any($conditions);

    /**
     * @param \Academe\Contracts\Connection\Condition|\Academe\Contracts\Connection\ConditionGroup|\Academe\Contracts\Connection\Condition[]|\Academe\Contracts\Connection\ConditionGroup[] $conditions
     * @return \Academe\Statement\ConditionStatement
     */
    public function must($conditions);

    /**
     * @return \Academe\Statement\InstructionStatement
     */
    public function query();
}
