<?php

namespace Academe\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Writer extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'academe.writer';
    }
}