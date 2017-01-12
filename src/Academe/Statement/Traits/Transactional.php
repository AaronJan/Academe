<?php

namespace Academe\Statement\Traits;

use Academe\Contracts\Mapper\Instruction;
use Academe\Instructions\Traits\Transactional as TransactionalInstruction;

trait Transactional
{
    /**
     * @var array
     */
    protected $transactions = [];

    /**
     * @param \Academe\Contracts\Transaction|\Academe\Contracts\Transaction[] $transactions
     * @return $this
     */
    public function involve($transactions)
    {
        if (! is_array($transactions)) {
            $transactions = [$transactions];
        }

        $this->transactions = array_merge($this->transactions, $transactions);

        return $this;
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    protected function tweakTransaction(Instruction $instruction)
    {
        /**
         * @var $instruction TransactionalInstruction
         */
        if (! empty($this->transactions)) {
            $instruction->setTransactions($this->transactions);
        }
    }
}