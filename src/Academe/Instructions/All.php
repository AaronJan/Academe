<?php

namespace Academe\Instructions;

use Academe\Contracts\Mapper\Mapper;
use Academe\Instructions\Traits\WithRelation;
use Academe\Contracts\Mapper\Instructions\All as AllContract;

class All extends SelectionType implements AllContract
{
    use WithRelation;

    /**
     * @param Mapper $mapper
     * @return array
     */
    public function execute(Mapper $mapper)
    {
        $entities = $this->getEntities($mapper);

        $loadedRelations = $this->getLoadedRelations($entities, $mapper);

        if (! empty($loadedRelations)) {
            $this->associateRelations($entities, $loadedRelations);
        }

        return $entities;
    }
}
