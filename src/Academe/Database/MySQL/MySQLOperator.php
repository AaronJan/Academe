<?php

namespace Academe\Database\MySQL;

use Academe\Database\BaseOperator;
use Academe\Database\MySQL\Contracts\MySQLQuery as MySQLQueryContract;
use Doctrine\DBAL\Connection as DBALConnection;

/**
 * Class MySQLOperator
 *
 * @package Academe\Database\MySQL
 */
class MySQLOperator extends BaseOperator
{
    static protected $methodMap = [
        'select'    => 'runSelect',
        'insert'    => 'runInsert',
        'update'    => 'runUpdate',
        'delete'    => 'runDelete',
        'aggregate' => 'runAggregate',
    ];

    /**
     * @var DBALConnection
     */
    protected $connection;

    /**
     * MongoDBOperator constructor.
     *
     * @param DBALConnection $connection
     */
    public function __construct(DBALConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run(MySQLQueryContract $query)
    {
        $connection = $this->getConnection();

        $method = $this->getExecuteMethod($query->getOperation());

        try {
            $result = $this->invokeQueryMethod($method, $connection, $query);
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($connection->isTransactionActive()) {
                throw $e;
            }

            $result = $this->tryAgainIfCausedByLostConnection(
                $e, $method, $connection, $query
            );
        }

        return $result;
    }

    /**
     * @param \Doctrine\DBAL\DBALException                 $e
     * @param                                              $method
     * @param \Doctrine\DBAL\Connection                    $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function tryAgainIfCausedByLostConnection(\Doctrine\DBAL\DBALException $e,
                                                        $method,
                                                        \Doctrine\DBAL\Connection $connection,
                                                        MySQLQueryContract $query)
    {
        if ($this->isCausedByLostConnection($e->getPrevious())) {
            $connection->close();
            $connection->connect();

            return $this->invokeQueryMethod($method, $connection, $query);
        }

        throw $e;
    }

    /**
     * From Laravel.
     *
     * @param \Exception $e
     * @return bool
     */
    protected function isCausedByLostConnection(\Exception $e)
    {
        $message = $e->getMessage();

        $possibleErrorMessages = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
        ];

        foreach ($possibleErrorMessages as $errorMessage) {
            if (mb_strpos($message, $errorMessage) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param                                              $method
     * @param \Doctrine\DBAL\Connection                    $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     */
    protected function invokeQueryMethod($method,
                                         \Doctrine\DBAL\Connection $connection,
                                         MySQLQueryContract $query)
    {
        $callable = [__CLASS__, $method];

        return call_user_func_array($callable, [$connection, $query]);
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param MySQLQueryContract        $query
     * @return array
     */
    static public function runSelect(\Doctrine\DBAL\Connection $connection, MySQLQueryContract $query)
    {
        $records = $connection
            ->executeQuery($query->getSQL(), $query->getParameters())
            ->fetchAll();

        return $records;
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param MySQLQueryContract        $query
     * @return int|float
     */
    static public function runAggregate(\Doctrine\DBAL\Connection $connection, MySQLQueryContract $query)
    {
        $result = $connection
            ->executeQuery($query->getSQL(), $query->getParameters())
            ->fetch();

        return is_numeric($result['aggregation']) ?
            (1 * $result['aggregation']) :
            $result['aggregation'];
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param MySQLQueryContract        $query
     * @return mixed
     */
    static public function runInsert(\Doctrine\DBAL\Connection $connection, MySQLQueryContract $query)
    {
        $result = $connection
            ->executeQuery($query->getSQL(), $query->getParameters());

        if ($result) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param MySQLQueryContract        $query
     * @return int
     */
    static public function runUpdate(\Doctrine\DBAL\Connection $connection, MySQLQueryContract $query)
    {
        $result = $connection
            ->executeQuery($query->getSQL(), $query->getParameters())
            ->rowCount();

        return $result;
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param MySQLQueryContract        $command
     * @return int
     */
    static public function runDelete(\Doctrine\DBAL\Connection $connection, MySQLQueryContract $command)
    {
        $result = $connection
            ->executeQuery($command->getSQL(), $command->getParameters())
            ->rowCount();

        return $result;
    }

}