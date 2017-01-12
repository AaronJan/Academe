<?php

namespace Academe\Contracts\MongoDB;

use Academe\Contracts\CastManager;

interface Operation
{
    /**
     * @param string                         $connectionType
     * @param \Academe\Contracts\CastManager $castManager
     */
    public function cast($connectionType, CastManager $castManager);

    /**
     * @return array
     */
    public function compile();
}