<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;
use Academe\Exceptions\BadMethodCallException;
use MongoDB\BSON\ObjectID;

class MongoDBObjectIDCaster extends BaseCaster
{
    /**
     * @var array
     */
    static protected $connectionTypeToCastInMethodMap = [
        Connection::TYPE_MYSQL   => 'castInPDO',
        Connection::TYPE_MONGODB => 'castInMongoDB',
    ];

    /**
     * @var array
     */
    static protected $connectionTypeToCastOutMethodMap = [
        Connection::TYPE_MYSQL   => 'castOutPDO',
        Connection::TYPE_MONGODB => 'castOutMongoDB',
    ];

    /**
     * @throws BadMethodCallException
     */
    protected function throwUnsupportException()
    {
        $message = "MongoDBObjectIDCaster can't be used with Database other than MongoDB.";

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

