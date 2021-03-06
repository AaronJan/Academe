<?php

namespace Academe\Contracts;

interface Writer
{
    /**
     * @param null|integer $isolationLevel
     * @return \Academe\Transaction
     */
    public function newTransaction($isolationLevel = null);

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
     * @return \Academe\Statement\ConditionStatement
     */
    public function isNull($field);

    /**
     * @param $field
     * @return \Academe\Statement\ConditionStatement
     */
    public function notNull($field);
    
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
    public function greaterThanOrEqual($field, $value);

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
     * @param $field
     * @param $size
     * @return \Academe\Statement\ConditionStatement
     */
    public function sizeIs($field, $size);

    /**
     * @param      $field
     * @param bool $isExists
     * @return \Academe\Statement\ConditionStatement
     */
    public function fieldExists($field, $isExists);

    /**
     * @param $field
     * @param $typeAlias
     * @return \Academe\Statement\ConditionStatement
     */
    public function typeIs($field, $typeAlias);

    /**
     * @param $field
     * @param $values
     * @return \Academe\Statement\ConditionStatement
     */
    public function containsAll($field, $values);

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

    /**
     * @return \Academe\MongoDB\Statement\InstructionStatement
     */
    public function queryAsMongoDB();

    /**
     * @param mixed $rawQuery
     * @return \Academe\Contracts\Raw
     */
    public function raw($rawQuery);

    /**
     * @return \Academe\Accumulations\AccumulationMaker
     */
    public function accumulation();
}
