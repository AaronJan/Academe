<?php

namespace Academe\Instructions\Traits;

use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\CastManager;

trait CastRecord
{
    /**
     * @param array                            $records
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @return array
     */
    protected function castRecords(array $records, Mapper $mapper)
    {
        $castManager    = $mapper->getCastManager();
        $connectionType = $mapper->getConnection()->getType();

        return $this->castRecordsUsingCastManager($records, $castManager, $connectionType);
    }

    /**
     * @param array $records
     * @param CastManager $castManager
     * @param integer $connectionType
     * @return array
     */
    protected function castRecordsUsingCastManager(array $records, CastManager $castManager, $connectionType) {
        return array_map(function ($record) use ($castManager, $connectionType) {
            return $castManager->castOutAttributes($record, $connectionType);
        }, $records);
    }
}