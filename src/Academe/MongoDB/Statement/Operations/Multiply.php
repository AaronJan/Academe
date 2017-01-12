<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Multiply extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var number
     */
    protected $num;

    /**
     * Multiply constructor.
     *
     * @param $field
     * @param $num
     */
    public function __construct($field, $num)
    {
        $this->field = $field;
        $this->num   = $num;
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
    protected function getNum()
    {
        return $this->num;
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        return [
            '$mul' => [
                $this->getField() => $this->getNum(),
            ],
        ];
    }
}