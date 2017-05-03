<?php

namespace Academe\Statement\Traits;

use Academe\Instructions\Traits\Lockable as LockableInstruction;
use Academe\Contracts\Mapper\Instruction;

trait Lockable
{
    /**
     * @var integer
     */
    protected $lockLevel = 0;

    /**
     * @return $this
     */
    public function lockForShare()
    {
        $this->lockLevel = 1;

        return $this;
    }

    /**
     * @return $this
     */
    public function lock()
    {
        $this->lockLevel = 2;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLockLevel()
    {
        return $this->lockLevel;
    }

    /**
     * @param integer $level
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
        if ($this->lockLevel) {
            $instruction->setLock($this->lockLevel);
        }
    }

}