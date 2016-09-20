<?php

namespace Academe\Actions\Traits;

use Academe\Contracts\Connection\Formation;

trait BeFormatted
{
    /**
     * @var Formation|null
     */
    protected $formation = null;

    /**
     * @param Formation $formation
     * @return $this
     */
    public function setFormation(Formation $formation)
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Formation|null
     */
    public function getFormation()
    {
        return $this->formation;
    }

}