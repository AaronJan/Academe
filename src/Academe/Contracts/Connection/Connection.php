<?php

namespace Academe\Contracts\Connection;

interface Connection
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return mixed
     */
    public function getDriver();

    /**
     * @return string
     */
    public function getDatabaseName();

    /**
     * @param \Academe\Contracts\Connection\Query $query
     * @return mixed
     */
    public function run(Query $query);

    /**
     * @return Builder
     */
    public function makeBuilder();

    /**
     * @return void
     */
    public function connect();

    /**
     * @return void
     */
    public function close();

    /**
     * @return void
     */
    public function reconnect();

    /**
     * @return bool
     */
    public function isTransactionActive();

    /**
     * @return void
     */
    public function beginTransaction();

    /**
     * @return void
     */
    public function commitTransaction();

    /**
     * @return void
     */
    public function rollBackTransaction();

    /**
     *
     */
    public function enableQueryLog();

    /**
     *
     */
    public function disableQueryLog();

    /**
     * @return array
     */
    public function getQueryLogs();

}
