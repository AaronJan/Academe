<?php

namespace Academe\Contracts\Mapper;

interface Executable
{
    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper);

}
