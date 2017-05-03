<?php

namespace Academe\Instructions\Traits;

use Academe\Constant\TransactionConstant;
use Academe\Contracts\Connection\Action;

trait Lockable
{
    /**
     * @var int|null
     */
    protected $lockLevel = TransactionConstant::LOCK_UNSET;

    /**
     * @param int|null $level
     * @return $this
     */
    public function setLock($level)
    {
        $this->lockLevel = $level;

        return $this;
    }

    /**
     * @return $this
     */
    public function setShareLock()
    {
        return $this->setLock(TransactionConstant::LOCK_FOR_SHARE);
    }

    /**
     * @return $this
     */
    public function setExclusiveLock()
    {
        return $this->setLock(TransactionConstant::LOCK_FOR_UPDATE);
    }

    /**
     * @return int|null
     */
    public function getLockLevel()
    {
        return $this->lockLevel;
    }

    /**
     * @param \Academe\Contracts\Connection\Action|\Academe\Actions\Traits\BeLockable $action
     * @param                                                                         $transactionSelectLockLevel
     */
    protected function setLockIfNotBeenSet(Action $action, $transactionSelectLockLevel)
    {
        if (! $action->hasLockBeenSet() && $transactionSelectLockLevel !== TransactionConstant::LOCK_UNSET) {
            $action->setLock($transactionSelectLockLevel);
        }
    }
}

