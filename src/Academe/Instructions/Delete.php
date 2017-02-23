<?php

namespace Academe\Instructions;

use Academe\Actions\Delete as DeleteAction;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\Mapper\Instructions\Delete as DeleteContract;

class Delete extends BaseExecutable implements DeleteContract
{
    /**
     * @var Connection\ConditionGroup
     */
    protected $conditionGroup;

    /**
     * Destroy constructor.
     *
     * @param Connection\ConditionGroup $conditionGroup
     */
    public function __construct(Connection\ConditionGroup $conditionGroup)
    {
        $this->conditionGroup = $conditionGroup;
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $connection = $mapper->getConnection();
        $query      = $this->makeQuery($connection, $mapper);

        $result = $connection->run($query);

        return $result;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param \Academe\Contracts\Mapper\Mapper         $mapper
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection, Mapper $mapper)
    {
        $action  = new DeleteAction($this->conditionGroup);
        $builder = $connection->makeBuilder();

        return $builder->parse($mapper->getSubject(), $action, $mapper->getCastManager());
    }

}
