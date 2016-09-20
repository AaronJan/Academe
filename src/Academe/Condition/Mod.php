<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\ModPDOResolver;
use Academe\Condition\Resolvers\ModMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class Mod extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => ModPDOResolver::class,
        Connection::TYPE_MONGODB => ModMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Mod constructor.
     *
     * @param $field
     * @param $divisor
     * @param $remainder
     */
    public function __construct($field, $divisor, $remainder)
    {
        $this->parameters = [$field, $divisor, $remainder];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
