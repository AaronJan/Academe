<?php

namespace Academe\Instructions;

use Academe\Actions\Select;
use Academe\Contracts\Mapper\Mapper;
use Academe\Formation;
use Academe\Instructions\Traits\WithRelation;
use Academe\Contracts\Mapper\Instructions\First as FirstContract;

class First extends SelectionType implements FirstContract
{
    use WithRelation;

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

        return reset($entities);
    }

    /**
     * @return Select
     */
    protected function makeSelectActionWithFormation()
    {
        $action    = $this->makeSelectAction();
        $formation = (new Formation())->setLimit(1)->setOrders($this->orders);

        $action->setFormation($formation);

        return $action;
    }

}
