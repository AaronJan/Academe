<?php

namespace Academe\Statement;

use Academe\Statement\Traits\Lockable;
use Academe\Statement\Traits\Sortable;

/**
 * Class MapperStatement
 *
 * @package Academe\Statement
 */
class MapperStatement extends BaseMapperStatement
{
    use Lockable, Sortable;

    /**
     * @param  Statement $statement
     * @return $this
     */
    public function loadFrom(Statement $statement)
    {
        $this->conditions = $statement->getConditions();

        if ($statement instanceof RelationSubStatement) {
            $this->fields = $statement->getFields();
            $this->setOrders($statement->getOrders());
        }

        return $this;
    }

}
