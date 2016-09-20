<?php

namespace Academe\Contracts\Mapper;

use Academe\Contracts\Academe;
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
     * @param array                                        $fields
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\All
     */
    public function makeAllInstruction(array $fields, ConditionGroup $conditionGroup);

    /**
     * @param                                              $field
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\Count
     */
    public function makeCountInstruction($field, ConditionGroup $conditionGroup);

    /**
     * @param array $attributes
     * @return \Academe\Contracts\Mapper\Instructions\Create
     */
    public function makeCreateInstruction(array $attributes);

    /**
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\Delete
     */
    public function makeDeleteInstruction(ConditionGroup $conditionGroup);

    /**
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\Exists
     */
    public function makeExistsInstruction(ConditionGroup $conditionGroup);

    /**
     * @param array                                        $fields
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\First
     */
    public function makeFirstInstruction(array $fields, ConditionGroup $conditionGroup);

    /**
     * @param                                              $page
     * @param                                              $perPage
     * @param array                                        $fields
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\Paginate
     */
    public function makePaginateInstruction($page,
                                            $perPage,
                                            array $fields,
                                            ConditionGroup $conditionGroup);

    /**
     * @param                                              $limit
     * @param array                                        $fields
     * @param                                              $offset
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\Segment
     */
    public function makeSegmentInstruction($limit,
                                           array $fields,
                                           $offset,
                                           ConditionGroup $conditionGroup);

    /**
     * @param array                                        $attributes
     * @param \Academe\Contracts\Connection\ConditionGroup $conditionGroup
     * @return \Academe\Contracts\Mapper\Instructions\Update
     */
    public function makeUpdateInstruction(array $attributes,
                                          ConditionGroup $conditionGroup);

}
