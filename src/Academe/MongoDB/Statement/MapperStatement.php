<?php

namespace Academe\MongoDB\Statement;

use Academe\Statement\BaseMapperStatement;

class MapperStatement extends BaseMapperStatement
{
    use Traits\ConditionBuilder, Traits\InstructionBuilder {
        Traits\InstructionBuilder::updateBy as makeUpdateByExecutable;
    }

    /**
     * @param callable $callback
     * @return \Academe\Statement\TerminatedStatement
     */
    public function updateBy(callable $callback)
    {
        $executable = $this->makeUpdateByExecutable($callback);

        return $this->getMapper()->execute($executable);
    }

}