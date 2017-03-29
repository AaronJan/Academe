<?php

namespace Academe;

class Model extends \ArrayObject implements \JsonSerializable
{
    /**
     * Model constructor.
     *
     * @param array $attribtes
     */
    public function __construct(array $attribtes = [])
    {
        parent::__construct($attribtes, \ArrayObject::ARRAY_AS_PROPS);
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
        return (array) $this;
    }

}