<?php

namespace Academe\Relation\Contracts;

interface RelationManager
{
    /**
     * @return RelationPivot
     */
    public function fromHost();

    /**
     * @return RelationPivot
     */
    public function fromGuest();

}
