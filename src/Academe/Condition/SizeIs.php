<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\SizeIsMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class SizeIs extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MONGODB => SizeIsMongoDBResolver::class,
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
