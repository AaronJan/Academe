<?php

namespace Academe;

use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Transaction as TransactionContract;

class Transaction implements TransactionContract
{
    /**
     * @var Connection[]
     */
    protected $connections = [];

    /**
     * Transaction constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param Connection $connection
     */
    public function begin(Connection $connection)
    {
        if (!in_array($connection, $this->connections, true)) {
            $connection->beginTransaction();

            $this->connections[] = $connection;
        }
    }

    /**
     *
     */
    public function commit()
    {
        foreach ($this->connections as $connection) {
            $connection->commitTransaction();
        }
    }

    /**
     *
     */
    public function rollBack()
    {
        foreach ($this->connections as $connection) {
            $connection->rollBackTransaction();
        }
    }

}
