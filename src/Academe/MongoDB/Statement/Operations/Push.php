<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Push extends BaseOperation
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
     * @var array|int|null
     */
    protected $sort;

    /**
     * @var int|null
     */
    protected $slice;

    /**
     * @var int|null
     */
    protected $position;

    /**
     * Push constructor.
     *
     * @param string         $field
     * @param array          $values
     * @param null|int|array $sort 1, -1, ['field' => 1]
     * @param null|int       $slice
     * @param int|null       $position
     */
    public function __construct($field, array $values, $sort = null, $slice = null, $position = null)
    {
        $this->field    = $field;
        $this->values   = $values;
        $this->sort     = $sort;
        $this->slice    = $slice;
        $this->position = $position;
    }

    protected function getField()
    {
        return $this->field;
    }

    protected function getValues()
    {
        return $this->values;
    }

    protected function getSort()
    {
        return $this->sort;
    }

    /**
     * @return null
     */
    protected function getSlice()
    {
        return $this->slice;
    }

    /**
     * @return null
     */
    protected function getPosition()
    {
        return $this->position;
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
            $values = $castManager->castIn($field, $values, $connectionType);
        }


        // use $each modifier by default
        $pullContent = [
            '$each' => $values,
        ];

        // $sort modifier
        $sort = $this->getSort();
        if (! is_null($sort)) {
            $pullContent['$sort'] = $sort;
        }

        // $slice modifier
        $slice = $this->getSlice();
        if (! is_null($slice)) {
            $pullContent['$slice'] = $slice;
        }

        // $position modifier
        $position = $this->getPosition();
        if (! is_null($position)) {
            $pullContent['$position'] = $position;
        }

        return [
            '$push' => [
                $this->getField() => $pullContent,
            ],
        ];
    }

}