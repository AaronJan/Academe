<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\LessThanOrEqualPDOResolver;
use Academe\Condition\Resolvers\LessThanOrEqualMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class LessThanOrEqual extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => LessThanOrEqualPDOResolver::class,
        Connection::TYPE_MONGODB => LessThanOrEqualMongoDBResolver::class,
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
