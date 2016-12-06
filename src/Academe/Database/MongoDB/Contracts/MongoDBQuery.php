<?php

namespace Academe\Database\MongoDB\Contracts;

use Academe\Contracts\Connection\Query;

interface MongoDBQuery extends Query
{
    public function __construct($operation, $collection, $parameters, $hasChange);

    public function getCollection();

    public function getParameters();
}