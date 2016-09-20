<?php

namespace Academe\Exceptions;

use Academe\Contracts\Mapper\Mapper;

class DataNotFoundException extends \RuntimeException
{
    public function setMapper(Mapper $mapper)
    {

    }
}
