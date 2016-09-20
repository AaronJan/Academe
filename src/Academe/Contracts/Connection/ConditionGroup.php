<?php

namespace Academe\Contracts\Connection;

interface ConditionGroup
{
    /**
     * @return Condition[]
     */
    public function getConditions();

    /**
     * @return int
     */
    public function getConditionCount();

    /**
     * @return bool
     */
    public function isStrict();

    public function asStrict();

    /**
     * @return bool
     */
    public function isLoose();

    public function asLoose();

}
