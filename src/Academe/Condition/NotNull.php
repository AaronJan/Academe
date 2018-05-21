<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Constant\ConnectionConstant;

class NotNull extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MYSQL   => Resolvers\NotNullPDOResolver::class,
        ConnectionConstant::TYPE_MONGODB => Resolvers\NotNullMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Equal constructor.
     *
     * @param string $field
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
