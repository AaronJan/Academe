<?php

namespace Academe\Statement\Traits;

use Academe\Constant\TransactionConstant;
use Academe\Instructions\Traits\Lockable as LockableInstruction;
use Academe\Contracts\Mapper\Instruction;

trait Lockable
{
    /**
     * @var integer
     */
    protected $lockLevel = TransactionConstant::LOCK_UNSET;

    /**
     * @return $this
     */
    public function lockForShare()
    {
        return $this->setLockLevel(TransactionConstant::LOCK_FOR_SHARE);
    }

    /**
     * @return $this
     */
    public function lock()
    {
        return $this->setLockLevel(TransactionConstant::LOCK_FOR_UPDATE);
    }

    /**
     * @return int|null
     */
    public function getLockLevel()
    {
        return $this->lockLevel;
    }

    /**
     * @param integer|null $level
     * @return $this
     */
    public function setLockLevel($level)
    {
        $this->lockLevel = $level;

        return $this;
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    protected function tweakLock(Instruction $instruction)
    {
        /**
         * @var $instruction LockableInstruction
         */
        if ($this->lockLevel !== TransactionConstant::LOCK_UNSET) {
            $instruction->setLock($this->lockLevel);
        }
    }

}