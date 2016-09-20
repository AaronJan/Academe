<?php

namespace Academe\Condition;

use Academe\Contracts\CastManager;
use Academe\Exceptions\UnsupportedConnectionException;

abstract class BaseCondition
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [];

    /**
     * @param int                            $connectionType
     * @param \Academe\Contracts\CastManager $castManager
     * @return mixed
     */
    public function parse($connectionType, CastManager $castManager = null)
    {
        $resolverClass = isset(static::$connectionToResolverClassMap[$connectionType]) ?
            static::$connectionToResolverClassMap[$connectionType] :
            null;

        if ($resolverClass === null) {
            $message = "This condition doesn't support connection type [{$connectionType}]";

            throw new UnsupportedConnectionException($message);
        }

        return call_user_func_array(
            [$resolverClass, 'resolve'],
            [$connectionType, $this, $castManager]
        );
    }

}