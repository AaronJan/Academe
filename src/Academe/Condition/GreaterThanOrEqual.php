<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\GreaterThanOrEqualPDOResolver;
use Academe\Condition\Resolvers\GreaterThanOrEqualMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Constant\ConnectionConstant;

class GreaterThanOrEqual extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MYSQL   => GreaterThanOrEqualPDOResolver::class,
        ConnectionConstant::TYPE_MONGODB => GreaterThanOrEqualMongoDBResolver::class,
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
