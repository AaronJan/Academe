<?php

namespace Academe\MongoDB\Statement\Traits;

use Academe\MongoDB\Instructions\AdvanceUpdateInstruction;
use Academe\MongoDB\Statement\MongoDBManualUpdate;

trait InstructionBuilder
{
    /**
     * @param       $instructionClass
     * @param array $instructionConstructParameters
     * @return \Academe\Statement\TerminatedStatement
     */
    abstract protected function makeTerminatedStatement($instructionClass,
                                                        array $instructionConstructParameters);

    /**
     * @return \Academe\Contracts\Connection\ConditionGroup
     */
    abstract public function compileConditionGroup();

    /**
     * @return \Academe\MongoDB\Statement\MongoDBManualUpdate
     */
    protected function makeMongoDBManualUpdate()
    {
        return new MongoDBManualUpdate();
    }

    /**
     * @param callable $callback
     * @return \Academe\Statement\TerminatedStatement
     */
    public function updateBy(callable $callback)
    {
        $manualUpdate = $this->makeMongoDBManualUpdate();

        $callback($manualUpdate);

        return $this->makeTerminatedStatement(
            AdvanceUpdateInstruction::class,
            [$this->compileConditionGroup(), $manualUpdate]
        );
    }

}