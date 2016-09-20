<?php

namespace Academe\Contracts\Connection;

interface Query
{
    /**
     * @return int
     */
    public function getConnectionType();

    /**
     * @return bool
     */
    public function hasChange();
}
