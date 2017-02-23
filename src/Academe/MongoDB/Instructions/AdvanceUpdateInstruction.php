<?php

namespace Academe\MongoDB\Instructions;

use Academe\Contracts\Connection;
use Academe\MongoDB\Actions\AdvanceUpdateAction;
use Academe\Contracts\Mapper\Instructions\Update as UpdateContract;
use Academe\Contracts\Mapper\Mapper;
use Academe\Instructions\BaseExecutable;
use Academe\MongoDB\Statement\MongoDBManualUpdate;

class AdvanceUpdateInstruction extends BaseExecutable implements UpdateContract
{
    /**
     * @var Connection\ConditionGroup
     */
    protected $conditionGroup;

    /**
     * @var \Academe\MongoDB\Statement\MongoDBManualUpdate
     */
    protected $mongoDBManualUpdate;

    /**
     * AdvanceUpdate constructor.
     *
     * @param \Academe\Contracts\Connection\ConditionGroup   $conditionGroup
     * @param \Academe\MongoDB\Statement\MongoDBManualUpdate $mongoDBManualUpdate
     */
    public function __construct(Connection\ConditionGroup $conditionGroup,
                                MongoDBManualUpdate $mongoDBManualUpdate)
    {
        $this->conditionGroup      = $conditionGroup;
        $this->mongoDBManualUpdate = $mongoDBManualUpdate;
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $connection = $mapper->getConnection();
        $query      = $this->makeQuery($connection, $mapper, $this->getMongoDBManualUpdate());

        $result = $connection->run($query);

        return $result;
    }

    /**
     * @return mixed
     */
    protected function getMongoDBManualUpdate()
    {
        return $this->mongoDBManualUpdate;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection       $connection
     * @param \Academe\Contracts\Mapper\Mapper               $mapper
     * @param \Academe\MongoDB\Statement\MongoDBManualUpdate $mongoDBManualUpdate
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection,
                                 Mapper $mapper,
                                 MongoDBManualUpdate $mongoDBManualUpdate)
    {
        $action = new AdvanceUpdateAction($this->conditionGroup, $mongoDBManualUpdate);

        return $connection->makeBuilder()
            ->parse($mapper->getSubject(), $action, $mapper->getCastManager());
    }

}
