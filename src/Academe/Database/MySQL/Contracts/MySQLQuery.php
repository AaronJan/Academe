<?php

namespace Academe\Database\MySQL\Contracts;

use Academe\Contracts\Connection\Query;

interface MySQLQuery extends Query
{
    public function __construct($operation, $SQL, array $parameters, $hasChange);

    public function getSQL();

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @return array
     */
    public function getHint();
}

