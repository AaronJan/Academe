<?php

namespace Academe;

use Academe\Constant\ConnectionConstant;
use Academe\Contracts\Academe as AcademeContract;
use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Mapper\Mapper as MapperContract;
use Academe\Contracts\Mapper\Blueprint;
use Academe\Contracts\Connection\Builder;
use Academe\Contracts\Mapper\Executable;
use Academe\Exceptions\BadMethodCallException;
use Academe\Relation\Contracts\Relation;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Casting\CastManager;
use Academe\Statement\MapperStatement;
use Academe\MongoDB\Statement\MapperStatement as MongoDBMapperStatement;

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
     * @param Executable $executable
     * @return mixed|array
     */
    public function execute(Executable $executable)
    {
        $result = $executable->execute($this);

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
     * @return \Academe\Statement\MapperStatement
     */
    public function query()
    {
        return new MapperStatement(
            $this,
            $this->getAcademe()->getConditionMaker()
        );
    }

    /**
     * @return \Academe\MongoDB\Statement\MapperStatement
     */
    public function queryAsMongoDB()
    {
        $this->throwIfConnectionNotCapable(ConnectionConstant::TYPE_MONGODB);

        return new MongoDBMapperStatement(
            $this,
            $this->getAcademe()->getConditionMaker()
        );
    }

    /**
     * @param $connetionType
     */
    protected function throwIfConnectionNotCapable($connetionType)
    {
        if ($connetionType != $this->getConnection()->getType()) {
            throw new BadMethodCallException("Mapper doesn't support this type of connection");
        }
    }

    /**
     * @param array $records
     * @return mixed[]
     */
    public function convertRecords(array $records)
    {
        $model = $this->getBlueprint()->model();

        return array_map(function ($record) use ($model) {
            return $model->newInstance($record);
        }, $records);
    }

    /**
     * @param $record
     * @return mixed
     */
    public function convertRecord($record)
    {
        return $this->getBlueprint()->model()->newInstance($record);
    }

    /**
     * @param \Academe\Transaction $transaction
     * @return bool
     */
    public function involve(Transaction $transaction)
    {
        return $transaction->involveConnection($this->getConnection());
    }

}
