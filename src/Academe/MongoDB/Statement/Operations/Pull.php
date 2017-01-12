<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Pull extends BaseOperation
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
     * Pull constructor.
     *
     * @param string $field
     * @param mixed  $value
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
        $field = $this->getField();
        $value = $this->getValue();

        if ($castManager !== null) {
            $value = $castManager->castIn($connectionType, $value, $connectionType);
        }

        return [
            '$pull' => [
                $field => $value,
            ],
        ];
    }
}