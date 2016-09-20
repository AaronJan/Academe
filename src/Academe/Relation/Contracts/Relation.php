<?php

namespace Academe\Relation\Contracts;

use Academe\Contracts\Academe;
use Academe\Contracts\Mapper\Mapper;

interface Relation
{
    /**
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param string                           $relationName
     * @param Academe                          $academe
     * @return \Academe\Relation\Contracts\RelationHandler
     */
    public function makeHandler(Mapper $hostMapper, $relationName, Academe $academe);

}
