<?php

namespace Academe\Contracts;

use Academe\Contracts\Mapper\Mapper;

interface MapperManager
{
    /**
     * @param $mapperClass
     * @return Mapper
     */
    public function getMapper($mapperClass);
}
