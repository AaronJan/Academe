<?php

namespace Academe;

use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Academe as AcademeContract;
use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Mapper\Mapper as MapperContract;
use Academe\Contracts\Mapper\Blueprint;
use Academe\Contracts\Connection\Builder;
use Academe\Contracts\Mapper\Executable;
use Academe\Contracts\Transaction;
use Academe\Exceptions\BadMethodCallException;
use Academe\Relation\Contracts\Relation;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Casting\CastManager;
use Academe\Statement\MapperStatement;
use Academe\Contracts\Connection\Condition;

class Mapper implements MapperContract
{
    /**
     * @var AcademeContract
     */
    protected $academe;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Relation[]
     */
    protected $relations;

    /**
     * @var Contracts\ConditionMaker
     */
    protected $conditionMaker;

    /**
     * @var \Academe\Contracts\CastManager
     */
    protected $castManager;

    /**
     * Storage constructor.
     *
     * @param AcademeContract $academe
     * @param Blueprint       $blueprint
     * @param Connection      $connection
     */
    public function __construct(AcademeContract $academe,
                                Blueprint $blueprint,
                                Connection $connection)
    {
        $this->academe        = $academe;
        $this->blueprint      = $blueprint;
        $this->connection     = $connection;
        $this->conditionMaker = $academe->getConditionMaker();
        $this->primaryKey     = $blueprint->primaryKey();
        $this->castManager    = $this->makeCastManager($blueprint->castRules());
        $this->relations      = $this->blueprint->relations();
    }

    /**
     * @param array $castRules
     * @return \Academe\Contracts\CastManager
     */
    protected function makeCastManager(array $castRules)
    {
        return new CastManager($castRules);
    }

    /**
     * @return \Academe\Contracts\Academe
     */
    public function getAcademe()
    {
        return $this->academe;
    }

    /**
     * @param Transaction|Transaction[] $transactions
     */
    public function involve($transactions)
    {
        if (! is_array($transactions)) {
            $transactions = [$transactions];
        }

        $connection = $this->getConnection();

        /**
         * @var $transactions Transaction[]
         */
        foreach ($transactions as $transaction) {
            $transaction->begin($connection);
        }
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return Blueprint|Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param Executable $instruction
     * @return mixed
     */
    public function execute(Executable $instruction)
    {
        $result = $instruction->execute($this);

        return $result;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->blueprint->subject();
    }

    /**
     * @return \Academe\Contracts\CastManager
     */
    public function getCastManager()
    {
        return $this->castManager;
    }

    /**
     * @return \Academe\Relation\Contracts\Relation[]|array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param $relationName
     * @return RelationHandler
     * @throws BadMethodCallException
     */
    public function relation($relationName)
    {
        if (! isset($this->relations[$relationName])) {
            throw new BadMethodCallException("Relation [{$relationName}] not found.");
        }

        $relationHandler = $this->relations[$relationName]->makeHandler(
            $this,
            $relationName,
            $this->academe
        );

        return $relationHandler;
    }

    /**
     * @param null|Condition|ConditionGroup $condition
     * @return \Academe\Statement\MapperStatement
     */
    public function queryWith($condition = null)
    {
        return new MapperStatement(
            $this,
            $this->getAcademe()->getConditionMaker(),
            $condition
        );
    }

    /**
     * @return \Academe\Statement\MapperStatement
     */
    public function query()
    {
        return new MapperStatement(
            $this,
            $this->getAcademe()->getConditionMaker()
        );
    }

}
