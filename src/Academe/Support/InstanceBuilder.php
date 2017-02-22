<?php

namespace Academe\Support;

use ReflectionClass;
use LogicException;

class InstanceBuilder
{
    /**
     * @param       $class
     * @param array $constructParameters
     * @return object
     */
    static public function make($class, array $constructParameters = [])
    {
        $reflector = new ReflectionClass($class);

        if (! $reflector->isInstantiable()) {
            throw new LogicException("[{$class}] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class;
        }

        return $reflector->newInstanceArgs($constructParameters);
    }
}