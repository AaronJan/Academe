<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\HasMany;
use Academe\Model;

class HasManyRelationHandler extends HasOneOrManyRelationHandler
{
    /**
     * HasManyRelationHandler constructor.
     *
     * @param \Academe\Relation\HasMany        $relation
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param                                  $relationName
     */
    public function __construct(HasMany $relation, Mapper $hostMapper, $relationName)
    {
        $this->relation     = $relation;
        $this->hostMapper   = $hostMapper;
        $this->relationName = $relationName;
        $this->foreignKey   = $relation->getForeignKey();
        $this->localKey     = $relation->getLocalKey();
    }

    /**
     * @param Model[] $entities
     * @return Model[]
     */
    public function associate($entities)
    {
        return $this->associateByType($entities, 'many');
    }

}