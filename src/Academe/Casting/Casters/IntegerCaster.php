<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;

class IntegerCaster extends BaseCaster
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
     * @param      $value
     * @return int
     */
     protected function castInPDO($value)
    {
        return (int) $value;
    }

    /**
     * @param      $value
     * @return int
     */
     protected function castOutPDO($value)
    {
        return (int) $value;
    }

    /**
     * @param      $value
     * @return int
     */
     protected function castInMongoDB($value)
    {
        return (int) $value;
    }

    /**
     * @param      $value
     * @return int
     */
     protected function castOutMongoDB($value)
    {
        return (int) $value;
    }
}

