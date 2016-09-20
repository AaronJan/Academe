<?php

namespace Academe;

use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup as ConditionGroupContract;

class ConditionGroup implements ConditionGroupContract
{
    /**
     * @var bool
     */
    protected $mustSatisfyAll = true;

    /**
     * @var Condition[]|\Academe\Contracts\Connection\ConditionGroup[]|array
     */
    protected $conditions = [];

    /**
     * @var array
     */
    protected $siblings = [];

    /**
     * ConditionGroup constructor.
     *
     * @param array $conditions
     * @param bool  $mustSatisfyAll
     */
    public function __construct(array $conditions, $mustSatisfyAll = true)
    {
        $this->conditions     = $conditions;
        $this->mustSatisfyAll = $mustSatisfyAll;
    }

    /**
     * @return Contracts\Connection\Condition[]|Contracts\Connection\ConditionGroup[]|array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return int
     */
    public function getConditionCount()
    {
        return count($this->conditions);
    }

    public function isStrict()
    {
        return $this->mustSatisfyAll;
    }

    public function isLoose()
    {
        return ! $this->mustSatisfyAll;
    }

    public function asStrict()
    {
        $this->mustSatisfyAll = true;

        return $this;
    }

    public function asLoose()
    {
        $this->mustSatisfyAll = false;

        return $this;
    }
}
