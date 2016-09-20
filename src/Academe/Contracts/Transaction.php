<?php

namespace Academe\Contracts;

use Academe\Contracts\Connection\Connection;

interface Transaction
{
    /**
     * @param Connection $connection
     */
    public function begin(Connection $connection);

    /**
     *
     */
    public function commit();

    /**
     *
     */
    public function rollBack();

}