<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Relation\HasOne;

class HasOneRelationHandler extends HasOneOrManyRelationHandler implements RelationHandler
{
    /**
     * HasOneRelationHandler constructor.
     *
     * @param HasOne                           $relation
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param                                  $relationName
     */
    public function __construct(HasOne $relation, Mapper $hostMapper, $relationName)
    {
        $this->relation     = $relation;
        $this->hostMapper   = $hostMapper;
        $this->relationName = $relationName;
        $this->foreignKey   = $relation->getForeignKey();
        $this->localKey     = $relation->getLocalKey();
    }

    /**
     * @param array[] $entities
     * @return array[]
     */
    public function associate($entities)
    {
        return $this->associateByType($entities, 'one');
    }

}