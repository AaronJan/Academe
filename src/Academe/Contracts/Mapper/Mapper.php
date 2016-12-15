<?php

namespace Academe\Contracts\Mapper;

use Academe\Contracts\Academe;
use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Connection\Connection;
use Academe\Contracts\Transaction;
use Academe\Exceptions\BadMethodCallException;
use Academe\Relation\Contracts\RelationHandler;

interface Mapper
{
    /**
     * @return Academe
     */
    public function getAcademe();

    /**
     * @return string
     */
    public function getPrimaryKey();

    /**
     * @return mixed
     */
    public function getSubject();

    /**
     * @return \Academe\Contracts\CastManager
     */
    public function getCastManager();

    /**
     * @return Connection
     */
    public function getConnection();

    /**
     * @return Blueprint
     */
    public function getBlueprint();

    /**
     * @return \Academe\Relation\Contracts\Relation[]|array
     */
    public function getRelations();

    /**
     * @param Transaction|Transaction[] $transactions
     */
    public function involve($transactions);

    /**
     * @param Executable $instruction
     * @return mixed
     */
    public function execute(Executable $instruction);

    /**
     * @param $relationName
     * @return RelationHandler
     * @throws BadMethodCallException
     */
    public function relation($relationName);

    /**
     * @param null|Condition|ConditionGroup $condition
     * @return \Academe\Statement\MapperStatement
     */
    public function queryWith($condition = null);

    /**
     * @return \Academe\Statement\MapperStatement
     */
    public function query();

}
