<?php

namespace Academe\Database\MongoDB;

use Academe\Contracts\Connection\Query;
use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Connection\Builder;
use Academe\Database\BaseConnection;
use Academe\Database\MongoDB\Contracts\MongoDBQuery as MongoDBQueryContract;

class MongoDBConnection extends BaseConnection
{
    /**
     * @var mixed
     */
    protected $config;

    /**
     * @var \MongoDB\Client
     */
    protected $mongoDBClient;

    /**
     * @var \MongoDB\Database
     */
    protected $databaseHandler;

    /**
     * @var string
     */
    protected $databaseName;

    /**
     * MongoDBConnection constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->setupDatabaseName($config);
        $this->setupMongoDBConnection($config);
    }

    /**
     * @param $config
     */
    protected function setupDatabaseName($config)
    {
        $this->databaseName = $config['database'];
    }

    /**
     * @param $config
     */
    protected function setupMongoDBConnection($config)
    {
        $DSN     = $this->getMongoDBConnectionDSN($config);
        $options = isset($config['options']) ? $config['options'] : [];

        $mongoDBClient = new \MongoDB\Client($DSN, $options);

        $this->mongoDBClient   = $mongoDBClient;
        $this->databaseHandler = $mongoDBClient->selectDatabase($config['database']);
    }

    /**
     * @param $config
     * @return string
     */
    protected function getMongoDBConnectionDSN($config)
    {
        $DSN = 'mongodb://';

        list($credentialDSN, $authenticationDatabase) = $this->getAuthenticationDSNs($config);

        $instanceDSN = $this->getInstanceDSN($config);

        $DSN .= "{$credentialDSN}{$instanceDSN}{$authenticationDatabase}";

        return $DSN;
    }

    /**
     * @param $config
     * @return array
     */
    protected function getAuthenticationDSNs($config)
    {
        $authentication = isset($config['authentication']) ?
            $config['authentication'] :
            [];

        $DSN      = '';
        $database = '';

        if (
            ! empty($authentication['username']) &&
            ! empty($authentication['password'])
        ) {
            $DSN = "{$authentication['username']}:{$authentication['password']}@";
        }

        if (isset($config['database'])) {
            $database = $config['database'];
        }

        return [$DSN, $database];
    }

    /**
     * @param $config
     * @return string
     */
    protected function getInstanceDSN($config)
    {
        $parts = array_map(function ($instance) {
            return $instance['host'] . (isset($instance['port']) ? ":{$instance['port']}" : '');
        }, $config['instances']);

        return implode(',', $parts) . '/';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Connection::TYPE_MONGODB;
    }

    /**
     * @return \MongoDB\Database
     */
    public function getDatabaseHandler()
    {
        return $this->databaseHandler;
    }

    /**
     * @return \MongoDB\Client
     */
    public function getDriver()
    {
        return $this->mongoDBClient;
    }

    /**
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @param Query|MongoDBQueryContract $query
     * @return mixed
     */
    public function run(Query $query)
    {
        $startTime = microtime(true);

        $operator = $this->makeOperator();
        $result   = $operator->run($query);

        $elapsedTime = $this->getElapsedTime($startTime);

        $this->logQuery($query, $elapsedTime);

        return $result;
    }

    /**
     * @param Query|MongoDBQueryContract $query
     * @param                            $elapsedTime
     */
    protected function logQuery(Query $query, $elapsedTime)
    {
        if ($this->logQuery) {
            $this->queryLogs[] = [
                'collection' => $query->getCollection(),
                'operation'  => $query->getOperation(),
                'parameters' => $query->getParameters(),
                'time'       => $elapsedTime,
            ];
        }
    }

    /**
     * @return MongoDBOperator
     */
    public function makeOperator()
    {
        return new MongoDBOperator($this->getDatabaseHandler());
    }

    /**
     * @return Builder
     */
    public function makeBuilder()
    {
        return new MongoDBBuilder();
    }

    /**
     * @return void
     */
    public function beginTransaction()
    {
        // Nothing
    }

    /**
     * @return void
     */
    public function commitTransaction()
    {
        // Nothing
    }

    /**
     * @return void
     */
    public function rollBackTransaction()
    {
        // Nothing
    }


}

