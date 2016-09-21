<?php

namespace Academe\Relation\Contracts;

use Academe\Contracts\Academe;
use Academe\Contracts\Caster;
use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Entity;

interface RelationHandler
{
    /**
     * @return string
     */
    public function getHostKeyField();

    /**
     * @param Entity[]|mixed $entities
     */
    public function associate($entities);

    /**
     * @param                            $entities
     * @param \Closure                   $constrain
     * @param \Academe\Contracts\Academe $academe
     * @param array                      $nestedRelations
     * @param array                      $transactions
     * @return $this
     */
    public function loadResults($entities,
                                \Closure $constrain,
                                Academe $academe,
                                array $nestedRelations,
                                array $transactions = []);

}
