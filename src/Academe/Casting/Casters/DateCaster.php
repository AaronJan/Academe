<?php

namespace Academe\Casting\Casters;

use Academe\Contracts\Connection\Connection;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;

class DateCaster extends BaseCaster
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
     * @param        $connectionType
     * @param Carbon $dateTime
     * @return string
     */
    protected function castInPDO($connectionType, $dateTime)
    {
        return $dateTime->toDateString();
    }

    /**
     * @param        $connectionType
     * @param string $value
     * @return \Carbon\Carbon
     */
    protected function castOutPDO($connectionType, $value)
    {
        return Carbon::createFromFormat('Y-m-d', $value);
    }

    /**
     * @param        $connectionType
     * @param Carbon $dateTime
     * @return \MongoDB\BSON\UTCDateTime
     */
    protected function castInMongoDB($connectionType, $dateTime)
    {
        $UTCMilliseconds = $dateTime->timestamp + $dateTime->offset;

        return new UTCDateTime($UTCMilliseconds);
    }

    /**
     * @param             $connectionType
     * @param UTCDateTime $mongoDate
     * @return \Carbon\Carbon
     */
    protected function castOutMongoDB($connectionType, $mongoDate)
    {
        return Carbon::instance($mongoDate->toDateTime());
    }
}

