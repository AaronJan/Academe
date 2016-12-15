<?php

namespace Academe\Contracts;

interface Receipt
{
    /**
     * @param callable $castHandler
     * @return void
     */
    public function setupCastManager(callable $castHandler);

    /**
     * @param null|string $sequence
     * @return mixed
     */
    public function getID($sequence = null);

    /**
     * @return int
     */
    public function getCount();
}