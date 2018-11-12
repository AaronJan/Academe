<?php

namespace Academe\Statement;

use Academe\Statement\Traits\Lockable;
use Academe\Statement\Traits\Sortable;
use Academe\Contracts\Statement;

class InstructionStatement extends BaseInstructionStatement
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

