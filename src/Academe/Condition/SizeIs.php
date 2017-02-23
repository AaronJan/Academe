<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\SizeIsMongoDBResolver;
use Academe\Constant\ConnectionConstant;
use Academe\Contracts\Connection\Condition as ConditionContract;

class SizeIs extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MONGODB => SizeIsMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * SizeIs constructor.
     *
     * @param $field
     * @param $size
     */
    public function __construct($field, $size)
    {
        $this->parameters = [$field, $size];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
