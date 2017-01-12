<?php

namespace Academe\Statement\Traits;

use Academe\Contracts\ConditionMaker;

trait ManageConditionMaker
{
    /**
     * @var \Academe\Contracts\ConditionMaker|null
     */
    protected $conditionMaker;

    /**
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     */
    public function setConditionMaker(ConditionMaker $conditionMaker)
    {
        $this->conditionMaker = $conditionMaker;
    }

    /**
     * @return \Academe\Contracts\ConditionMaker
     */
    public function getConditionMaker()
    {
        return $this->conditionMaker;
    }
}