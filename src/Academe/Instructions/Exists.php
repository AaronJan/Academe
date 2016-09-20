<?php

namespace Academe\Instructions;

use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Instructions\Exists as ExistsContract;

class Exists extends BaseCount implements ExistsContract
{
    /**
     * Exists constructor.
     *
     * @param Connection\ConditionGroup|null $conditionGroup
     */
    public function __construct(Connection\ConditionGroup $conditionGroup = null)
    {
        parent::__construct('*', $conditionGroup);
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $count = parent::execute($mapper);

        return $count > 0;
    }

}
