<?php

namespace Academe\Database;

use Academe\Exceptions\LogicException;

abstract class BaseQueryInterpreter
{
    /**
     * @var array
     */
    static protected $operationToMethodMap = [];

    /**
     * @param $operation
     * @return bool|mixed
     * @throws LogicException
     */
    static public function getMethodForOperation($operation)
    {
        $operation = strtolower($operation);

        if (isset(static::$operationToMethodMap[$operation])) {
            return static::$operationToMethodMap[$operation];
        } else {
            throw new LogicException("Unsupported operation [{$operation}]");
        }
    }

    /**
     * @param $startTime
     * @return float
     */
    static protected function getElapsedTime($startTime)
    {
        return round((microtime(true) - $startTime) * 1000, 2);
    }

}