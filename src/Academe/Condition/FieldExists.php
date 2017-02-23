<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\FieldExistsMongoDBResolver;
use Academe\Constant\ConnectionConstant;
use Academe\Contracts\Connection\Condition as ConditionContract;

class FieldExists extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MONGODB => FieldExistsMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * FieldExists constructor.
     *
     * @param $field
     * @param $isExists
     */
    public function __construct($field, $isExists)
    {
        $this->parameters = [$field, $isExists];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
