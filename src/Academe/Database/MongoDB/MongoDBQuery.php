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
     * MongoDBQuery constructor.
     *
     * @param      $operation
     * @param      $collection
     * @param      $parameters
     * @param      $hasChange
     */
    public function __construct($operation, $collection, $parameters, $hasChange)
    {
        $this->collection = $collection;
        $this->operation  = $operation;
        $this->parameters = $parameters;
        $this->hasChange  = $hasChange;
    }

    /**
     * @return int
     */
    public function getConnectionType()
    {
        return ConnectionConstant::TYPE_MONGODB;
    }

    /**
     * @return array
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

}
