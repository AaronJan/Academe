<?php

namespace Academe\Database\MongoDB;

use Academe\Constant\ConnectionConstant;
use Academe\Contracts\Connection\Query;
use Academe\Contracts\Connection\Builder;
use Academe\Database\BaseConnection;
use Academe\Database\MongoDB\Contracts\MongoDBQuery as MongoDBQueryContract;
use MongoDB\Database as MongoDBDatabase;

class MongoDBConnection extends BaseConnection
{
    /**
     * @var mixed
     */
    protected $config;

    /**
     * @var \MongoDB\Database|null
     */
    protected $databaseHandler = null;

    /**
     * @var string
     */
    protected $databaseName;

    /**
     * @var int
     */
    protected $transactionCount = 0;

    /**
     * MongoDBConnection constructor.
     *
     * @param string $name
     * @param        $config
     */
    public function __construct($name, $config)
    {
        $this->name = $name;

        $this->setConfig($config);
        $this->setupDatabaseName($config);
    }

    /**
     *
     */
    public function connect()
    {
        $config = $this->getConfig();

        $mongoDBClient = $this->makeMongoDBClient($config);

        $this->setDatabaseHandler(
            $mongoDBClient->selectDatabase($config['database'])
        );
    }

    /**
     * @param $config
     * @return \MongoDB\Client
     */
    protected function makeMongoDBClient($config)
    {
        $DSN     = $this->getMongoDBConnectionDSN($config);
        $options = isset($config['options']) ? $config['options'] : [];

        if (is_string($config['replica']) && $config['replica'] !== '') {
            $options['replicaSet'] = $config['replica'];
        }

        return new \MongoDB\Client($DSN, $options);
    }

    /**
     * @param \MongoDB\Database $databaseHandler
     */
    protected function setDatabaseHandler(MongoDBDatabase $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
    }

    /**
     *
     */
    public function close()
    {
        $this->transactionCount = 0;

        // Currently, there isn't a "close" operation in MongoDB library.
    }

    /**
     *
     */
    public function reconnect()
    {
        $this->close();
        $this->connect();
    }

    /**
     * Lazy connect.
     */
    public function connectIfMissingConnection()
    {
        $databaseHandler = $this->getDatabaseHandler();

        if (is_null($databaseHandler)) {
            $this->connect();
        }
    }

    /**
     * @return bool
     */
    public function isTransactionActive()
    {
        return $this->transactionCount > 0;
    }

    /**
     * @param $config
     */
    protected function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    protected function getConfig()
    {
        return $this->config;
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

        if (isset($config['authenticationDatabase'])) {
            $database = $config['authenticationDatabase'];
        } else {
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
        return ConnectionConstant::TYPE_MONGODB;
    }

    /**
     * @return \MongoDB\Database|null
     */
    public function getDatabaseHandler()
    {
        return $this->databaseHandler;
    }

    /**
     * @return \MongoDB\Database|null
     */
    public function getDriver()
    {
        $this->connectIfMissingConnection();

        return $this->databaseHandler;
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
        $result = $this->interpretQuery($query);

        $this->logQuery($query, $result['elapsed']);

        return $result['data'];
    }

    /**
     * @param \Academe\Database\MongoDB\Contracts\MongoDBQuery $query
     * @return mixed
     */
    protected function interpretQuery(MongoDBQueryContract $query)
    {
        return MongoDBQueryInterpreter::run($this, $query);
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
     * @return Builder
     */
    public function makeBuilder()
    {
        return new MongoDBBuilder();
    }

    /**
     * @param null|integer $isolationLevel
     * @return void
     */
    public function beginTransaction($isolationLevel = null)
    {
        ++ $this->transactionCount;

        // Nothing but make connection
        $this->connectIfMissingConnection();
    }

    /**
     * @return void
     */
    public function commitTransaction()
    {
        -- $this->transactionCount;

        // Do nothing
    }

    /**
     * @return void
     */
    public function rollBackTransaction()
    {
        -- $this->transactionCount;

        // Do nothing
    }

}

