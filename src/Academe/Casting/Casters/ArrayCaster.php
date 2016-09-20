<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;

class ArrayCaster extends BaseCaster
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
     * @param $value
     * @return string
     */
    static protected function castInPDO($value)
    {
        return json_encode($value);
    }

    /**
     * @param $value
     * @return array
     */
    static protected function castOutPDO($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     * @return string
     */
    static protected function castInMongoDB($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return array
     */
    static protected function castOutMongoDB($value)
    {
        return $value;
    }

}
