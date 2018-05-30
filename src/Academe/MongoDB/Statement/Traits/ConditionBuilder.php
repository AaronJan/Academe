<?php

namespace Academe\MongoDB\Statement\Traits;

use Academe\Contracts\Connection\Condition;
use Academe\Contracts\Connection\ConditionGroup;

trait ConditionBuilder
{
    /**
     * @return \Academe\Contracts\ConditionMaker
     */
    abstract public function getConditionMaker();

    /**
     * @param Condition|ConditionGroup $condition
     */
    abstract protected function addCondition($condition);

    /**
     * @param      $field
     * @param bool $isExists
     * @return $this
     */
    public function fieldExists($field, $isExists = true)
    {
        $this->addCondition($this->getConditionMaker()->fieldExists($field, $isExists));

        return $this;
    }

    /**
     * @param $field
     * @param $typeAlias
     * @return $this
     */
    public function typeIs($field, $typeAlias)
    {
        $this->addCondition($this->getConditionMaker()->typeIs($field, $typeAlias));

        return $this;
    }

    /**
     * @param $field
     * @param $values
     * @return $this
     */
    public function containsAll($field, $values)
    {
        $this->addCondition($this->getConditionMaker()->containsAll($field, $values));

        return $this;
    }

    /**
     * @param $field
     * @param $size
     * @return $this
     */
    public function sizeIs($field, $size)
    {
        $this->addCondition($this->getConditionMaker()->sizeIs($field, $size));

        return $this;
    }

    /**
     * @param $field
     * @param $condition
     * @return $this
     */
    public function elementMatch($field, $condition)
    {
        $this->addCondition($this->getConditionMaker()->elementMatch($field, $condition));

        return $this;
    }

}