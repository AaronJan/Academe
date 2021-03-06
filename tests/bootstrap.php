<?php

require __DIR__ . '/../vendor/autoload.php';

//Helper functions =============================================================

/**
 * @param $object
 * @param $property
 * @param $value
 */
function setUnaccessibleObjectPropertyValue($object, $property, $value)
{
    $relection          = new \ReflectionClass($object);
    $reflectionProperty = $relection->getProperty($property);
    $reflectionProperty->setAccessible(true);
    $reflectionProperty->setValue($object, $value);
}

/**
 * @param $object
 * @param $property
 * @return mixed
 */
function getUnaccessibleObjectPropertyValue($object, $property)
{
    $relection          = new \ReflectionClass($object);
    $reflectionProperty = $relection->getProperty($property);
    $reflectionProperty->setAccessible(true);

    return $reflectionProperty->getValue($object);
}

/**
 * @param $object
 * @param $methodName
 * @return ReflectionMethod
 */
function getUnaccessibleObjectMethod($object, $methodName)
{
    $relection = new \ReflectionClass($object);
    $method    = $relection->getMethod($methodName);
    $method->setAccessible(true);

    return $method;
}