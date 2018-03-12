<?php

namespace Academe\Relation\Handlers;

use Academe\Constant\TransactionConstant;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\WithMany;
use Academe\Contracts\Academe;
use Academe\Support\ArrayHelper;
use Academe\Model;

class WithManyRelationHandler extends BaseRelationHandler
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
     * @var \Academe\Relation\WithMany
     */
    protected $relation;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * WithManyRelationHandler constructor.
     *
     * @param \Academe\Relation\WithMany       $relation
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param                                  $relationName
     */
    public function __construct(WithMany $relation, Mapper $hostMapper, $relationName)
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
        $dictionary = $this->buildDictionary($this->results, $this->localKey);

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
     * @param int|null                   $lockLevel
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations,
                                $lockLevel = TransactionConstant::LOCK_UNSET)
    {
        if ($this->loaded) {
            return $this;
        }

        $foreignKey = $this->foreignKey;
        $localKey   = $this->localKey;

        $childKeys = $this->getAllChildKeysFromHostEntities($entities, $foreignKey);

        $childMapper = $academe->getMapper($this->relation->getChildBlueprintClass());

        $fluentStatement = $this->applyConditionIfExisted(
            $this->makeLimitedFluentStatement($academe),
            $this->relation->getCondition()
        );

        $constrain($fluentStatement);

        $executable = $fluentStatement
            ->in($localKey, $childKeys)
            ->upgrade()
            ->setLockLevel($lockLevel)
            ->with($nestedRelations)
            ->all();

        $this->results = $childMapper->execute($executable);
        $this->loaded  = true;

        return $this;
    }

    /**
     * @param array|mixed $entities
     * @param             $foreignKey
     * @return array
     */
    protected function getAllChildKeysFromHostEntities($entities, $foreignKey)
    {
        $childKeys = ArrayHelper::flatten(
            ArrayHelper::map($entities, function ($entity) use ($foreignKey) {
                return (array) ArrayHelper::get($entity, $foreignKey);
            })
        );

        return array_values(array_unique($childKeys));
    }


}