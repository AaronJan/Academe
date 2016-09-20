<?php

namespace Academe\Contracts\Connection;

use Academe\Contracts\CastManager;

interface Builder
{
    /**
     * @param                                     $subject
     * @param Action                              $action
     * @param \Academe\Contracts\CastManager      $castManager
     * @return \Academe\Contracts\Connection\Query
     */
    public function parse($subject, Action $action, CastManager $castManager = null);
}
