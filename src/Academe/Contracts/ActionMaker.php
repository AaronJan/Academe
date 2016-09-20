<?php

namespace Academe\Contracts;

use Academe\Actions\Aggregate;
use Academe\Actions\Calculate;
use Academe\Actions\Delete;
use Academe\Actions\Insert;
use Academe\Actions\Select;
use Academe\Actions\Update;
use Academe\Contracts\Connection\ConditionGroup;

interface ActionMaker
{
    /**
     * @param string $method
     * @param string $field
     * @return Aggregate
     */
    public function aggregate($method, $field = '*');

    /**
     * @param array|ConditionGroup $condition
     * @param string               $field
     * @param string               $operator
     * @param int                  $value
     * @return Calculate
     */
    public function calculate($condition, $field, $operator, $value = 1);

    /**
     * @param array|ConditionGroup $condition
     * @return Delete
     */
    public function delete($condition);

    /**
     * @param array $attributes
     * @return Insert
     */
    public function insert(array $attributes);

    /**
     * @param array|mixed $fields
     * @return Select
     */
    public function select($fields = ['*']);

    /**
     * @param array|ConditionGroup $condition
     * @param array                $attributes
     * @return Update
     */
    public function update($condition, array $attributes);
}
