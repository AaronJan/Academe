<?php

namespace Academe\Actions\Traits;

trait BeLockable
{
    /**
     * @var int
     */
    protected $lockLevel = 0;

    /**
     * @param int $level
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
        $this->lockLevel = 1;

        return $this;
    }

    /**
     * @return $this
     */
    public function setExclusiveLock()
    {
        $this->lockLevel = 2;

        return $this;
    }

    /**
     * @return int
     */
    public function getLockLevel()
    {
        return $this->lockLevel;
    }
}

