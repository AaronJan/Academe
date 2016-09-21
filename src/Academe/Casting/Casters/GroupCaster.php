<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;

class GroupCaster extends ListCaster
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

