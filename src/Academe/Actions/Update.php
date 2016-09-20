<?php

namespace Academe\Actions;

use Academe\Contracts\Connection\Action;
use Academe\Actions\Traits\BeCondtionable;
use Academe\Contracts\Conditionable;
use Academe\Contracts\Connection\ConditionGroup;

class Update implements Action, Conditionable
{
    use BeCondtionable;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Update constructor.
     *
     * @param ConditionGroup|null $conditionGroup
     * @param array               $attributes
     */
    public function __construct(ConditionGroup $conditionGroup, array $attributes)
    {
        $this->conditionGroup = $conditionGroup;
        $this->attributes     = $attributes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'update';
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return [$this->conditionGroup, $this->attributes];
    }

}
