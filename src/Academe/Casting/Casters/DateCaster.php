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
     * @return mixed|string
     */
    protected function castInPDO($connectionType, $dateTime)
    {
        return empty($dateTime) ? $dateTime : $dateTime->toDateString();
    }

    /**
     * @param        $connectionType
     * @param string $value
     * @return mixed|\Carbon\Carbon
     */
    protected function castOutPDO($connectionType, $value)
    {
        return empty($value) ? $value : Carbon::createFromFormat('Y-m-d', $value);
    }

    /**
     * @param        $connectionType
     * @param Carbon $dateTime
     * @return mixed|\MongoDB\BSON\UTCDateTime
     */
    protected function castInMongoDB($connectionType, $dateTime)
    {
        if (empty($dateTime)) {
            return $dateTime;
        }

        $UTCMilliseconds = ($dateTime->timestamp + $dateTime->offset) * 1000;

        return new UTCDateTime($UTCMilliseconds);
    }

    /**
     * @param             $connectionType
     * @param UTCDateTime $mongoDate
     * @return mixed|\Carbon\Carbon
     */
    protected function castOutMongoDB($connectionType, $mongoDate)
    {
        return empty($mongoDate) ? $mongoDate : Carbon::instance($mongoDate->toDateTime());
    }
}

