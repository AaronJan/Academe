<?php

namespace Acadmee\Accumulations;

use Academe\Casting\CasterMaker;
use Academe\Contracts\Caster;

class Max extends BaseAccumulation
{
    /**
     * @var string
     */
    protected $field;

    public function __construct(string $field)
    {
        $this->field = $field;
        $this->caster = CasterMaker::decimal();
    }

    public function getField()
    {
        return $this->field;
    }

    /**
     * @return Caster
     */
    public function getCaster()
    {
        return $this->caster;
    }
}
