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
     * @param MySQLQueryContract $query
     * @return mixed
     */
    public function run(MySQLQueryContract $query)
    {
        $connection = $this->connection;

        $method = $this->getExecuteMethod($query->getOperation());

        return call_user_func_array([__CLASS__, $method], [$connection, $query]);
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