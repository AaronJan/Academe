<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\LessThanOrEqualPDOResolver;
use Academe\Condition\Resolvers\LessThanOrEqualMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Constant\ConnectionConstant;

class LessThanOrEqual extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MYSQL   => LessThanOrEqualPDOResolver::class,
        ConnectionConstant::TYPE_MONGODB => LessThanOrEqualMongoDBResolver::class,
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
