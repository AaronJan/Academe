<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;
use MongoDB\BSON\ObjectID;

class PrimaryKeyCaster extends BaseCaster
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
     * @var array
     */
    protected $options = [
        'primitiveType' => 'integer', // integer, string
        'isObjectID'    => true, // for MongoDB
    ];

    /**
     * @param $value
     * @return mixed
     */
    protected function castInPDO($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function castOutPDO($value)
    {
        settype($value, $this->options['primitiveType']);

        return $value;
    }

    /**
     * @param string $value
     * @return ObjectID|mixed
     */
    protected function castInMongoDB($value)
    {
        if ($this->options['isObjectID']) {
            return new ObjectID($value);
        }

        settype($value, $this->options['primitiveType']);

        return $value;
    }

    /**
     * @param ObjectID $value
     * @return mixed
     */
    protected function castOutMongoDB($value)
    {
        if ($this->options['isObjectID']) {
            return (string) $value;
        }

        settype($value, $this->options['primitiveType']);

        return $value;
    }
}

