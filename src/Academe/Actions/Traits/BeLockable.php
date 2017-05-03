<?php

namespace Academe\Actions\Traits;

use Academe\Constant\TransactionConstant;

trait BeLockable
{
    /**
     * @var int|null
     */
    protected $lockLevel = TransactionConstant::LOCK_UNSET;

    /**
     * @return bool
     */
    public function hasLockBeenSet()
    {
        return $this->lockLevel !== TransactionConstant::LOCK_UNSET;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLock($level)
    {
        $this->lockBeenSet = true;
        $this->lockLevel   = $level;

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
}

