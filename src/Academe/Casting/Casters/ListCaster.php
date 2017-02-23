<?php

namespace Academe\Casting\Casters;

use Academe\Constant\ConnectionConstant;

class ListCaster extends BaseCaster
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
     * @var \Academe\Contracts\Caster[]
     */
    protected $casterMap;

    /**
     * ListCaster constructor.
     *
     * @param \Academe\Contracts\Caster[] $casterMap
     */
    public function __construct(array $casterMap = [])
    {
        $this->casterMap = $casterMap;
    }

    /**
     * @param $name
     * @return \Academe\Contracts\Caster|bool|mixed
     */
    protected function getCaster($name)
    {
        return isset($this->casterMap[$name]) ? $this->casterMap[$name] : false;
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
            $caster = $this->getCaster($name);

            if ($caster) {
                $value = $caster->{$method}($value, $connectionType);
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

