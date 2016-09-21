<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;

class ListCaster extends BaseCaster
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
     * @var \Academe\Contracts\Caster[]
     */
    protected $list;

    /**
     * ListCaster constructor.
     *
     * @param \Academe\Contracts\Caster[] $list
     */
    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    /**
     * @param string $method
     * @param array  $record
     * @param        $connectionType
     * @return array
     */
    protected function castNestedRecords($method, array $record, $connectionType)
    {
        $resolvedAttributes = [];

        foreach ($record as $name => $value) {
            if (isset($this->list[$name])) {
                $caster = $this->list[$name];
                $value  = $caster->{$method}($value, $connectionType);
            }

            $resolvedAttributes[$name] = $value;
        }

        return $resolvedAttributes;
    }

    /**
     * @param        $connectionType
     * @param  array $record
     * @return string
     */
    protected function castInPDO($connectionType, array $record)
    {
        return json_encode(
            $this->castNestedRecords('castIn', $record, $connectionType)
        );
    }

    /**
     * @param        $connectionType
     * @param string $record
     * @return array
     */
    protected function castOutPDO($connectionType, $record)
    {
        $record = json_decode($record, true);

        return $this->castNestedRecords('castOut', $record, $connectionType);
    }

    /**
     * @param       $connectionType
     * @param array $record
     * @return array
     */
    protected function castInMongoDB($connectionType, array $record)
    {
        return (array) $this->castNestedRecords('castIn', $record, $connectionType);
    }

    /**
     * @param $connectionType
     * @param $record
     * @return string
     */
    protected function castOutMongoDB($connectionType, $record)
    {
        return $this->castNestedRecords('castOut', (array) $record, $connectionType);
    }
}

