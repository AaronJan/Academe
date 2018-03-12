<?php

namespace Academe\Laravel\Console\Traits;

trait MakeCommandHelper
{
    /**
     * @param $path
     * @param $rootNamespace
     * @return string
     */
    protected function convertPathToNamespace($path, $rootNamespace)
    {
        $appendNamespace = '\\' .
            trim(
                str_replace('/', '\\', $path),
                '\\'
            );

        return rtrim($rootNamespace, '\\') . $appendNamespace;
    }
}