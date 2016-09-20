<?php

namespace Academe\Relation\Contracts;

use Academe\Contracts\Mapper\Blueprint;

interface Bond extends Blueprint
{
    /**
     * @return string
     */
    public function managerClass();

    public function hostBlueprintClass();

    public function hostKeyAttribute();

    public function guestBlueprintClass();

    public function guestKeyAttribute();

}
