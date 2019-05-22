<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Caster;
use Academe\Constant\ConnectionConstant;

class GroupCaster extends BaseCaster
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
     * @var \Academe\Contracts\Caster|null
     */
    protected $caster;

    /**
     * GroupCaster constructor.
     *
     * @param \Academe\Contracts\Caster|null $caster
     */
    public function __construct(Caster $caster = null)
    {
        $this->caster = $caster;
    }

    /**
     * @return \Academe\Contracts\Caster|null
     */
    public function getCaster()
    {
        return $this->caster;
    }

    /**
     * @param string $method
     * @param array  $records
     * @param        $connectionType
     * @return array
     */
    protected function castRecords($method, array $records, $connectionType)
    {
        $caster = $this->getCaster();

        if (!$caster) {
            return $records;
        }

        $castedRecords = [];

        foreach ($records as $value) {
            $castedRecords[] = $caster->{$method}($value, $connectionType);
        }

        return $castedRecords;
    }

    /**
     * @param       $connectionType
     * @param array|null $records
     * @return string
     */
    protected function castInPDO($connectionType, $records)
    {
        if ($records === null) {
            return $records;
        }

        $castedRecords = $this->castRecords('castIn', $records, $connectionType);

        return json_encode($castedRecords);
    }

    /**
     * @param string $connectionType
     * @param string $recordsJSON
     * @return array|null
     */
    protected function castOutPDO($connectionType, $recordsJSON)
    {
        if ($recordsJSON === null) {
            return null;
        }

        $records = json_decode($recordsJSON, true);

        return $this->castRecords('castOut', $records, $connectionType);
    }

    /**
     * @param array $connectionType
     * @param array|null $records
     * @return array
     */
    protected function castInMongoDB($connectionType, $records)
    {
        if ($records === null) {
            return null;
        }

        return $this->castRecords('castIn', $records, $connectionType);
    }

    /**
     * @param $connectionType
     * @param $records
     * @return array|null
     */
    protected function castOutMongoDB($connectionType, $records)
    {
        if ($records === null) {
            return null;
        }

        return $this->castRecords('castOut', (array)$records, $connectionType);
    }
}
