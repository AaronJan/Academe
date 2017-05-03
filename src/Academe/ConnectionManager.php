<?php

namespace Academe;

use Academe\Contracts\Connection\Connection;
use Academe\Exceptions\ConfigurationException;

class ConnectionManager
{
    /**
     * @var array
     */
    protected $driverToConnectionClass = [
        'mysql'   => \Academe\Database\MySQL\MySQLConnection::class,
        'mongodb' => \Academe\Database\MongoDB\MongoDBConnection::class,
    ];

    /**
     * @var string
     */
    protected $defaultConnectionName;

    /**
     * @var array
     */
    protected $connectionConfig;

    /**
     * @var array
     */
    protected $connections;

    /**
     * Connector constructor.
     *
     * @param $connectionConfig
     */
    public function __construct($connectionConfig, $defaultConnectionName)
    {
        $this->connectionConfig      = $connectionConfig;
        $this->defaultConnectionName = $defaultConnectionName;
    }

    /**
     * @param null|string $name
     * @return Connection
     */
    public function connect($name = null)
    {
        $name = $name ?: $this->defaultConnectionName;

        if (! isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name, $this->getConnectionConfig($name));
        }

        return $this->connections[$name];
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getConnectionConfig($name)
    {
        if (! isset($this->connectionConfig[$name])) {
            $message = "Undefined connection name [{$name}], check your configure.";

            throw new ConfigurationException($message);
        }

        return $this->connectionConfig[$name];
    }

    /**
     * @param string $name
     * @param array  $config
     * @return Connection
     * @throws \Exception
     */
    protected function makeConnection($name, $config)
    {
        $connectionClass = $this->getConnectionClass($config['type']);

        try {
            $connection = new $connectionClass($name, $config);
        } catch (\Exception $e) {
            throw $e;
        }

        return $connection;
    }

    /**
     * @param $type
     * @return string
     */
    protected function getConnectionClass($type)
    {
        if (! isset($this->driverToConnectionClass[$type])) {
            $message = "Database type [{$type}] is not supported.";

            throw new ConfigurationException($message);
        }

        return $this->driverToConnectionClass[$type];
    }


}
