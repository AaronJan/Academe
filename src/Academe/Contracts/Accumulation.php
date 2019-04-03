<?php

namespace Academe\Contracts;

use Academe\Contracts\Caster;

interface Accumulation {
    public function asInteger();

    public function asDecimal();

    /**
     * @return Caster
     */
    public function getCaster();

    public function getField();
}