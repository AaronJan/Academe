<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\ContainsAllMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class ContainsAll extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MONGODB => ContainsAllMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * ContainsAll constructor.
     *
     * @param $field
     * @param $values
     */
    public function __construct($field, $values)
    {
        $this->parameters = [$field, $values];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
