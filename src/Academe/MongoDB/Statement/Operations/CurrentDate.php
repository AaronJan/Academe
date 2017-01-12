<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class CurrentDate extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var bool
     */
    protected $useDate;

    /**
     * CurrentDate constructor.
     *
     * @param string $field
     * @param bool   $useDate
     */
    public function __construct($field, $useDate = true)
    {
        $this->field   = $field;
        $this->useDate = $useDate;
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
    protected function getUseDate()
    {
        return $this->useDate;
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        $value = ['$type' => ($this->getUseDate() ? 'date' : 'timestamp')];

        return [
            '$currentDate' => [
                $this->getField() => $value,
            ],
        ];
    }
}