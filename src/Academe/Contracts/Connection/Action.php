<?php

namespace Academe\Contracts\Connection;

interface Action
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getParameters();
}
