<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\NotInPDOResolver;
use Academe\Condition\Resolvers\NotInMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Constant\ConnectionConstant;

class NotIn extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MYSQL   => NotInPDOResolver::class,
        ConnectionConstant::TYPE_MONGODB => NotInMongoDBResolver::class,
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
