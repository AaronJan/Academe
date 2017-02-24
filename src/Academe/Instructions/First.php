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
        $entities = $this->getEntities($mapper);

        $loadedRelations = $this->getLoadedRelations($entities, $mapper);

        if (! empty($loadedRelations)) {
            $this->associateRelations($entities, $loadedRelations);
        }

        $record = reset($entities);

        return $record ? $mapper->convertRecord($record) : $record;
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
