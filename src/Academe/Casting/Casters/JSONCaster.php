<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;

class JSONCaster extends BaseCaster
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
        return (array) $value;
    }

}
