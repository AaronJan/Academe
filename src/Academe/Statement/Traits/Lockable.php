<?php

namespace Academe\Statement\Traits;

use Academe\Instructions\Traits\Lockable as LockableInstruction;
use Academe\Contracts\Mapper\Instruction;

trait Lockable
{
    /**
     * @var null|int
     */
    protected $lockLevel;

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