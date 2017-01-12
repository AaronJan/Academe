<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class AddToSet extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $values;

    /**
     * AddToSet constructor.
     *
     * @param string $field
     * @param array  $values
     */
    public function __construct($field, $values)
    {
        $this->field  = $field;
        $this->values = $values;
    }

    /**
     * @return string
     */
    protected function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    protected function getValues()
    {
        return $this->values;
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        $field  = $this->getField();
        $values = $this->getValues();

        if ($castManager !== null) {
            $values = $this->castEachValue($field, $values, $connectionType, $castManager);
        }

        return [
            '$addToSet' => [
                $field => ['$each' => $values],
            ],
        ];
    }
}