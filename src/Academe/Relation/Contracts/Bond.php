<?php

namespace Academe\Relation\Contracts;

use Academe\Contracts\Mapper\Blueprint;

interface Bond extends Blueprint
{
    /**
     * @return string
     */
    public function managerClass();

    /**
     * @return string
     */
    public function hostBlueprintClass();

    /**
     * @return string
     */
    public function hostKeyField();

    /**
     * @return string
     */
    public function guestBlueprintClass();

    /**
     * @return string
     */
    public function guestKeyField();

    /**
     * @return string
     */
    public function pivotField();

}
