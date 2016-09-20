<?php

namespace Academe\Instructions\Traits;

trait Sortable
{
    /**
     * @var array
     */
    protected $orders = [];

    /**
     * @param $field
     * @param $direction
     * @return $this
     */
    public function sort($field, $direction)
    {
        $this->orders[] = [$field, $direction];

        return $this;
    }
}