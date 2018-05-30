<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\ElementMatchMongoDBResolver;
use Academe\Constant\ConnectionConstant;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;

class ElementMatch extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MONGODB => ElementMatchMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * FieldExists constructor.
     *
     * @param $field
     * @param Condition|ConditionGroup $condition
     */
    public function __construct($field, $condition)
    {
        $this->parameters = [$field, $condition];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
