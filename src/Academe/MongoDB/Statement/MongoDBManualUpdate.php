<?php

namespace Academe\MongoDB\Statement;

use Academe\Contracts\CastManager;
use Academe\MongoDB\Statement\Operations;
use Academe\MongoDB\Contracts\Operation;

/**
 * Class MongoDBAdvanceUpdateOperation
 *
 * @package Academe\Statement\MongoDB
 */
class MongoDBManualUpdate
{
    /**
     * @var Operation[]
     */
    protected $operations = [];

    /**
     * @var bool
     */
    protected $useUpsert = false;

    /**
     * @param \Academe\MongoDB\Contracts\Operation $operation
     */
    protected function addOperation(Operation $operation)
    {
        $this->operations[] = $operation;
    }

    /**
     * @return Operation[]
     */
    protected function getOperations()
    {
        return $this->operations;
    }

    /**
     * @param      $field
     * @param null $amount
     * @return $this
     */
    public function increment($field, $amount = null)
    {
        $operation = new Operations\Increment($field, $amount);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $num
     * @return $this
     */
    public function multiply($field, $num)
    {
        $operation = new Operations\Multiply($field, $num);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $newName
     * @return $this
     */
    public function rename($field, $newName)
    {
        $operation = new Operations\Rename($field, $newName);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function setOnInsert($field, $value)
    {
        $operation = new Operations\SetOnInsert($field, $value);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function set($field, $value)
    {
        $operation = new Operations\Set($field, $value);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $fields
     * @return $this
     */
    public function unsetField($fields)
    {
        $operation = new Operations\UnsetField($fields);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function min($field, $value)
    {
        $operation = new Operations\Min($field, $value);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function max($field, $value)
    {
        $operation = new Operations\Max($field, $value);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param      $field
     * @param bool $useDate
     * @return $this
     */
    public function currentDate($field, $useDate = true)
    {
        $operation = new Operations\CurrentDate($field, $useDate);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param       $field
     * @param mixed $value
     * @return $this
     */
    public function addToSet($field, $value)
    {
        $operation = new Operations\AddToSet($field, [$value]);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param       $field
     * @param array $values
     * @return $this
     */
    public function addToSetEach($field, array $values)
    {
        $operation = new Operations\AddToSet($field, $values);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param      $field
     * @param bool $first
     * @return $this
     */
    public function pop($field, $first = true)
    {
        $operation = new Operations\Pop($field, $first);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $values
     * @return $this
     */
    public function pullAll($field, $values)
    {
        $operation = new Operations\PullAll($field, $values);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function pull($field, $value)
    {
        $operation = new Operations\Pull($field, $value);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param      $field
     * @param      $value
     * @param null $sort
     * @param null $slice
     * @param null $position
     * @return $this
     */
    public function push($field, $value, $sort = null, $slice = null, $position = null)
    {
        $operation = new Operations\Push($field, [$value], $sort, $slice, $position);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * @param      $field
     * @param      $values
     * @param null $sort
     * @param null $slice
     * @param null $position
     * @return $this
     */
    public function pushBatch($field, $values, $sort = null, $slice = null, $position = null)
    {
        $operation = new Operations\Push($field, $values, $sort, $slice, $position);

        $this->addOperation($operation);

        return $this;
    }

    /**
     * Compile to MongoDB update method parameters.
     *
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compileToUpdateParameters($connectionType, CastManager $castManager = null)
    {
        $update  = [];
        $options = [];

        foreach ($this->getOperations() as $operation) {
            $each = $operation->compile($connectionType, $castManager);

            $operator = key($each);
            $content  = $each[$operator];

            $update = $this->mergeWhenExisted($update, $operator, $content);
        }

        if ($this->getUseUpsert()) {
            $options['upsert'] = true;
        }

        return [
            'update'  => $update,
            'options' => $options,
        ];
    }

    /**
     * @param $array
     * @param $key
     * @param $content
     * @return mixed
     */
    protected function mergeWhenExisted($array, $key, $content)
    {
        if (isset($array[$key])) {
            $merged = array_merge($array[$key], $content);
        } else {
            $merged = $content;
        }

        $array[$key] = $merged;

        return $array;
    }

    /**
     * @return $this
     */
    public function asUpsert()
    {
        $this->useUpsert = true;

        return $this;
    }

    /**
     * @return bool
     */
    protected function getUseUpsert()
    {
        return $this->useUpsert;
    }

}
