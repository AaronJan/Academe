<?php

namespace Academe;

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
use Academe\Contracts\Academe as AcademeContract;
use Academe\Contracts\ConditionMaker as ConditionMakerContract;

class ConditionMaker implements ConditionMakerContract
{
    /**
     * @var AcademeContract
     */
    protected $Academe;

    /**
     * ConditionMaker constructor.
     *
     * @param AcademeContract $Academe
     */
    public function __construct(AcademeContract $Academe)
    {
        $this->Academe = $Academe;
    }

    /**
     * @param      $conditions
     * @param bool $mustSatisfyAll
     * @return \Academe\Contracts\Connection\ConditionGroup
     */
    public function group($conditions, $mustSatisfyAll = true)
    {
        if (! is_array($conditions)) {
            $conditions = func_get_args();
        }

        return new ConditionGroup($conditions, $mustSatisfyAll);
    }

    /**
     * @param $attribute
     * @param $value
     * @return Equal
     */
    public function equal($attribute, $value)
    {
        return new Equal($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @return GreaterThan
     */
    public function greaterThan($attribute, $value)
    {
        return new GreaterThan($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @return GreaterThanOrEqual
     */
    public function greaterThaniOrEqual($attribute, $value)
    {
        return new GreaterThanOrEqual($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @return LessThan
     */
    public function lessThan($attribute, $value)
    {
        return new LessThan($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @return LessThanOrEqual
     */
    public function lessThanOrEqual($attribute, $value)
    {
        return new LessThanOrEqual($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $values
     * @return In
     */
    public function in($attribute, $values)
    {
        return new In($attribute, $values);
    }

    /**
     * @param $attribute
     * @param $value
     * @param $matchMode
     * @return Like
     */
    public function like($attribute, $value, $matchMode)
    {
        return new Like($attribute, $value, $matchMode);
    }

    /**
     * @param $attribute
     * @param $divisor
     * @param $remainder
     * @return Mod
     */
    public function mod($attribute, $divisor, $remainder)
    {
        return new Mod($attribute, $divisor, $remainder);
    }

    /**
     * @param $attribute
     * @param $value
     * @return NotEqual
     */
    public function notEqual($attribute, $value)
    {
        return new NotEqual($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $values
     * @return NotIn
     */
    public function notIn($attribute, $values)
    {
        return new NotIn($attribute, $values);
    }

}

