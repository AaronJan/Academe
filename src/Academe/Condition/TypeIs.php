<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\TypeIsMongoDBResolver;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Contracts\Connection\Connection;

class TypeIs extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        Connection::TYPE_MONGODB => TypeIsMongoDBResolver::class,
    ];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * TypeIs constructor.
     *
     * @param $field
     * @param $typeAlias
     */
    public function __construct($field, $typeAlias)
    {
        $this->parameters = [$field, $typeAlias];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
