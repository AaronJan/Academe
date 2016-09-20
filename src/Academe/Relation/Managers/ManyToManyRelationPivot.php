<?php

namespace Academe\Relation\Managers;

use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\RelationPivot;

class ManyToManyRelationPivot implements RelationPivot
{
    /**
     * @var \Academe\Contracts\Mapper\Mapper
     */
    protected $pivotMapper;

    /**
     * @var string
     */
    protected $mainKey;

    /**
     * @var string
     */
    protected $attachedKey;

    /**
     * ManyToManyPivotHandler constructor.
     *
     * @param \Academe\Contracts\Mapper\Mapper $pivotMapper
     * @param                                  $mainKey
     * @param                                  $attachedKey
     */
    public function __construct(Mapper $pivotMapper, $mainKey, $attachedKey)
    {
        $this->pivotMapper = $pivotMapper;
        $this->mainKey     = $mainKey;
        $this->attachedKey = $attachedKey;
    }

    /**
     * @param       $hostPrimary
     * @param       $guestPrimary
     * @param array $additionAttributes
     */
    public function attachByKey($hostPrimary, $guestPrimary, $additionAttributes = [])
    {
        $mainAttributes = [
            $this->mainKey     => $hostPrimary,
            $this->attachedKey => $guestPrimary,
        ];

        $this->createOrUpdate($mainAttributes, $additionAttributes);
    }

    /**
     * @param $hostPrimary
     * @param $guestPrimaries
     * @return int
     */
    public function detachByKeys($hostPrimary, $guestPrimaries)
    {
        $pivotMapper = $this->getPivotMapper();

        $conditions = [
            $pivotMapper->equal($this->mainKey, $hostPrimary),
            $pivotMapper->in($this->attachedKey, $guestPrimaries),
        ];

        return $pivotMapper->execute($pivotMapper->delete($conditions));
    }

    /**
     * @param array $mainAttributes
     * @param array $additionAttributes
     */
    protected function createOrUpdate($mainAttributes, $additionAttributes)
    {
        $pivotMapper = $this->getPivotMapper();

        $entity = $pivotMapper->execute($pivotMapper->first(
            ['*'],
            $this->makeEqualityConditionsFromAttributes($mainAttributes, $pivotMapper)
        ));

        if ($entity) {
            $primaryKey = $pivotMapper->getPrimaryKey();

            $this->updateBySpecificKey(
                $primaryKey,
                $entity[$primaryKey],
                $additionAttributes
            );
        } else {
            $pivotMapper->execute($pivotMapper->create(array_merge($additionAttributes, $mainAttributes)));
        }
    }

    /**
     * @param       $primaryKey
     * @param       $primaryKeyValue
     * @param array $attributes
     * @return int
     */
    protected function updateBySpecificKey($primaryKey, $primaryKeyValue, array $attributes)
    {
        if (count($attributes) === 0) {
            return 0;
        }

        $pivotMapper = $this->getPivotMapper();

        $instruction = $pivotMapper->update([
            $pivotMapper->equal($primaryKey, $primaryKeyValue),
        ], $attributes);

        return $pivotMapper->execute($instruction);
    }

    /**
     * @param $hostPrimary
     * @return int
     */
    public function detachAll($hostPrimary)
    {
        $pivotMapper = $this->getPivotMapper();

        return $pivotMapper->execute($pivotMapper->delete([
            $pivotMapper->equal($this->mainKey, $hostPrimary),
        ]));
    }

    /**
     * @param      $hostPrimary
     * @param      $guestPrimaries
     * @param bool $detaching
     * @return array
     */
    public function syncByKeys($hostPrimary, $guestPrimaries, $detaching = true)
    {
        $changes         = [
            'attached' => [],
            'detached' => [],
            'updated'  => [],
        ];
        $pivotMapper     = $this->getPivotMapper();
        $pivotPrimaryKey = $pivotMapper->getPrimaryKey();

        $currentEntities = $pivotMapper->execute($pivotMapper->all(
            [$pivotPrimaryKey, $this->attachedKey],
            [$pivotMapper->equal($this->mainKey, $hostPrimary)]
        ));

        $currentList = $this->makeList($currentEntities, $pivotPrimaryKey, $this->attachedKey);

        $keyValueList = $this->formatSyncList($guestPrimaries);

        $detach = array_diff($currentList, array_keys($keyValueList));

        if ($detaching && count($detach) > 0) {
            $this->detachByKeys($hostPrimary, $detach);
            $changes['detached'] = $detach;
        }

        $changes = array_merge(
            $changes,
            $this->attachNewOrUpdateExists($hostPrimary, $currentList, $keyValueList)
        );

        return $changes;
    }

    /**
     * @param array $records
     * @return array
     */
    protected function formatSyncList($records)
    {
        $result = [];

        foreach ($records as $id => $attributes) {
            if (! is_array($attributes)) {
                list($id, $attributes) = [$attributes, []];
            }

            $result[$id] = $attributes;
        }

        return $result;
    }

    /**
     * @param $entities
     * @param $key
     * @param $field
     * @return array
     */
    protected function makeList($entities, $key, $field)
    {
        $list = [];

        foreach ($entities as $entity) {
            $list[$entity[$key]] = $entity[$field];
        }

        return $list;
    }

    /**
     * @param $mainKeyValue
     * @param $currentList
     * @param $keyValueList
     * @return array
     */
    protected function attachNewOrUpdateExists($mainKeyValue, $currentList, $keyValueList)
    {
        $changes = [
            'attached' => [],
            'updated'  => [],
        ];

        foreach ($keyValueList as $id => $attributes) {
            if (! in_array($id, $currentList)) {
                $this->attachByKey($mainKeyValue, $id, $attributes);
                $changes['attached'][] = $id;
            } elseif (count($attributes) > 0) {
                if ($this->updateExistingPivot($mainKeyValue, $id, $attributes)) {
                    $changes['attached'][] = $id;
                }
            }
        }

        return $changes;
    }

    /**
     * @param       $mainKeyValue
     * @param       $attachedKeyValue
     * @param array $additionAttributes
     * @return int
     */
    protected function updateExistingPivot($mainKeyValue, $attachedKeyValue, $additionAttributes = [])
    {
        $pivotMapper = $this->getPivotMapper();

        $conditions = $this->makeEqualityConditionsFromAttributes([
            $this->mainKey     => $mainKeyValue,
            $this->attachedKey => $attachedKeyValue,
        ], $pivotMapper);

        $updated = $pivotMapper->execute($pivotMapper->update(
            $conditions,
            $additionAttributes
        ));

        return $updated;
    }

    /**
     * @return \Academe\Contracts\Mapper\Mapper
     */
    protected function getPivotMapper()
    {
        return $this->pivotMapper;
    }

    /**
     * @param                                   $attributes
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     * @return Condition[]
     */
    protected function makeEqualityConditionsFromAttributes($attributes, ConditionMaker $conditionMaker)
    {
        $conditions = [];

        foreach ($attributes as $attribute => $value) {
            $conditions[] = $conditionMaker->equal($attribute, $value);
        }

        return $conditions;
    }

}


