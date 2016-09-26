<?php

namespace Academe;

use Academe\Contracts\Academe as AcademeContract;
use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Mapper\Blueprint;
use Academe\Contracts\Mapper\Mapper;
use Academe\Exceptions\ErrorException;
use Academe\Relation\Contracts\Bond;
use Academe\Relation\Contracts\RelationManager;
use Academe\Relation\Managers\ManyToManyRelationManager;
use Academe\Statement\Writer;

class Academe implements AcademeContract
{
    /**
     * @var AcademeContract
     */
    static protected $instance;

    /**
     * @var null|ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var ConditionMaker
     */
    protected $conditionMaker;

    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var Blueprint[]
     */
    protected $blueprints = [];

    /**
     * @var Bond[]
     */
    protected $bonds = [];

    /**
     * @var Mapper[]
     */
    protected $mappers = [];

    /**
     * @var RelationManager[]
     */
    protected $relationManagers = [];

    /**
     * Academe constructor.
     *
     * @param $config
     */
    protected function __construct($config)
    {
        $this->initializeConnectionManager($config['connections'], $config['default_connection']);
        $this->initializeCondtionMaker();
        $this->initializeWriter();
    }

    /**
     * @return \Academe\Statement\Writer
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @param $config
     * @return \Academe\Contracts\Academe
     * @throws \Academe\Exceptions\ErrorException
     */
    static public function initialize($config)
    {
        if (static::$instance !== null) {
            throw new ErrorException('Academe is already initialized.');
        }

        static::$instance = new static($config);

        return static::$instance;
    }

    /**
     * @return \Academe\Contracts\Academe
     */
    static public function getInstance()
    {
        return static::$instance;
    }

    /**
     * @param $connections
     * @param $defaultConnection
     */
    protected function initializeConnectionManager($connections, $defaultConnection)
    {
        $this->connectionManager = new ConnectionManager($connections, $defaultConnection);
    }

    /**
     *
     */
    protected function initializeCondtionMaker()
    {
        $this->conditionMaker = new \Academe\ConditionMaker($this);
    }

    /**
     *
     */
    protected function initializeWriter()
    {
        $this->writer = new Writer($this->getConditionMaker());
    }

    /**
     * @return \Academe\Contracts\ConditionMaker
     */
    public function getConditionMaker()
    {
        return $this->conditionMaker;
    }

    /**
     * @param $bondClass
     * @return RelationManager
     */
    public function getRelationManager($bondClass)
    {
        if (! isset($this->relationManagers[$bondClass])) {
            $bond         = $this->getBond($bondClass);
            $managerClass = $bond->managerClass() ?: ManyToManyRelationManager::class;

            $this->relationManagers[$bondClass] = new $managerClass(
                $bond,
                $this
            );
        }

        return $this->relationManagers[$bondClass];
    }

    /**
     * @param null $name
     * @return \Academe\Contracts\Connection\Connection
     */
    public function getConnection($name = null)
    {
        return $this->connectionManager->connect($name);
    }

    /**
     * @param $class
     * @return \Academe\Contracts\Mapper\Blueprint
     */
    public function getBlueprint($class)
    {
        if (! isset($this->blueprints[$class])) {
            $this->blueprints[$class] = new $class();
        }

        return $this->blueprints[$class];
    }

    /**
     * @param $class
     * @return \Academe\Relation\Contracts\Bond
     */
    public function getBond($class)
    {
        if (! isset($this->bonds[$class])) {
            $this->bonds[$class] = new $class();
        }

        return $this->bonds[$class];
    }

    /**
     * @param $blueprintClass
     * @return \Academe\Contracts\Mapper\Mapper
     */
    public function getMapper($blueprintClass)
    {
        if (! isset($this->mappers[$blueprintClass])) {
            $blueprint   = $this->getBlueprint($blueprintClass);
            $mapperClass = $blueprint->mapperClass() ?: \Academe\Mapper::class;

            $this->mappers[$blueprintClass] = new $mapperClass(
                $this,
                $blueprint,
                $this->getConnection($blueprint->connectionName())
            );
        }

        return $this->mappers[$blueprintClass];
    }

}