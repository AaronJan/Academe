<?php

namespace Academe\Database\MySQL;

use Academe\Contracts\Connection\Query;
use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Connection\Builder;
use Academe\Database\BaseConnection;
use Academe\Database\MySQL\Contracts\MySQLQuery as MySQLQueryContract;

class MySQLConnection extends BaseConnection
{
    /**
     * @var mixed
     */
    protected $config;

    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * @var string
     */
    protected $databaseName;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $readDBALConnection;

    /**
     * @var \Doctrine\DBAL\Connection|null
     */
    protected $DBALConnection = null;

    /**
     * @var int
     */
    protected $transactionCount = 0;

    /**
     * MySQLConnection constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->setConfig($config);

        $this->setupTablePrefix($config);
        $this->setupDatabaseName($config);
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
     *
     */
    public function connect()
    {
        $this->DBALConnection = $this->makeDBALConnection($this->getConfig());
    }

    /**
     *
     */
    public function close()
    {
        $this->transactionCount = 0;

        $DBALConnection = $this->getDBALConnection();

        if ($DBALConnection) {
            $DBALConnection->close();
        }
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
        $DBALConnection = $this->getDBALConnection();

        if (is_null($DBALConnection)) {
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
    protected function setupTablePrefix($config)
    {
        if (isset($config['prefix'])) {
            $this->tablePrefix = $config['prefix'];
        }
    }

    /**
     * @param $config
     */
    protected function setupDatabaseName($config)
    {
        $this->databaseName = $config['database'];
    }

    /**
     * @return \Doctrine\DBAL\Connection|null
     */
    public function getDriver()
    {
        return $this->getDBALConnection();
    }

    /**
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @param $config
     * @return \Doctrine\DBAL\Connection
     */
    protected function makeDBALConnection($config)
    {
        return \Doctrine\DBAL\DriverManager::getConnection(
            $this->getDBALConnectionParams($config),
            new \Doctrine\DBAL\Configuration()
        );
    }

    /**
     * @param $config
     * @return array
     */
    protected function getDBALConnectionParams($config)
    {
        return isset($config['unix_socket']) ?
            $this->getUnixDomainSocketDBALConnectionParams($config) :
            $this->getHostDBALConnectionParams($config);
    }

    /**
     * @param $config
     * @return array
     */
    protected function getUnixDomainSocketDBALConnectionParams($config)
    {
        return [
            'dbname'        => $config['database'],
            'user'          => $config['username'],
            'password'      => $config['password'],
            'unix_socket'   => $config['unix_socket'],
            'charset'       => isset($config['charset']) ? $config['charset'] : 'utf8',
            'driver'        => 'pdo_mysql',
            'driverOptions' => isset($config['pdo_options']) ? $config['pdo_options'] : [],
        ];
    }

    /**
     * @param $config
     * @return array
     */
    protected function getHostDBALConnectionParams($config)
    {
        return [
            'dbname'        => $config['database'],
            'user'          => $config['username'],
            'password'      => $config['password'],
            'host'          => $config['host'],
            'port'          => isset($config['port']) ? $config['port'] : 3306,
            'charset'       => isset($config['charset']) ? $config['charset'] : 'utf8',
            'driver'        => 'pdo_mysql',
            'driverOptions' => isset($config['pdo_options']) ? $config['pdo_options'] : [],
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Connection::TYPE_MYSQL;
    }

    /**
     * @param \Academe\Contracts\Connection\Query|MySQLQueryContract $query
     * @return mixed
     */
    public function run(Query $query)
    {
        $result = $this->interpretQuery($query);

        $this->logQuery($query, $result['elapsed']);

        return $result['data'];
    }

    /**
     * @param \Academe\Database\MySQL\Contracts\MySQLQuery $query
     * @return mixed
     */
    protected function interpretQuery(MySQLQueryContract $query)
    {
        return MySQLQueryInterpreter::run($this, $query);
    }

    /**
     * @param Query|MySQLQueryContract $query
     * @param                          $elapsedTime
     */
    protected function logQuery(Query $query, $elapsedTime)
    {
        if ($this->logQuery) {
            $this->queryLogs[] = [
                'SQL'   => $query->getSQL(),
                'binds' => $query->getParameters(),
                'time'  => $elapsedTime,
            ];
        }
    }

    /**
     * @return \Doctrine\DBAL\Connection|null
     */
    public function getDBALConnection()
    {
        return $this->DBALConnection;
    }

    /**
     * @return Builder
     */
    public function makeBuilder()
    {
        return new MySQLBuilder($this->tablePrefix);
    }

    /**
     * @return void
     */
    public function beginTransaction()
    {
        ++ $this->transactionCount;

        $this->connectIfMissingConnection();

        $this->getDBALConnection()->beginTransaction();
    }

    /**
     * @return void
     */
    public function commitTransaction()
    {
        -- $this->transactionCount;

        $this->getDBALConnection()->commit();
    }

    /**
     * @return void
     */
    public function rollBackTransaction()
    {
        -- $this->transactionCount;

        $this->getDBALConnection()->rollBack();
    }
}
