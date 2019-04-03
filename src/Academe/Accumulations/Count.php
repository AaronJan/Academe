<?php

namespace Acadmee\Accumulations;

use Academe\Casting\CasterMaker;
use Academe\Contracts\Caster;

class Count extends BaseAccumulation
{
    public function __construct()
    {
        $this->caster = CasterMaker::integer();
    }
}
