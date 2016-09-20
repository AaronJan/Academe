<?php

namespace Academe\Database;

use Academe\Exceptions\BadMethodCallException;

abstract class BaseOperator
{
    static protected $methodMap = [];

    /**
     * @param $operation
     * @return bool|mixed
     * @throws BadMethodCallException
     */
    static public function getExecuteMethod($operation)
    {
        $operation = strtolower($operation);

        if (isset(static::$methodMap[$operation])) {
            return static::$methodMap[$operation];
        } else {
            throw new BadMethodCallException("Unsupported operation [{$operation}]");
        }
    }

}