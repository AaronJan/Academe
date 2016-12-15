<?php

namespace Academe\Database\MongoDB;

use Academe\Contracts\Receipt;
use Academe\Database\BaseQueryInterpreter;
use Academe\Database\MongoDB\Contracts\MongoDBQuery as MongoDBQueryContract;
use MongoDB\Driver\Exception\ConnectionTimeoutException;

/**
 * Class MongoDBOperator
 *
 * @package Academe\Database\MongoDB
 */
class MongoDBQueryInterpreter extends BaseQueryInterpreter
{
    static protected $operationToMethodMap = [
        'find'       => 'runFind',
        'aggregate'  => 'runAggregate',
        'insertone'  => 'runInsertOne',
        'updatemany' => 'runUpdateMany',
        'deletemany' => 'runDeleteMany',
    ];

    /**
     * @param MongoDBQueryContract $query
     * @return array
     */
    static public function run(MongoDBConnection $connection, MongoDBQueryContract $query)
    {
        $connection->connectIfMissingConnection();

        $method = static::getMethodForOperation($query->getOperation());

        $startTime = microtime(true);

        try {
            $result = static::performDatabaseQuery($method, $connection, $query);
        } catch (ConnectionTimeoutException $e) {
            if ($connection->isTransactionActive()) {
                throw $e;
            }

            $connection->reconnect();

            $result = static::performDatabaseQuery($method, $connection, $query);
        }

        $elapsedTime = static::getElapsedTime($startTime);

        return [
            'data'    => $result,
            'elapsed' => $elapsedTime,
        ];
    }

    /**
     * @param                                                  $method
     * @param \Academe\Database\MongoDB\MongoDBConnection      $connection
     * @param \Academe\Database\MongoDB\Contracts\MongoDBQuery $query
     * @return mixed
     */
    static protected function performDatabaseQuery($method,
                                                   MongoDBConnection $connection,
                                                   MongoDBQueryContract $query)
    {
        return call_user_func(
            [__CLASS__, $method],
            $connection, $query
        );
    }

    /**
     * @param \Academe\Database\MongoDB\MongoDBConnection      $connection
     * @param \Academe\Database\MongoDB\Contracts\MongoDBQuery $query
     * @return mixed
     */
    static public function runFind(MongoDBConnection $connection, MongoDBQueryContract $query)
    {
        $databaseHandler = $connection->getDatabaseHandler();

        $collection = $databaseHandler->selectCollection($query->getCollection());

        $cursor = call_user_func_array([$collection, 'find'], $query->getParameters());

        return $cursor->toArray();
    }

    /**
     * @param \Academe\Database\MongoDB\MongoDBConnection      $connection
     * @param \Academe\Database\MongoDB\Contracts\MongoDBQuery $query
     * @return number
     */
    static public function runAggregate(MongoDBConnection $connection, MongoDBQueryContract $query)
    {
        $databaseHandler = $connection->getDatabaseHandler();

        $collection = $databaseHandler->selectCollection($query->getCollection());

        $cursor = call_user_func_array([$collection, 'aggregate'], $query->getParameters());

        $result = $cursor->toArray();

        return empty($result) ? 0 : $result[0]->value;
    }

    /**
     * @param \Academe\Database\MongoDB\MongoDBConnection      $connection
     * @param \Academe\Database\MongoDB\Contracts\MongoDBQuery $query
     * @return Receipt
     */
    static public function runInsertOne(MongoDBConnection $connection, MongoDBQueryContract $query)
    {
        $databaseHandler = $connection->getDatabaseHandler();

        $collection = $databaseHandler->selectCollection($query->getCollection());

        $insertOneResult = call_user_func_array([$collection, 'insertOne'], $query->getParameters());

        return new MongoDBReceipt($insertOneResult);
    }

    /**
     * @param \Academe\Database\MongoDB\MongoDBConnection      $connection
     * @param \Academe\Database\MongoDB\Contracts\MongoDBQuery $query
     * @return int
     */
    static public function runUpdateMany(MongoDBConnection $connection, MongoDBQueryContract $query)
    {
        $databaseHandler = $connection->getDatabaseHandler();

        $collection = $databaseHandler->selectCollection($query->getCollection());

        $updateResult = call_user_func_array([$collection, 'updateMany'], $query->getParameters());

        return $updateResult->getModifiedCount();
    }

    /**
     * @param \Academe\Database\MongoDB\MongoDBConnection      $connection
     * @param \Academe\Database\MongoDB\Contracts\MongoDBQuery $query
     * @return int
     */
    static public function runDeleteMany(MongoDBConnection $connection, MongoDBQueryContract $query)
    {
        $databaseHandler = $connection->getDatabaseHandler();

        $collection = $databaseHandler->selectCollection($query->getCollection());

        $deleteResult = call_user_func_array([$collection, 'deleteMany'], $query->getParameters());

        return $deleteResult->getDeletedCount();
    }

}