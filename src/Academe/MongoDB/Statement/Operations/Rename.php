<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class Rename extends BaseOperation
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $newName;

    public function __construct($field, $newName)
    {
        $this->field   = $field;
        $this->newName = $newName;
    }

    /**
     * @return string
     */
    protected function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    protected function getNewName()
    {
        return $this->newName;
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        return [
            '$rename' => [
                $this->getField() => $this->getNewName(),
            ],
        ];
    }
}