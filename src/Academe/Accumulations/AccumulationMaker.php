<?php

namespace Academe\Accumulations;

class AccumulationMaker
{
    /**
     * @return Count
     */
    public function count()
    {
        return new Count();
    }

    public function sum(string $field)
    {
        return new Sum($field);
    }

    public function avg(string $field)
    {
        return new Avg($field);
    }

    public function min(string $field)
    {
        return new Min($field);
    }

    public function max(string $field)
    {
        return new Max($field);
    }
}
