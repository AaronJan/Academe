<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;

class SetCaster extends ListCaster
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
     * @param       $connectionType
     * @param array $records
     * @return string
     */
    protected function castInPDO($connectionType, array $records)
    {
        $castedRecords = [];

        foreach ($records as $record) {
            $castedRecords[] = $this->castNestedRecords('castIn', $record, $connectionType);
        }

        return json_encode(
            $castedRecords
        );
    }

    /**
     * @param string $connectionType
     * @param string $recordsJSON
     * @return array
     */
    protected function castOutPDO($connectionType, $recordsJSON)
    {
        $records = json_decode($recordsJSON, true);

        $castedRecords = [];

        foreach ($records as $record) {
            $castedRecords[] = $this->castNestedRecords('castOut', $record, $connectionType);
        }

        return $castedRecords;
    }

    /**
     * @param array $connectionType
     * @param array $records
     * @return array
     */
    protected function castInMongoDB($connectionType, array $records)
    {
        $castedRecords = [];

        foreach ($records as $record) {
            $castedRecords[] = $this->castNestedRecords('castIn', $record, $connectionType);
        }

        return $castedRecords;
    }

    /**
     * @param $connectionType
     * @param $records
     * @return array
     */
    protected function castOutMongoDB($connectionType, $records)
    {
        $castedRecords = [];

        foreach ($records as $record) {
            $castedRecords[] = $this->castNestedRecords('castOut', (array) $record, $connectionType);
        }

        return $castedRecords;
    }
}

