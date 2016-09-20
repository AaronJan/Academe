<?php

namespace Academe\Contracts\Connection;

interface ConditionGroup
{
    /**
     * @return Condition[]
     */
    public function getConditions();

    /**
     * @return bool
     */
    public function isStrict();

    public function asStrict();

    /**
     * @return bool
     */
    public function isLenient();

    public function asLenient();

}
