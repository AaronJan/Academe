<?php

namespace Academe\Accumulations;

use Academe\Casting\CasterMaker;
use Academe\Contracts\Caster;
use Academe\Contracts\Accumulation;

abstract class BaseAccumulation implements Accumulation
{
    /**
     * @var null|string
     */
    protected $field;

    /**
     * @var Caster
     */
    protected $caster;

    public function asInteger()
    {
        $this->caster = CasterMaker::integer();

        return $this;
    }

    public function asDecimal()
    {
        $this->caster = CasterMaker::decimal()();

        return $this;
    }

    /**
     * @return Caster
     */
    public function getCaster()
    {
        return $this->caster;
    }

    public function getField()
    {
        return $this->field;
    }
}

