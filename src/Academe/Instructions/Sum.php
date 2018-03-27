<?php

namespace Academe\Instructions;

use Academe\Actions\Aggregate;
use Academe\Contracts\Mapper\Mapper;
use Academe\Instructions\Traits\WithRelation;
use Academe\Contracts\Mapper\Instructions\All as AllContract;

class Sum extends AggregateType implements AllContract
{
    use WithRelation;

    /**
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @return \Academe\Aggregation|mixed
     */
    public function execute(Mapper $mapper)
    {
        $record = $this->getAggregation($mapper, Aggregate::METHOD_SUM);

        return $record;
    }
}
