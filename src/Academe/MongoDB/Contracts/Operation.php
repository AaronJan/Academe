<?php

namespace Academe\MongoDB\Contracts;

use Academe\Contracts\CastManager;

interface Operation
{
    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null);
}