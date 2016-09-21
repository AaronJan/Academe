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
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castInPDO($connectionType, $value)
    {
        return (string) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castOutPDO($connectionType, $value)
    {
        return (string) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    static protected function castInMongoDB($connectionType, $value)
    {
        return (string) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    static protected function castOutMongoDB($connectionType, $value)
    {
        return (string) $value;
    }
}

