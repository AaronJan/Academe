<?php

namespace Academe\Support;

use ReflectionClass;
use LogicException;

class ClassInstanceBuilder
{
    /**
     * @param       $class
     * @param array $constructParameters
     * @return object
     */
    static public function makeInstance($class, array $constructParameters = [])
    {
        $reflector = new ReflectionClass($class);

        if (! $reflector->isInstantiable()) {
            throw new LogicException("[] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class;
        }

        return $reflector->newInstanceArgs($constructParameters);
    }
}