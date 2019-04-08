<?php

namespace Academe\Accumulations;

use Academe\Casting\CasterMaker;
use Academe\Contracts\Caster;

class Min extends BaseAccumulation
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
}
