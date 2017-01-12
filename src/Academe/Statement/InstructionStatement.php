<?php

namespace Academe\Statement;

use Academe\Statement\Traits\Lockable;
use Academe\Statement\Traits\Sortable;
use Academe\Statement\Traits\Transactional;

class InstructionStatement extends BaseInstructionStatement
{
    use Lockable, Sortable, Transactional;
}

