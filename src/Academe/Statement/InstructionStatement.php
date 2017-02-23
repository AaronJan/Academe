<?php

namespace Academe\Statement;

use Academe\Statement\Traits\Lockable;
use Academe\Statement\Traits\Sortable;

class InstructionStatement extends BaseInstructionStatement
{
    use Lockable, Sortable;
}

