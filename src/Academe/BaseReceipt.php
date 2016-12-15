<?php

namespace Academe;

use Academe\Contracts\Receipt;

abstract class BaseReceipt implements Receipt
{
    /**
     * @var callable|null
     */
    protected $castHandler;

    /**
     * @param callable $castHandler
     */
    public function setupCastManager(callable $castHandler)
    {
        $this->castHandler = $castHandler;
    }

    /**
     * @return callable|null
     */
    protected function getCastHandler()
    {
        return $this->castHandler;
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function castIfAvailable($id)
    {
        $castHandler = $this->getCastHandler();

        return $castHandler ? $castHandler($id) : $id;
    }


}