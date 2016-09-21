<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;

class JSONCaster extends BaseCaster
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
     * @return string
     */
    static protected function castInPDO($connectionType, $value)
    {
        return json_encode($value);
    }

    /**
     * @param $connectionType
     * @param $value
     * @return array
     */
    static protected function castOutPDO($connectionType, $value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $connectionType
     * @param $value
     * @return string
     */
    static protected function castInMongoDB($connectionType, $value)
    {
        return $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return array
     */
    static protected function castOutMongoDB($connectionType, $value)
    {
        return $value;
    }

}
