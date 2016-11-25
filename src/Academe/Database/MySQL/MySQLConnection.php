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
     * @var \Doctrine\DBAL\Connection
     */
    protected $writeDBALConnection;

    /**
     * MySQLConnection constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->setupTablePrefix($config);
        $this->setupDatabaseName($config);
        $this->setupDBALConnection($config);
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
     * @return array
     */
    public function getDriver()
    {
        return [
            'read'  => $this->readDBALConnection,
            'write' => $this->readDBALConnection,
        ];
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
     */
    protected function setupDBALConnection($config)
    {
        $this->writeDBALConnection = $this->makeDBALConnection($config);

        if (isset($config['read'])) {
            $this->readDBALConnection = $this->makeDBALConnection(array_merge($config, $config['read']));
        } else {
            $this->readDBALConnection = $this->writeDBALConnection;
        }
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
        $startTime = microtime(true);

        $operator = $this->makeOperator($query);
        $result   = $operator->run($query);

        $elapsedTime = $this->getElapsedTime($startTime);

        $this->logQuery($query, $elapsedTime);

        return $result;
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
     * @param MySQLQueryContract $query
     * @return MySQLOperator
     */
    public function makeOperator(MySQLQueryContract $query)
    {
        return new MySQLOperator($this->getDBALConnection($query->hasChange()));
    }

    /**
     * @param $hasChange
     * @return \Doctrine\DBAL\Connection
     */
    public function getDBALConnection($hasChange)
    {
        return $hasChange ?
            $this->writeDBALConnection :
            $this->readDBALConnection;
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
        $DBALConnection = $this->getDBALConnection(true);
        $DBALConnection->beginTransaction();
    }

    /**
     * @return void
     */
    public function commitTransaction()
    {
        $DBALConnection = $this->getDBALConnection(true);
        $DBALConnection->commit();
    }

    /**
     * @return void
     */
    public function rollBackTransaction()
    {
        $DBALConnection = $this->getDBALConnection(true);
        $DBALConnection->rollBack();
    }
}
