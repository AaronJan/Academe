<?php

namespace Academe\Instructions;

use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;

abstract class WriteType extends BaseExecutable
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param \Academe\Contracts\Mapper\Mapper         $mapper
     * @param array                                    $attributes
     * @return mixed
     */
    abstract protected function makeQuery(Connection\Connection $connection,
                                          Mapper $mapper,
                                          array $attributes);

}