<?php

namespace Academe\Relation\Handlers;

use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\Academe;
use Academe\Relation\WithManyPredefined;
use Academe\Support\ArrayHelper;

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
     * @param array[]|mixed $entities
     * @return array[]|mixed
     */
    public function associate($entities)
    {
        $groupDictionary = $this->buildDictionaryForGroup($this->relation->getPredefined(), $this->localKey);

        foreach ($entities as $entity) {
            $children = [];

            foreach ($entity[$this->foreignKey] as $childKey) {
                if (isset($groupDictionary[$childKey])) {
                    $children[] = $groupDictionary[$childKey];
                }
            }

            $entity[$this->relationName] = $children;
        }

        return $entities;
    }

    /**
     * @param array|mixed $entities
     * @param             $key
     * @return array
     */
    protected function buildDictionaryForGroup($entities, $key)
    {
        $dictionary = [];

        foreach ($entities as $entity) {
            $dictionary[$entity[$key]][] = $entity;
        }

        return $dictionary;
    }

    /**
     * @param                            $entities
     * @param \Closure                   $constrain
     * @param \Academe\Contracts\Academe $academe
     * @param array                      $nestedRelations
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations)
    {
        // do nothing, is it awesome?

        return $this;
    }

}