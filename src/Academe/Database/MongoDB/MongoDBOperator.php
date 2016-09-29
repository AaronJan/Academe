<?php

namespace Academe\Database\MongoDB;

use Academe\Database\BaseOperator;
use Academe\Database\MongoDB\Contracts\MongoDBQuery as MongoDBQueryContract;
use \MongoDB\Database as MongoDBDatabase;

/**
 * Class MongoDBOperator
 *
 * @package Academe\Database\MongoDB
 */
class MongoDBOperator extends BaseOperator
{
    static protected $methodMap = [
        'find'       => 'runFind',
        'aggregate'  => 'runAggregate',
        'insertone'  => 'runInsertOne',
        'updatemany' => 'runUpdateMany',
        'deletemany' => 'runDeleteMany',
    ];

    /**
     * @var MongoDBConnection
     */
    protected $databaseHandler;

    /**
     * MongoDBOperator constructor.
     *
     * @param MongoDBDatabase $databaseHandler
     */
    public function __construct(MongoDBDatabase $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
    }

    /**
     * @param MongoDBQueryContract $query
     * @return mixed
     */
    public function run(MongoDBQueryContract $query)
    {
        $databaseHandler = $this->databaseHandler;

        $method = $this->getExecuteMethod($query->getOperation());

        return call_user_func_array([__CLASS__, $method], [$databaseHandler, $query]);
    }

    /**
     * @param MongoDBDatabase      $databaseHandler
     * @param MongoDBQueryContract $query
     * @return mixed
     */
    static public function runFind(MongoDBDatabase $databaseHandler, MongoDBQueryContract $query)
    {
        $collection = $databaseHandler->selectCollection($query->getCollection());

        $cursor = call_user_func_array([$collection, 'find'], $query->getParameters());

        return $cursor->toArray();
    }

    /**
     * @param MongoDBDatabase      $databaseHandler
     * @param MongoDBQueryContract $query
     * @return mixed
     */
    static public function runAggregate(MongoDBDatabase $databaseHandler, MongoDBQueryContract $query)
    {
        $collection = $databaseHandler->selectCollection($query->getCollection());

        $cursor = call_user_func_array([$collection, 'aggregate'], $query->getParameters());

        $result = $cursor->toArray();

        return empty($result) ? 0 : $result[0]->value;
    }

    /**
     * @param MongoDBDatabase      $databaseHandler
     * @param MongoDBQueryContract $query
     * @return mixed
     */
    static public function runInsertOne(MongoDBDatabase $databaseHandler, MongoDBQueryContract $query)
    {
        $collection = $databaseHandler->selectCollection($query->getCollection());

        $insertOneResult = call_user_func_array([$collection, 'insertOne'], $query->getParameters());

        return $insertOneResult->getInsertedId();
    }

    /**
     * @param MongoDBDatabase      $databaseHandler
     * @param MongoDBQueryContract $query
     * @return int
     */
    static public function runUpdateMany(MongoDBDatabase $databaseHandler, MongoDBQueryContract $query)
    {
        $collection = $databaseHandler->selectCollection($query->getCollection());

        $updateResult = call_user_func_array([$collection, 'updateMany'], $query->getParameters());

        return $updateResult->getModifiedCount();
    }

    /**
     * @param MongoDBDatabase      $databaseHandler
     * @param MongoDBQueryContract $query
     * @return int
     */
    static public function runDeleteMany(MongoDBDatabase $databaseHandler, MongoDBQueryContract $query)
    {
        $collection = $databaseHandler->selectCollection($query->getCollection());

        $deleteResult = call_user_func_array([$collection, 'deleteMany'], $query->getParameters());

        return $deleteResult->getDeletedCount();
    }

}