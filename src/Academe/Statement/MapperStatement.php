<?php

namespace Academe\Statement;

use Academe\Statement\Traits\Lockable;
use Academe\Statement\Traits\Sortable;
use Academe\Statement\Traits\Transactional;

/**
 * Class MapperStatement
 *
 * @package Academe\Statement
 */
class MapperStatement extends BaseMapperStatement
{
    use Lockable, Sortable, Transactional;

}
