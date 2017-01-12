<?php

namespace Academe\Contracts;

use Academe\Condition\Equal;
use Academe\Condition\GreaterThan;
use Academe\Condition\GreaterThanOrEqual;
use Academe\Condition\In;
use Academe\Condition\LessThan;
use Academe\Condition\LessThanOrEqual;
use Academe\Condition\Like;
use Academe\Condition\Mod;
use Academe\Condition\NotEqual;
use Academe\Condition\NotIn;

interface ConditionMaker
{
    /**
     * @param      $conditions
     * @param bool $mustSatisfyAll
     * @return \Academe\Contracts\Connection\ConditionGroup
     */
    public function group($conditions, $mustSatisfyAll = true);

    /**
     * @param $attribute
     * @param $value
     * @return Equal
     */
    public function equal($attribute, $value);

    /**
     * @param $attribute
     * @param $value
     * @return GreaterThan
     */
    public function greaterThan($attribute, $value);

    /**
     * @param $attribute
     * @param $value
     * @return GreaterThanOrEqual
     */
    public function greaterThanOrEqual($attribute, $value);

    /**
     * @param $attribute
     * @param $value
     * @return LessThan
     */
    public function lessThan($attribute, $value);

    /**
     * @param $attribute
     * @param $value
     * @return LessThanOrEqual
     */
    public function lessThanOrEqual($attribute, $value);

    /**
     * @param $attribute
     * @param $values
     * @return In
     */
    public function in($attribute, $values);

    /**
     * @param $attribute
     * @param $value
     * @param $matchMode
     * @return Like
     */
    public function like($attribute, $value, $matchMode);

    /**
     * @param $attribute
     * @param $divisor
     * @param $remainder
     * @return Mod
     */
    public function mod($attribute, $divisor, $remainder);

    /**
     * @param $attribute
     * @param $value
     * @return NotEqual
     */
    public function notEqual($attribute, $value);

    /**
     * @param $attribute
     * @param $values
     * @return NotIn
     */
    public function notIn($attribute, $values);

    /**
     * @param $field
     * @param $size
     * @return \Academe\Condition\SizeIs
     */
    public function sizeIs($field, $size);

    /**
     * @param $field
     * @param bool $isExists
     * @return \Academe\Condition\FieldExists
     */
    public function fieldExists($field, $isExists);

    /**
     * @param $field
     * @param $typeAlias
     * @return \Academe\Condition\TypeIs
     */
    public function typeIs($field, $typeAlias);

    /**
     * @param $field
     * @param $values
     * @return \Academe\Condition\ContainsAll
     */
    public function containsAll($field, $values);

}
