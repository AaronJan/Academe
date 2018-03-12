<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;

class VersionCaster extends BaseCaster
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
     * @param         $connectionType
     * @param integer $value
     * @return int
     */
    protected function castInPDO($connectionType, $value)
    {
        return ((int) $value) + 1;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castOutPDO($connectionType, $value)
    {
        return (int) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castInMongoDB($connectionType, $value)
    {
        return ((int) $value) + 1;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castOutMongoDB($connectionType, $value)
    {
        return (int) $value;
    }
}

