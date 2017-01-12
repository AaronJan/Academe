<?php

namespace Academe\Statement\Traits;

use Academe\Contracts\Mapper\Instruction;
use Academe\Instructions\Traits\Sortable as SortableInstruction;

trait Sortable
{
    /**
     * @var array
     */
    protected $orders = [];

    /**
     * @param $field
     * @param $direction
     * @return $this
     */
    public function sort($field, $direction)
    {
        $this->orders[] = [$field, $direction];

        return $this;
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    protected function tweakOrder(Instruction $instruction)
    {
        /**
         * @var $instruction SortableInstruction
         */
        if (! empty($this->orders)) {
            foreach ($this->orders as $order) {
                list($field, $direction) = $order;

                $instruction->sort($field, $direction);
            }
        }
    }

}