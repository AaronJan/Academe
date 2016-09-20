<?php

namespace Academe\Instructions;

use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Mapper;

abstract class WriteType extends BaseExecutable
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $mapper->involve($this->getTransactions());

        $connection = $mapper->getConnection();
        $query      = $this->makeQuery($connection, $mapper, $this->attributes);

        $result = $connection->run($query);

        return $result;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param \Academe\Contracts\Mapper\Mapper         $mapper
     * @param array                                    $attributes
     * @return mixed
     */
    abstract protected function makeQuery(Connection\Connection $connection,
                                          Mapper $mapper,
                                          array $attributes);

}