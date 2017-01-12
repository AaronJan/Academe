<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Set extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Set constructor.
     *
     * @param $field
     * @param $value
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
     * @return mixed
     */
    protected function getValue()
    {
        return $this->value;
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        $field = $this->field;
        $value = $this->getValue();

        if ($castManager !== null) {
            $value = $castManager->castIn($field, $value, $connectionType);
        }

        return [
            '$set' => [
                $field => $value,
            ],
        ];
    }
}