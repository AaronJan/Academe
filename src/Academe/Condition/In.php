<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\InPDOResolver;
use Academe\Condition\Resolvers\InMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class In extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MYSQL   => InPDOResolver::class,
        Connection::TYPE_MONGODB => InMongoDBResolver::class,
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
