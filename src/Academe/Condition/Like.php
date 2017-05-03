<?php

namespace Academe\Condition;

use Academe\Condition\Resolvers\LikePDOResolver;
use Academe\Condition\Resolvers\LikeMongoDBResolver;
use Academe\Constant\InstructionConstant;
use Academe\Contracts\Connection\Condition as ConditionContract;
use Academe\Constant\ConnectionConstant;
use Academe\Exceptions\BadMethodCallException;

class Like extends BaseCondition implements ConditionContract
{
    /**
     * @var array
     */
    static protected $connectionToResolverClassMap = [
        ConnectionConstant::TYPE_MYSQL   => LikePDOResolver::class,
        ConnectionConstant::TYPE_MONGODB => LikeMongoDBResolver::class,
    ];

    const MATCH_FROM_LEFT  = InstructionConstant::LIKE_MATCH_FROM_LEFT;
    const MATCH_FROM_RIGHT = InstructionConstant::LIKE_MATCH_FROM_RIGHT;
    const MATCH_INCLUDE    = InstructionConstant::LIKE_MATCH_INCLUDE;

    static protected $allowedMatchModes = [1, 2, 3];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Like constructor.
     *
     * @param $field
     * @param $value
     * @param $matchMode
     */
    public function __construct($field, $value, $matchMode)
    {
        $this->validateMatchMode($matchMode);

        $this->parameters = [$field, $value, $matchMode];
    }

    /**
     * @param $matchMode
     * @throws BadMethodCallException
     */
    protected function validateMatchMode($matchMode)
    {
        if (! in_array($matchMode, static::$allowedMatchModes)) {
            throw new BadMethodCallException("Undefined match mode [{$matchMode}]");
        }
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
