<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\EqualMongoDBResolver;
use Academe\Condition\Resolvers\EqualPDOResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class Equal extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => EqualPDOResolver::class,
        Connection::TYPE_MONGODB => EqualMongoDBResolver::class,
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
