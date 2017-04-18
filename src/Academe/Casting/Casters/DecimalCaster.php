<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;
use Brick\Math\BigDecimal;
use MongoDB\BSON\Decimal128;

class DecimalCaster extends BaseCaster
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
     * @param                        $connectionType
     * @param \Brick\Math\BigDecimal $value
     * @return int
     */
    protected function castInPDO($connectionType, $value)
    {
        return (string) $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return \Brick\Math\BigDecimal
     */
    protected function castOutPDO($connectionType, $value)
    {
        return BigDecimal::of($value);
    }

    /**
     * @param                        $connectionType
     * @param \Brick\Math\BigDecimal $value
     * @return \MongoDB\BSON\Decimal128
     */
    protected function castInMongoDB($connectionType, $value)
    {
        $stringified = (string) $value;

        return new Decimal128($stringified);
    }

    /**
     * @param                          $connectionType
     * @param \MongoDB\BSON\Decimal128 $value
     * @return \Brick\Math\BigDecimal
     */
    protected function castOutMongoDB($connectionType, $value)
    {
        $stringified = (string) $value;

        return BigDecimal::of($stringified);
    }
}

