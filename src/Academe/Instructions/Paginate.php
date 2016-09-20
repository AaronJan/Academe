<?php

namespace Academe\Instructions;

use Academe\Actions\Aggregate;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\Transaction;
use Academe\Instructions\Traits\WithRelation;
use Academe\Support\Pagination;
use Academe\Contracts\Mapper\Instructions\Paginate as PaginateContract;

class Paginate extends Segment implements PaginateContract
{
    use WithRelation;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * Paginate constructor.
     *
     * @param array                          $page
     * @param int                            $perPage
     * @param array                          $fields
     * @param Connection\ConditionGroup|null $conditionGroup
     */
    public function __construct($page,
                                $perPage = 15,
                                $fields = ['*'],
                                Connection\ConditionGroup $conditionGroup = null)
    {
        $offset = (($page - 1) * $perPage);

        parent::__construct($perPage, $fields, $offset, $conditionGroup);

        $this->page    = $page;
        $this->perPage = $perPage;
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $transactions = $this->getTransactions();

        $mapper->involve($transactions);

        $total    = $this->getCountForPagination($mapper);
        $entities = $this->getPaginationEntities($mapper, $total, $transactions);

        return new Pagination($entities, $total, $this->perPage, $this->page);
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @param                                  $total
     * @param Transaction[]                    $transactions
     * @return array
     */
    protected function getPaginationEntities(Mapper $mapper, $total, array $transactions = [])
    {
        if ($total == 0) {
            return [];
        }

        $entities = $this->getEntities($mapper);

        $loadedRelations = $this->getLoadedRelations($entities, $mapper, $transactions);

        if (! empty($loadedRelations)) {
            $this->associateRelations($entities, $loadedRelations);
        }

        return $entities;
    }

    /**
     * @param Mapper $mapper
     * @return int
     */
    protected function getCountForPagination(Mapper $mapper)
    {
        $connection = $mapper->getConnection();
        $query      = $this->makeCountForPaginationQuery($connection, $mapper->getSubject());

        return $connection->run($query);
    }

    /**
     * @param Connection\Connection $connection
     * @param                       $subject
     * @return Connection\Query
     */
    protected function makeCountForPaginationQuery(Connection\Connection $connection,
                                                   $subject)
    {
        $builder = $connection->makeBuilder();
        $action  = $this->makeCountAggregateAction();

        if ($this->conditionGroup) {
            $action = $action->setConditionGroup($this->conditionGroup);
        }

        return $builder->parse($subject, $action);
    }

    /**
     * @return \Academe\Actions\Aggregate
     */
    protected function makeCountAggregateAction()
    {
        $action = new Aggregate('count');

        $action->setLock($this->lockLevel);

        return $action;
    }

}
