<?php

namespace Academe\Relation\Contracts;

use Academe\Contracts\Academe;

interface RelationHandler
{
    /**
     * @param array[] $entities
     */
    public function associate($entities);

    /**
     * @param                            $entities
     * @param \Closure                   $constrain
     * @param \Academe\Contracts\Academe $academe
     * @param array                      $nestedRelations
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations);

}
