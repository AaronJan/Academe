<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\LikePDOResolver;
use Academe\Condition\Resolvers\LikeMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;
use Academe\Exceptions\BadMethodCallException;

class Like extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => LikePDOResolver::class,
        Connection::TYPE_MONGODB => LikeMongoDBResolver::class,
    ];

    const MATCH_FROM_LEFT  = 1;
    const MATCH_FROM_RIGHT = 2;
    const MATCH_INCLUDE    = 3;

    static protected $allowedMatchModes = [1, 2, 3];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Like constructor.
     *
     * @param $field
     * @param $value
     * @param $matchMode
     */
    public function __construct($field, $value, $matchMode)
    {
        $this->validateMatchMode($matchMode);

        $this->parameters = [$field, $value, $matchMode];
    }

    /**
     * @param $matchMode
     * @throws BadMethodCallException
     */
    protected function validateMatchMode($matchMode)
    {
        if (! in_array($matchMode, static::$allowedMatchModes)) {
            throw new BadMethodCallException("Undefined match mode [{$matchMode}]");
        }
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
