<?php

namespace Academe;

class Model implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * Model constructor.
     *
     * @param array $attribtes
     */
    public function __construct(array $attribtes = [])
    {
        $this->attributes = $attribtes;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public function newInstance(array $attributes)
    {
        return new static($attributes);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->attributes[$offset] ?? null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    function toArray()
    {
        return $this->attributes;
    }

}