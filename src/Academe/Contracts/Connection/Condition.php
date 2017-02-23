<?php

namespace Academe\Contracts\Connection;

use Academe\Contracts\CastManager;

interface Condition
{
    public function getParameters();

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return \Academe\Contracts\Connection\Query
     */
    public function parse($connectionType, CastManager $castManager = null);
}
