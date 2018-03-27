<?php

namespace Academe;

class Aggregation extends \ArrayObject implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $aggregationField;

    /**
     * Aggregation constructor.
     *
     * @param array $attribtes
     * @param $field
     */
    public function __construct(array $attribtes, $field)
    {
        parent::__construct($attribtes, \ArrayObject::ARRAY_AS_PROPS);

        $this->aggregationField = $field;
    }

    /**
     * @return array
     */
    public function toAttributes()
    {
        return $this->getArrayCopy();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return mixed|null
     */
    public function getResult()
    {
        return $this->offsetGet($this->aggregationField);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return (array)$this;
    }

    /**
     * @param mixed $index
     * @return mixed|null
     */
    public function offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return parent::offsetGet($index);
        }

        return null;
    }
}