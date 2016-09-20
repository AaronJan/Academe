<?php

namespace Academe\Contracts;

use Academe\Contracts\Connection\Formation;

interface Directable
{
    /**
     * @param Formation $formation
     * @return $this
     */
    public function setFormation(Formation $formation);

    /**
     * @return Formation|null
     */
    public function getFormation();
}
