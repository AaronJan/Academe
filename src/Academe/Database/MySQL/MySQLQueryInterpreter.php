<?php

namespace Academe\Database\MySQL;

use Academe\Contracts\Receipt;
use Academe\Database\BaseQueryInterpreter;
use Academe\Database\MySQL\Contracts\MySQLQuery as MySQLQueryContract;
use Academe\Exceptions\LogicException;

/**
 * Class MySQLOperator
 *
 * @package Academe\Database\MySQL
 */
class MySQLQueryInterpreter extends BaseQueryInterpreter
{
    static protected $operationToMethodMap = [
        'select'    => 'runSelect',
        'insert'    => 'runInsert',
        'update'    => 'runUpdate',
        'delete'    => 'runDelete',
        'aggregate' => 'runAggregate',
    ];

    /**
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    static public function run(MySQLConnection $connection, MySQLQueryContract $query)
    {
        $method = static::getMethodForOperation($query->getOperation());

        $connection->connectIfMissingConnection();

        $startTime = microtime(true);
        $result = static::getQueryResult($method, $connection, $query);
        $elapsedTime = static::getElapsedTime($startTime);

        return [
            'data'    => $result,
            'elapsed' => $elapsedTime,
        ];
    }

    /**
     * @param                                              $method
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    static protected function getQueryResult($method,
                                             MySQLConnection $connection,
                                             MySQLQueryContract $query
    ) {
        try {
            $result = static::performDatabaseQuery($method, $connection, $query);
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($connection->isTransactionActive()) {
                throw $e;
            }

            $result = static::tryAgainIfCausedByLostConnection(
                $e, $method, $connection, $query
            );
        }

        return $result;
    }

    /**
     * @param \Doctrine\DBAL\DBALException $e
     * @param                                              $method
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    static protected function tryAgainIfCausedByLostConnection(\Doctrine\DBAL\DBALException $e,
                                                               $method,
                                                               MySQLConnection $connection,
                                                               MySQLQueryContract $query
    ) {
        if (static::isCausedByLostConnection($e->getPrevious())) {
            $connection->reconnect();

            return static::performDatabaseQuery($method, $connection, $query);
        }

        throw $e;
    }

    /**
     * From Laravel.
     *
     * @param \Exception $e
     * @return bool
     */
    static protected function isCausedByLostConnection(\Exception $e)
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
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     */
    static protected function performDatabaseQuery($method,
                                                   MySQLConnection $connection,
                                                   MySQLQueryContract $query
    ) {
        return call_user_func(
            [__CLASS__, $method],
            $connection, $query
        );
    }

    /**
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return array
     */
    static public function runSelect(MySQLConnection $connection, MySQLQueryContract $query)
    {
        $DBALConnection = $connection->getDBALConnection();

        $records = $DBALConnection
            ->executeQuery($query->getSQL(), $query->getParameters())
            ->fetchAll();

        return $records;
    }

    /**
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return array
     */
    static public function runAggregate(MySQLConnection $connection, MySQLQueryContract $query)
    {
        $DBALConnection = $connection->getDBALConnection();

        $queryHint = $query->getHint();

        if (! isset($queryHint['field'])) {
            throw new LogicException('[Internal Error] Aggregate action must contain a `field` hint.');
        }

        $result = $DBALConnection
            ->executeQuery($query->getSQL(), $query->getParameters())
            ->fetch();

        return [$queryHint['field'] => $result['aggregation']];
    }

    /**
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return Receipt
     */
    static public function runInsert(MySQLConnection $connection, MySQLQueryContract $query)
    {
        $DBALConnection = $connection->getDBALConnection();

        $result = $DBALConnection
            ->executeQuery($query->getSQL(), $query->getParameters());

        return new MySQLReceipt($DBALConnection, $result->rowCount());
    }

    /**
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     */
    static public function runUpdate(MySQLConnection $connection, MySQLQueryContract $query)
    {
        $DBALConnection = $connection->getDBALConnection();

        $result = $DBALConnection
            ->executeQuery($query->getSQL(), $query->getParameters());

        return $result->rowCount();
    }

    /**
     * @param \Academe\Database\MySQL\MySQLConnection $connection
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $command
     * @return mixed
     */
    static public function runDelete(MySQLConnection $connection, MySQLQueryContract $command)
    {
        $DBALConnection = $connection->getDBALConnection();

        $result = $DBALConnection
            ->executeQuery($command->getSQL(), $command->getParameters());

        return $result->rowCount();
    }

}