<?php

namespace Academe;

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
     * Transaction constructor.
     *
     * @param null $isolationLevel
     */
    public function __construct($isolationLevel = null)
    {
        $this->isolationLevel = $isolationLevel;
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

        if (! in_array($connection, $this->connections, true)) {
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
        }

        return true;
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
        }

        return true;
    }
}