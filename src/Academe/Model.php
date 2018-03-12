<?php

namespace Academe;

class Model extends \ArrayObject implements \JsonSerializable
{
    /**
     * @var null|string
     */
    protected $primaryKeyField = null;

    /**
     * @var array
     */
    protected $relationFields = [];

    /**
     * Model constructor.
     *
     * @param array       $attribtes
     * @param null|string $primaryKeyField
     */
    public function __construct(array $attribtes = [], $primaryKeyField = null)
    {
        parent::__construct($attribtes, \ArrayObject::ARRAY_AS_PROPS);
        $this->primaryKeyField = $primaryKeyField;
    }

    /**
     * @param array       $attributes
     * @param null|string $primaryKeyField
     * @return static
     */
    public function newInstance(array $attributes, $primaryKeyField = null)
    {
        return new static($attributes, $primaryKeyField);
    }

    /**
     * @return array
     */
    public function toAttributes()
    {
        $array       = $this->getArrayCopy();
        $allKeys     = array_keys($array);
        $excludeKeys = array_filter(array_merge($this->relationFields, [$this->primaryKeyField]));
        $keys        = array_diff($allKeys, $excludeKeys);

        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * @param string $field
     * @param mixed  $data
     */
    public function setRelation($field, $data)
    {
        $this->relationFields[] = $field;

        $this[$field] = $data;
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

    /**
     * @param mixed $index
     * @return mixed|null
     */
    function offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return parent::offsetGet($index);
        }

        return null;
    }

}