<?php

namespace Academe\Database;

use Academe\Contracts\Connection\Connection;
use Academe\Transaction;

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
     * @var Transaction[]
     */
    protected $transactions = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

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
     * @return int|null
     */
    public function getTransactionSelectLockLevel()
    {
        $transaction = $this->getRecentActiveTransaction();

        return $transaction ?
            $transaction->getLockLevel() :
            null;
    }

    /**
     * @return \Academe\Transaction|null
     */
    protected function getRecentActiveTransaction()
    {
        foreach (array_reverse($this->transactions) as $transaction) {
            /**
             * @var $transaction Transaction
             */

            if ($transaction->isActive()) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * @param \Academe\Transaction $transaction
     * @return int
     */
    public function rememberTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        end($this->transactions);

        return key($this->transactions);
    }

    /**
     * @param $id
     */
    public function forgetTransaction($id)
    {
        unset($this->transactions[$id]);
    }

}