<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\ContainsAllMongoDBResolver;
use Academe\Constant\ConnectionConstant;
use Academe\Contracts\Connection\Condition as ConditionContract;

class ContainsAll extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MONGODB => ContainsAllMongoDBResolver::class,
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
