<?php

namespace Academe\Database;

use Academe\Contracts\Connection\Connection;

abstract class BaseConnection implements Connection
{
    /**
     * @var bool
     */
    protected $logQuery = false;

    /**
     * @var array
     */
    protected $queryLogs = [];

    /**
     *
     */
    public function enableQueryLog()
    {
        $this->logQuery = true;
    }

    /**
     *
     */
    public function disableQueryLog()
    {
        $this->logQuery = false;
    }

    /**
     * @return array
     */
    public function getQueryLogs()
    {
        return $this->queryLogs;
    }

    /**
     * @param $startTime
     * @return float
     */
    protected function getElapsedTime($startTime)
    {
        return round((microtime(true) - $startTime) * 1000, 2);
    }
}