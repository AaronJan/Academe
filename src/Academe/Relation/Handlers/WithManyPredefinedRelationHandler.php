<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\Academe;
use Academe\Relation\WithManyPredefined;
use Academe\Support\ArrayHelper;
use Academe\Model;

class WithManyPredefinedRelationHandler extends BaseRelationHandler
{
    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    /**
     * @var \Academe\Relation\WithManyPredefined
     */
    protected $relation;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * WithManyPredefinedRelationHandler constructor.
     *
     * @param \Academe\Relation\WithManyPredefined $relation
     * @param \Academe\Contracts\Mapper\Mapper     $hostMapper
     * @param                                      $relationName
     */
    public function __construct(WithManyPredefined $relation, Mapper $hostMapper, $relationName)
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
        $dictionary = $this->buildDictionary($this->relation->getPredefined(), $this->localKey);

        foreach ($entities as $entity) {
            $children = [];

            foreach (ArrayHelper::get($entity, $this->foreignKey) as $childKey) {
                if (isset($dictionary[$childKey])) {
                    $children[] = $dictionary[$childKey];
                }
            }

            $entity->setRelation($this->relationName, $children);
        }

        return $entities;
    }

    /**
     * @param array|mixed $entities
     * @param             $key
     * @return array
     */
    protected function buildDictionary($entities, $key)
    {
        $dictionary = [];

        foreach ($entities as $entity) {
            $dictionary[$entity[$key]] = $entity;
        }

        return $dictionary;
    }

    /**
     * @param                            $entities
     * @param \Closure                   $constrain
     * @param \Academe\Contracts\Academe $academe
     * @param array                      $nestedRelations
     * @param int                        $lockLevel
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations,
                                $lockLevel = 0)
    {
        // do nothing, is it awesome?

        return $this;
    }

}