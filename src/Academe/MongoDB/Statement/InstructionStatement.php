<?php

namespace Academe\MongoDB\Statement;

use Academe\Statement\BaseInstructionStatement;

class InstructionStatement extends BaseInstructionStatement
{
    use Traits\ConditionBuilder, Traits\InstructionBuilder;

}