<?php

namespace Academe\Relation\Handlers;

use Academe\Constant\TransactionConstant;
use Academe\Contracts\Academe;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\HasMany;
use Academe\Relation\HasOne;
use Academe\Support\ArrayHelper;

/**
 * Class HasOneOrManyRelationHandler
 *
 * @package Academe\Relation\Handlers
 */
abstract class HasOneOrManyRelationHandler extends BaseRelationHandler
{
    /**
     * @var HasOne|HasMany
     */
    protected $relation;

    /**
     * @var Mapper
     */
    protected $hostMapper;

    /**
     * @var ConditionGroup|null
     */
    protected $conditionGroup = null;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var array
     */
    protected $groupedResults = [];

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    /**
     * @var string
     */
    protected $relationName;

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

        $childKeyAttributes = array_map(function ($entity) use ($localKey) {
            return ArrayHelper::get($entity, $localKey);
        }, $entities);

        $childMapper = $academe->getMapper($this->relation->getChildBlueprintClass());

        $fluentStatement = $this->makeLimitedFluentStatement($academe)
            ->in($foreignKey, $childKeyAttributes);

        $constrain($fluentStatement);

        $executable = $fluentStatement
            ->upgrade()
            ->setLockLevel($lockLevel)
            ->with($nestedRelations)
            ->all();

        $this->results = $childMapper->execute($executable);
        $this->loaded  = true;

        return $this;
    }

    /**
     * @param array[]|mixed  $entities
     * @param                $type
     * @return array[]|mixed
     */
    public function associateByType($entities, $type)
    {
        $dictionary = $this->buildDictionaryForGroup();

        foreach ($entities as $entity) {
            $key                         = ArrayHelper::get($entity, $this->localKey);
            $entity[$this->relationName] = $this->getRelationResult($dictionary, $key, $type);
        }

        return $entities;
    }

    /**
     * @return array
     */
    protected function buildDictionaryForGroup()
    {
        $dictionary = [];
        $foreignKey = $this->foreignKey;

        foreach ($this->results as $result) {
            $dictionary[$result[$foreignKey]][] = $result;
        }

        return $dictionary;
    }

    /**
     * @param        $dictionary
     * @param        $key
     * @param string $type "one" or "many"
     * @return array|null
     */
    protected function getRelationResult($dictionary, $key, $type)
    {
        if (! isset($dictionary[$key])) {
            return $type === 'one' ? null : [];
        }

        $result = $dictionary[$key];

        return $type === 'one' ? reset($result) : $result;
    }

}