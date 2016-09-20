<?php

namespace Academe\Actions;

use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Connection\Action;
use Academe\Exceptions\BadMethodCallException;
use Academe\Actions\Traits\BeCondtionable;
use Academe\Contracts\Conditionable;

class Calculate implements Action, Conditionable
{
    use BeCondtionable;

    static protected $allowedOperators = ['-', '+'];

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var int
     */
    protected $value;

    /**
     * Calculate constructor.
     *
     * @param ConditionGroup $conditionGroup
     * @param                $field
     * @param                $operator
     * @param int            $value
     */
    public function __construct(ConditionGroup $conditionGroup, $field, $operator, $value = 1)
    {
        $this->validateOperator($operator);
        $this->validateValue($value);

        $this->conditionGroup = $conditionGroup;
        $this->field          = $field;
        $this->operator       = $operator;
        $this->value          = $value;
    }

    /**
     * @param $operator
     * @throws BadMethodCallException
     */
    protected function validateOperator($operator)
    {
        if (! in_array($operator, static::$allowedOperators)) {
            throw new BadMethodCallException("operator [{$operator}] now allowed");
        }
    }

    /**
     * @param $value
     * @throws BadMethodCallException
     */
    protected function validateValue($value)
    {
        if (! is_numeric($value)) {
            throw new BadMethodCallException("value [{$value}] isn't a number");
        }

        if ($value < 0) {
            throw new BadMethodCallException("value must be positive. [{$value}] given");
        }
    }

    public function getName()
    {
        return 'calculate';
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return [$this->conditionGroup, $this->field, $this->operator, $this->value];
    }
}
