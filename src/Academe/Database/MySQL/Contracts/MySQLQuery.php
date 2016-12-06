<?php

namespace Academe\Database\MySQL\Contracts;

use Academe\Contracts\Connection\Query;

interface MySQLQuery extends Query
{
    public function __construct($operation, $SQL, array $params, $hasChange);

    public function getSQL();

    public function getParameters();
}

