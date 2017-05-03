<?php

namespace Academe\Statement;

use Academe\Statement\Traits\Lockable;

class RelationSubStatement extends ConditionStatement
{
    use Lockable;

    /**
     * @var array|null
     */
    protected $fields;

    /**
     * @return array|null
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}

