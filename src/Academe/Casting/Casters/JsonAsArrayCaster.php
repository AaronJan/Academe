<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;

/**
 * Class JsonAsArrayCaster
 *
 * @package Academe\Casting\Casters
 */
class JsonAsArrayCaster extends BaseCaster
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
     * @var int
     */
    protected $encodeOption;

    /**
     * @var int
     */
    protected $decodeOption;

    /**
     * JsonAsArrayCaster constructor.
     *
     * @param int $encodeOption
     * @param int $decodeOption
     */
    public function __construct($encodeOption = 0, $decodeOption = 0)
    {
        $this->encodeOption = $encodeOption;
        $this->decodeOption = $decodeOption;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return string
     */
    protected function castInPDO($connectionType, $value)
    {
        return json_encode($value, $this->encodeOption);
    }

    /**
     * @param $connectionType
     * @param $value
     * @return array
     */
    protected function castOutPDO($connectionType, $value)
    {
        return json_decode($value, true, $this->decodeOption);
    }

    /**
     * @param $connectionType
     * @param $value
     * @return string
     */
    protected function castInMongoDB($connectionType, $value)
    {
        return $value;
    }

    /**
     * @param $connectionType
     * @param $value
     * @return array
     */
    protected function castOutMongoDB($connectionType, $value)
    {
        return (array) $value;
    }

}
