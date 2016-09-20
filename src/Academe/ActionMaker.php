<?php

namespace Academe;

use Academe\Actions\Aggregate;
use Academe\Actions\Calculate;
use Academe\Actions\Delete;
use Academe\Actions\Insert;
use Academe\Actions\Select;
use Academe\Actions\Update;
use Academe\Contracts\ActionMaker as ActionMakerContract;
use Academe\Contracts\Connection\ConditionGroup;

class ActionMaker implements ActionMakerContract
{
    /**
     * @param string $method
     * @param string $field
     * @return Aggregate
     */
    public function aggregate($method, $field = '*')
    {
        return new Aggregate($method, $field);
    }

    /**
     * @param array|ConditionGroup $condition
     * @param string               $field
     * @param string               $operator
     * @param int                  $value
     * @return Calculate
     */
    public function calculate($condition, $field, $operator, $value = 1)
    {
        return new Calculate(
            $this->makeConditionGroup($condition),
            $field,
            $operator,
            $value
        );
    }

    /**
     * @param array|ConditionGroup $condition
     * @return Delete
     */
    public function delete($condition)
    {
        return new Delete($this->makeConditionGroup($condition));
    }

    /**
     * @param array $attributes
     * @return Insert
     */
    public function insert(array $attributes)
    {
        return new Insert($attributes);
    }

    /**
     * @param array|mixed $fields
     * @return Select
     */
    public function select($fields = ['*'])
    {
        return new Select($fields);
    }

    /**
     * @param array|ConditionGroup $condition
     * @param array                $attributes
     * @return Update
     */
    public function update($condition, array $attributes)
    {
        return new Update($this->makeConditionGroup($condition), $attributes);
    }

    /**
     * @param array|ConditionGroup $condition
     * @return ConditionGroup|null
     */
    protected function makeConditionGroup($condition)
    {
        if ($condition instanceof ConditionGroup) {
            return $condition;
        }

        return new \Academe\ConditionGroup($condition);
    }

}
