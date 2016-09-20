<?php

namespace Academe\Instructions;

use Academe\Actions\Select;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;
use Academe\Formation;
use Academe\Instructions\Traits\WithRelation;
use Academe\Contracts\Mapper\Instructions\Segment as SegmentContract;

class Segment extends SelectionType implements SegmentContract
{
    use WithRelation;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var null|int
     */
    protected $offset = null;

    /**
     * Segment constructor.
     *
     * @param int                                               $limit
     * @param array                                             $fields
     * @param null|int                                          $offset
     * @param \Academe\Contracts\Connection\ConditionGroup|null $conditionGroup
     */
    public function __construct($limit,
                                $fields = ['*'],
                                $offset = null,
                                Connection\ConditionGroup $conditionGroup = null)
    {
        parent::__construct($fields, $conditionGroup);

        $this->limit  = $limit;
        $this->offset = $offset;
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $transactions = $this->getTransactions();

        $mapper->involve($transactions);

        $entities = $this->getEntities($mapper);

        $loadedRelations = $this->getLoadedRelations($entities, $mapper, $transactions);

        if (! empty($loadedRelations)) {
            $this->associateRelations($entities, $loadedRelations);
        }

        return $entities;
    }

    /**
     * @return Select
     */
    protected function makeSelectActionWithFormation()
    {
        $action    = $this->makeSelectAction();
        $formation = $this->makeFormation();

        $action->setFormation($formation);

        return $action;
    }

    /**
     * @return Formation
     */
    protected function makeFormation()
    {
        $formation = new Formation();

        $formation->setOrders($this->orders);

        if ($this->offset) {
            $formation->setLimit($this->limit, $this->offset);
        } else {
            $formation->setLimit($this->limit);
        }

        return $formation;
    }

}
