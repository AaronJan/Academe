<?php

namespace Academe\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Academe extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'academe';
    }
}