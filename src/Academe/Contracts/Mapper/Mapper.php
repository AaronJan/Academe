<?php

namespace Academe\Contracts\Mapper;

use Academe\Contracts\Academe;
use Academe\Contracts\Connection\Connection;
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
     * @return \Academe\Statement\MapperStatement
     */
    public function query();

    /**
     * @return \Academe\MongoDB\Statement\MapperStatement
     */
    public function queryAsMongoDB();

}
