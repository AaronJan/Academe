<?php

namespace Academe\Contracts\Connection;

use Academe\Transaction;

interface Connection
{
    /**
     * @return string
     */
    public function getName();

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
     * @param null|integer $isolationLevel
     * @return void
     */
    public function beginTransaction($isolationLevel = null);

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

    /**
     * @return int|null
     */
    public function getTransactionSelectLockLevel();

    /**
     * @param \Academe\Transaction $transaction
     * @return int
     */
    public function rememberTransaction(Transaction $transaction);

    /**
     * @param $id
     */
    public function forgetTransaction($id);

    /**
     * @return string
     */
    public function getSubjectPrefix();

}
