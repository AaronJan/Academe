<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;
use Academe\Exceptions\BadMethodCallException;
use MongoDB\BSON\ObjectID;

class MongodbObjectIdCaster extends BaseCaster
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
        $message = "MongodbObjectIdCaster can only been used with MongoDB.";
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
     * @param          $connectionType
     * @param ObjectID $value
     * @return \MongoDB\BSON\ObjectID
     */
    protected function castInMongoDB($connectionType, $value)
    {
        return new $value;
    }

    /**
     * @param          $connectionType
     * @param ObjectID $value
     * @return mixed
     */
    protected function castOutMongoDB($connectionType, $value)
    {
        return $value;
    }

}

