<?php

namespace Academe\Instructions\Traits;

trait Transactional
{
    /**
     * @var \Academe\Contracts\Transaction[]
     */
    protected $transactions = [];

    /**
     * @param \Academe\Contracts\Transaction[] $transactions
     * @return $this
     */
    public function setTransactions(array $transactions)
    {
        $this->transactions = $transactions;

        return $this;
    }

    /**
     * @return \Academe\Contracts\Transaction[]
     */
    protected function getTransactions()
    {
        return $this->transactions;
    }

}