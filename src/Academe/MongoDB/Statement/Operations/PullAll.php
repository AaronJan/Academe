<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class PullAll extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var array
     */
    protected $values;

    /**
     * PullAll constructor.
     *
     * @param $field
     * @param $values
     */
    public function __construct($field, $values)
    {
        $this->field  = $field;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return array
     */
    public function getValues()
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
            '$pullAll' => [
                $field => $values,
            ],
        ];
    }
}