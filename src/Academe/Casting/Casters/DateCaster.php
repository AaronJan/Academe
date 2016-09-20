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
     * @param Carbon $dateTime
     * @return string
     */
    protected function castInPDO($dateTime)
    {
        return $dateTime->toDateString();
    }

    /**
     * @param string $value
     * @return Carbon
     */
    protected function castOutPDO($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value);
    }

    /**
     * @param Carbon $dateTime
     * @return UTCDateTime
     */
    protected function castInMongoDB($dateTime)
    {
        $UTCMilliseconds = $dateTime->timestamp + $dateTime->offset;

        return new UTCDateTime($UTCMilliseconds);
    }

    /**
     * @param UTCDateTime $mongoDate
     * @return Carbon
     */
    protected function castOutMongoDB($mongoDate)
    {
        return Carbon::instance($mongoDate->toDateTime());
    }
}

