<?php

namespace Academe\Contracts\Action;

use Academe\Contracts\Connection\ConditionGroup;

interface Conditionable
{
    /**
     * @param ConditionGroup $conditionGroup
     * @return $this
     */
    public function setConditionGroup(ConditionGroup $conditionGroup);

    /**
     * @return ConditionGroup|null
     */
    public function getConditionGroup();
}
