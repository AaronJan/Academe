<?php

namespace Academe\Instructions;

use Academe\Contracts\Connection;
use Academe\Actions\Update as UpdateAction;
use Academe\Contracts\Mapper\Instructions\Update as UpdateContract;
use Academe\Contracts\Mapper\Mapper;

class Update extends WriteType implements UpdateContract
{
    /**
     * @var Connection\ConditionGroup
     */
    protected $conditionGroup;

    /**
     * @param Connection\ConditionGroup $conditionGroup
     * @param array                     $attributes
     */
    public function __construct(Connection\ConditionGroup $conditionGroup, array $attributes)
    {
        $this->conditionGroup = $conditionGroup;
        $this->attributes     = $attributes;
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $mapper->involve($this->getTransactions());

        $connection = $mapper->getConnection();
        $query      = $this->makeQuery($connection, $mapper, $this->attributes);

        $result = $connection->run($query);

        return $result;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param \Academe\Contracts\Mapper\Mapper         $mapper
     * @param array                                    $attributes
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection,
                                 Mapper $mapper,
                                 array $attributes)
    {
        $action = new UpdateAction($this->conditionGroup, $attributes);

        return $connection->makeBuilder()
            ->parse($mapper->getSubject(), $action, $mapper->getCastManager());
    }

}
