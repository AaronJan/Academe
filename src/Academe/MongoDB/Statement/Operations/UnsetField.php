<?php

namespace Academe\MongoDB\Statement\Operations;

use Academe\Contracts\CastManager;

class UnsetField extends BaseOperation
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * UnsetField constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return mixed
     */
    protected function fieldArrayToUnsetParameters(array $fields)
    {
        return array_reduce($this->getFields(), function ($carry, $field) {
            $carry[$field] = '';

            return $carry;
        }, []);
    }

    /**
     * @param                                     $connectionType
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    public function compile($connectionType, CastManager $castManager = null)
    {
        $values = $this->fieldArrayToUnsetParameters($this->getFields());

        return [
            '$unset' => $values,
        ];
    }
}