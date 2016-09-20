<?php

namespace Academe\Actions;

use Academe\Contracts\Connection\Action;

class Insert implements Action
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * Create constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getName()
    {
        return 'insert';
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return [$this->attributes];
    }
}
