<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\GreaterThanOrEqualPDOResolver;
use Academe\Condition\Resolvers\GreaterThanOrEqualMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class GreaterThanOrEqual extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => GreaterThanOrEqualPDOResolver::class,
        Connection::TYPE_MONGODB => GreaterThanOrEqualMongoDBResolver::class,
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
