<?php

namespace Academe\Actions\Traits;

use Academe\Contracts\Connection\ConditionGroup;

trait BeCondtionable
{
    /**
     * @var ConditionGroup|null
     */
    protected $conditionGroup = null;

    /**
     * @param ConditionGroup $conditionGroup
     * @return $this
     */
    public function setConditionGroup(ConditionGroup $conditionGroup)
    {
        $this->conditionGroup = $conditionGroup;

        return $this;
    }

    /**
     * @return ConditionGroup|null
     */
    public function getConditionGroup()
    {
        return $this->conditionGroup;
    }

}