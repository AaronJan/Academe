<?php

namespace Academe\Database\MongoDB;

use Academe\Constant\ConnectionConstant;
use Academe\Database\MongoDB\Contracts\MongoDBQuery as MongoDBQueryContract;

class MongoDBQuery implements MongoDBQueryContract
{
    /**
     * @var string
     */
    protected $collection;

    /**
     * @var string
     */
    protected $operation;

    /**
     * @var
     */
    protected $parameters;

    /**
     * @var bool
     */
    protected $hasChange;

    /**
     * @var array
     */
    protected $hint;

    /**
     * MongoDBQuery constructor.
     *
     * @param $operation
     * @param $collection
     * @param $parameters
     * @param $hasChange
     * @param array $hint
     */
    public function __construct($operation, $collection, $parameters, $hasChange, $hint = [])
    {
        $this->collection = $collection;
        $this->operation = $operation;
        $this->parameters = $parameters;
        $this->hasChange = $hasChange;
        $this->hint = $hint;
    }

    /**
     * @return int
     */
    public function getConnectionType()
    {
        return ConnectionConstant::TYPE_MONGODB;
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return bool
     */
    public function hasChange()
    {
        return $this->hasChange;
    }

    /**
     * @return array
     */
    public function getHint()
    {
        return $this->hint;
    }
}
