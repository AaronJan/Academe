<?php

namespace Academe\Instructions;

use Academe\Instructions\Traits\Transactional;
use Academe\Contracts\Mapper\Executable;

abstract class BaseExecutable implements Executable
{
    use Transactional;
}