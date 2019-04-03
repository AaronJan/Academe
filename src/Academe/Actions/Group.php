<?php

namespace Academe\Actions;

use Academe\Contracts\Connection\Action;
use Academe\Actions\Traits\BeCondtionable;
use Academe\Actions\Traits\BeLockable;
use Academe\Contracts\Action\Conditionable;

class Group implements Action, Conditionable
{
    use BeCondtionable, BeLockable;

    /**
     * @var array
     */
    protected $aggregation;

    /**
     * @var array
     */
    protected $values;

    /**
     * Group constructor.
     *
     * @param array $aggregation
     * @param array $values
     */
    public function __construct($aggregation, $values)
    {
        $this->aggregation = $aggregation;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return [$this->aggregation, $this->values];
    }
}
