<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\GreaterThanMongoDBResolver;
use Academe\Condition\Resolvers\GreaterThanPDOResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class GreaterThan extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => GreaterThanPDOResolver::class,
        Connection::TYPE_MONGODB => GreaterThanMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Equal constructor.
     *
     * @param $attribute
     * @param $value
     */
    public function __construct($attribute, $value)
    {
        $this->parameters = [$attribute, $value];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
