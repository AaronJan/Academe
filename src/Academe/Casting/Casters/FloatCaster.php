<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;

class FloatCaster extends BaseCaster
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
     * @return int
     */
    protected function castInPDO($connectionType, $value)
    {
        return (float) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castOutPDO($connectionType, $value)
    {
        return (float) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castInMongoDB($connectionType, $value)
    {
        return (float) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return int
     */
    protected function castOutMongoDB($connectionType, $value)
    {
        return (float) $value;
    }
}

