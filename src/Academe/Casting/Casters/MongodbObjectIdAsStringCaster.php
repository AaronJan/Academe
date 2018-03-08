<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;
use Academe\Exceptions\BadMethodCallException;
use MongoDB\BSON\ObjectID;

/**
 * Class MongodbObjectIdAsStringCaster
 *
 * @package Academe\Casting\Casters
 */
class MongodbObjectIdAsStringCaster extends BaseCaster
{
    /**
     * @var array
     */
    static protected $connectionTypeToCastInMethodMap = [
        ConnectionConstant::TYPE_MYSQL   => 'castInPDO',
        ConnectionConstant::TYPE_MONGODB => 'castInMongoDB',
    ];

    /**
     * @var array
     */
    static protected $connectionTypeToCastOutMethodMap = [
        ConnectionConstant::TYPE_MYSQL   => 'castOutPDO',
        ConnectionConstant::TYPE_MONGODB => 'castOutMongoDB',
    ];

    /**
     * @throws BadMethodCallException
     */
    protected function throwUnsupportException()
    {
        $message = "MongodbObjectIdAsStringCaster can't be used with Database other than MongoDB.";
        throw new BadMethodCallException($message);
    }

    /**
     * @param $connectionType
     * @param $value
     */
    protected function castInPDO($connectionType, $value)
    {
        $this->throwUnsupportException();
    }

    /**
     * @param $connectionType
     * @param $value
     */
    protected function castOutPDO($connectionType, $value)
    {
        $this->throwUnsupportException();
    }

    /**
     * @param        $connectionType
     * @param string $value
     * @return \MongoDB\BSON\ObjectID
     */
    protected function castInMongoDB($connectionType, $value)
    {
        return new ObjectID($value);
    }

    /**
     * @param          $connectionType
     * @param ObjectID $value
     * @return mixed
     */
    protected function castOutMongoDB($connectionType, $value)
    {
        return (string) $value;
    }

}

