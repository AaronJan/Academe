<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\LessThanMongoDBResolver;
use Academe\Condition\Resolvers\LessThanPDOResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class LessThan extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => LessThanPDOResolver::class,
        Connection::TYPE_MONGODB => LessThanMongoDBResolver::class,
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
