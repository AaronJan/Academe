<?php

namespace Academe\Statement;

use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\Contracts\Statement;

class ConditionStatement extends BaseConditionStatement implements Statement,
                                                                   ConditionGroup
{
    /**
     * FluentStatement constructor.
     *
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     */
    public function __construct(ConditionMaker $conditionMaker)
    {
        $this->setConditionMaker($conditionMaker);
    }

    /**
     * @return InstructionStatement
     */
    public function upgrade()
    {
        $fluentStatement = new InstructionStatement($this->getConditionMaker());

        return $fluentStatement->loadFrom($this);
    }

}
