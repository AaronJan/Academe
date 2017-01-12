<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Max extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var number
     */
    protected $value;

    /**
     * Min constructor.
     *
     * @param string $field
     * @param number $value
     */
    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @return string
     */
    protected function getField()
    {
        return $this->field;
    }

    /**
     * @return number
     */
    protected function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        $field = $this->getField();
        $value = $this->getValue();

        if ($castManager !== null) {
            $value = $castManager->castIn($field, $value, $connectionType);
        }

        return [
            '$max' => [
                $field => $value,
            ],
        ];
    }
}