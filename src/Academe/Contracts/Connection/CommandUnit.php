<?php

namespace Academe\Contracts\Connection;

interface CommandUnit
{
    /**
     * @return int
     */
    public function getConnectionType();

    /**
     * @return mixed
     */
    public function getRaw();
}
