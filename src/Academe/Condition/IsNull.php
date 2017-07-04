<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\IsNullMongoDBResolver;
use Academe\Condition\Resolvers\IsNullPDOResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Constant\ConnectionConstant;

class IsNull extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MYSQL   => IsNullPDOResolver::class,
        ConnectionConstant::TYPE_MONGODB => IsNullMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Equal constructor.
     *
     * @param $field
     * @param $value
     */
    public function __construct($field)
    {
        $this->parameters = [$field];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
