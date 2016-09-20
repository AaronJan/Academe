<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;

class StringCaster extends BaseCaster
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
        return (string) $value;
    }

    /**
     * @param      $value
     * @return int
     */
    protected function castOutPDO($value)
    {
        return (string) $value;
    }

    /**
     * @param      $value
     * @return int
     */
    static protected function castInMongoDB($value)
    {
        return (string) $value;
    }

    /**
     * @param      $value
     * @return int
     */
    static protected function castOutMongoDB($value)
    {
        return (string) $value;
    }
}

