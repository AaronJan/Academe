<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\NotInPDOResolver;
use Academe\Condition\Resolvers\NotInMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class NotIn extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => NotInPDOResolver::class,
        Connection::TYPE_MONGODB => NotInMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Equal constructor.
     *
     * @param $attribute
     * @param $values
     */
    public function __construct($attribute, $values)
    {
        $this->parameters = [$attribute, $values];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
