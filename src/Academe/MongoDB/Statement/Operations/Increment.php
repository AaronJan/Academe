<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Increment extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var int
     */
    protected $amount;

    /**
     * Increment constructor.
     *
     * @param     $field
     * @param int $amount
     */
    public function __construct($field, $amount = 1)
    {
        $this->field  = $field;
        $this->amount = $amount;
    }

    protected function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    protected function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        return [
            '$inc' => [
                $this->getField() => $this->getAmount(),
            ],
        ];
    }
}