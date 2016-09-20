<?php

namespace Academe;

class Entity implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array
     */
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }

    function toArray()
    {
        return $this->attributes;
    }
}