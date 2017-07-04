<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\NotEqualMongoDBResolver;
use Academe\Condition\Resolvers\NotEqualPDOResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Constant\ConnectionConstant;

class NotEqual extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MYSQL   => NotEqualPDOResolver::class,
        ConnectionConstant::TYPE_MONGODB => NotEqualMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Equal constructor.
     *
     * @param $field
     * @param $notExpect
     */
    public function __construct($field, $notExpect)
    {
        $this->parameters = [$field, $notExpect];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
