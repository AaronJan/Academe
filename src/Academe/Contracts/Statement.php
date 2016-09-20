<?php

namespace Academe\Contracts;

interface Statement
{
    /**
     * @return \Academe\Contracts\Connection\Condition[]
     */
    public function getConditions();
    
}