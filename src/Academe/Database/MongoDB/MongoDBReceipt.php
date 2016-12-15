<?php

namespace Academe\Database\MongoDB;

use Academe\BaseReceipt;
use MongoDB\InsertOneResult;

/**
 * Class MongoDBReceipt
 *
 * @package Academe\Database\MongoDB
 */
class MongoDBReceipt extends BaseReceipt
{
    /**
     * @var \MongoDB\InsertOneResult
     */
    protected $insertOneResult;

    /**
     * MongoDBReceipt constructor.
     *
     * @param \MongoDB\InsertOneResult $insertOneResult
     */
    public function __construct(InsertOneResult $insertOneResult)
    {
        $this->insertOneResult = $insertOneResult;
    }

    /**
     * @return \MongoDB\InsertOneResult
     */
    protected function getInsertOneResult()
    {
        return $this->insertOneResult;
    }

    /**
     * @param null|string $sequence
     * @return mixed
     */
    public function getID($sequence = null)
    {
        $id = $this->getInsertOneResult()->getInsertedId();

        return $this->castIfAvailable($id);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->getInsertOneResult()->getInsertedCount();
    }
}