<?php

namespace Academe\Actions;

use Academe\Contracts\Connection\Action;
use Academe\Actions\Traits\BeCondtionable;
use Academe\Contracts\Conditionable;
use Academe\Contracts\Connection\ConditionGroup;

class Delete implements Action, Conditionable
{
    use BeCondtionable;

    public function __construct(ConditionGroup $conditionGroup)
    {
        $this->conditionGroup = $conditionGroup;
    }

    public function getName()
    {
        return 'delete';
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return [$this->conditionGroup];
    }
}
