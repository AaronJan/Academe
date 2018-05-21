<?php

namespace Academe;

use Academe\Condition as AcademeConditions;
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
     * @return AcademeConditions\Equal
     */
    public function equal($attribute, $value)
    {
        return new AcademeConditions\Equal($attribute, $value);
    }

    /**
     * @param $field
     * @return \Academe\Condition\IsNull
     */
    public function isNull($field)
    {
        return new AcademeConditions\IsNull($field);
    }

    /**
     * @param $field
     * @return \Academe\Condition\NotNull
     */
    public function notNull($field)
    {
        return new AcademeConditions\NotNull($field);
    }

    /**
     * @param $attribute
     * @param $value
     * @return \Academe\Condition\GreaterThan
     */
    public function greaterThan($attribute, $value)
    {
        return new AcademeConditions\GreaterThan($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @return \Academe\Condition\GreaterThanOrEqual
     */
    public function greaterThanOrEqual($attribute, $value)
    {
        return new AcademeConditions\GreaterThanOrEqual($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @return \Academe\Condition\LessThan
     */
    public function lessThan($attribute, $value)
    {
        return new AcademeConditions\LessThan($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @return \Academe\Condition\LessThanOrEqual
     */
    public function lessThanOrEqual($attribute, $value)
    {
        return new AcademeConditions\LessThanOrEqual($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $values
     * @return \Academe\Condition\In
     */
    public function in($attribute, $values)
    {
        return new AcademeConditions\In($attribute, $values);
    }

    /**
     * @param $attribute
     * @param $value
     * @param $matchMode
     * @return \Academe\Condition\Like
     */
    public function like($attribute, $value, $matchMode)
    {
        return new AcademeConditions\Like($attribute, $value, $matchMode);
    }

    /**
     * @param $attribute
     * @param $divisor
     * @param $remainder
     * @return \Academe\Condition\Mod
     */
    public function mod($attribute, $divisor, $remainder)
    {
        return new AcademeConditions\Mod($attribute, $divisor, $remainder);
    }

    /**
     * @param $attribute
     * @param $value
     * @return \Academe\Condition\NotEqual
     */
    public function notEqual($attribute, $value)
    {
        return new AcademeConditions\NotEqual($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $values
     * @return \Academe\Condition\NotIn
     */
    public function notIn($attribute, $values)
    {
        return new AcademeConditions\NotIn($attribute, $values);
    }

    /**
     * @param $field
     * @param $size
     * @return \Academe\Condition\SizeIs
     */
    public function sizeIs($field, $size)
    {
        return new AcademeConditions\SizeIs($field, $size);
    }

    /**
     * @param $field
     * @param bool $isExists
     * @return \Academe\Condition\FieldExists
     */
    public function fieldExists($field, $isExists)
    {
        return new AcademeConditions\FieldExists($field, $isExists);
    }

    /**
     * @param $field
     * @param $typeAlias
     * @return \Academe\Condition\TypeIs
     */
    public function typeIs($field, $typeAlias)
    {
        return new AcademeConditions\TypeIs($field, $typeAlias);
    }

    /**
     * @param $field
     * @param $values
     * @return \Academe\Condition\ContainsAll
     */
    public function containsAll($field, $values)
    {
        return new AcademeConditions\ContainsAll($field, $values);
    }

}

