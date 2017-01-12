<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Pop extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var bool
     */
    protected $first;

    /**
     * Pop constructor.
     *
     * @param string $field
     * @param bool   $first
     */
    public function __construct($field, $first = true)
    {
        $this->field = $field;
        $this->first = $first;
    }

    /**
     * @return string
     */
    protected function getField()
    {
        return $this->field;
    }

    /**
     * @return bool
     */
    protected function getFirst()
    {
        return $this->first;
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        $value = $this->getFirst() ? - 1 : 1;

        return [
            '$pop' => [
                $this->getField() => $value,
            ],
        ];
    }
}