<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Mapper\Mapper;
use Academe\Entity;
use Academe\Relation\HasMany;

class HasManyRelationHandler extends HasOneOrManyRelationHandler
{
    /**
     * HasManyRelationHandler constructor.
     *
     * @param HasMany       $relation
     * @param               $relationName
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
     * @param Entity[]|mixed $entities
     * @return Entity[]|mixed
     */
    public function associate($entities)
    {
        return $this->associateByType($entities, 'many');
    }

}