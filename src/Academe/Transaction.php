<?php

namespace Academe;

use Academe\Constant\InstructionConstant;
use Academe\Constant\TransactionConstant;
use Academe\Contracts\Connection\Connection;

class Transaction
{
    /**
     * @var \Academe\Contracts\Connection\Connection[]
     */
    protected $startedConnections = [];

    /**
     * @var \Academe\Contracts\Connection\Connection[]
     */
    protected $connections = [];

    /**
     * @var string[]
     */
    protected $involdedConnectionRecord = [];

    /**
     * @var bool
     */
    protected $isStarted = false;

    /**
     * @var bool
     */
    protected $isExecuted = false;

    /**
     * @var null|int
     */
    protected $isolationLevel = null;

    /**
     * @var int|null
     */
    protected $lockLevel = TransactionConstant::LOCK_UNSET;

    /**
     * Transaction constructor.
     *
     * @param null $isolationLevel
     * @param int  $lockLevel
     */
    public function __construct($isolationLevel = null, $lockLevel = TransactionConstant::LOCK_UNSET)
    {
        $this->isolationLevel = $isolationLevel;

    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLockLevel($level)
    {
        $this->lockLevel = $level;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLockLevel()
    {
        return $this->lockLevel;
    }

    /**
     * @return \Academe\Transaction
     */
    public function lockSelect()
    {
        return $this->setLockLevel(TransactionConstant::LOCK_FOR_UPDATE);
    }

    /**
     * @return \Academe\Transaction
     */
    public function lockSelectForShare()
    {
        return $this->setLockLevel(TransactionConstant::LOCK_FOR_SHARE);
    }

    /**
     * @return int|null
     */
    public function getIsolationLevel()
    {
        return $this->isolationLevel;
    }

    /**
     *
     */
    protected function mustNotBeExecuted()
    {
        if ($this->isDone()) {
            throw new \LogicException("Transaction already executed.");
        }
    }

    /**
     *
     */
    protected function mustNotBeStarted()
    {
        if ($this->isActive()) {
            throw new \LogicException("Transaction already started.");
        }
    }

    /**
     *
     */
    protected function mustBeStarted()
    {
        if (! $this->isActive()) {
            throw new \LogicException("Transaction hasn't started yet");
        }
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @return bool
     */
    public function involveConnection(Connection $connection)
    {
        $this->mustNotBeStarted();

        $connectionName = $connection->getName();

        if (! isset($this->involdedConnectionRecord[$connectionName])) {
            $id = $connection->rememberTransaction($this);

            $this->involdedConnectionRecord[$connectionName] = $id;

            $this->connections[] = $connection;

            return true;
        }

        return false;
    }

    /**
     *
     */
    public function begin()
    {
        $this->mustNotBeStarted();

        if ($this->isActive()) {
            throw new \LogicException("Transaction has already started.");
        }

        $isolationLevel = $this->getIsolationLevel();

        foreach ($this->connections as $connection) {
            $connection->beginTransaction($isolationLevel);
        }

        $this->isStarted = true;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isStarted;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->isExecuted;
    }

    /**
     * @return bool
     */
    public function commit()
    {
        $this->mustBeStarted();
        $this->mustNotBeExecuted();

        foreach ($this->connections as $connection) {
            $connection->commitTransaction();
            $this->forgetTransaction($connection);
        }

        return true;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     */
    protected function forgetTransaction(Connection $connection)
    {
        $connection->forgetTransaction(
            $this->getTransactionIdForConnection($connection->getName())
        );
    }

    /**
     * @param $connectionName
     * @return int
     */
    protected function getTransactionIdForConnection($connectionName)
    {
        return $this->involdedConnectionRecord[$connectionName];
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        $this->mustBeStarted();
        $this->mustNotBeExecuted();

        foreach ($this->connections as $connection) {
            $connection->rollBackTransaction();
            $this->forgetTransaction($connection);
        }

        return true;
    }
}