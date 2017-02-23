<?php

namespace Academe\Contracts\Action;

use Academe\Contracts\Connection\Formation;

interface Directable
{
    /**
     * @return Formation
     */
    public function getFormation();

    /**
     * @param \Academe\Contracts\Connection\Formation $formation
     */
    public function setFormation(Formation $formation);
}