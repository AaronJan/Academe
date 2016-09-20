<?php

namespace Academe\Instructions;

use Academe\Actions\Insert;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Instructions\Create as CreateContract;
use Academe\Contracts\Mapper\Mapper;

class Create extends WriteType implements CreateContract
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param \Academe\Contracts\Connection\Connection $connection
     * @param \Academe\Contracts\Mapper\Mapper         $mapper
     * @param array                                    $attributes
     * @return \Academe\Contracts\Connection\Query
     */
    protected function makeQuery(Connection\Connection $connection,
                                 Mapper $mapper,
                                 array $attributes)
    {
        $action = new Insert($attributes);

        return $connection->makeBuilder()
            ->parse($mapper->getSubject(), $action, $mapper->getCastManager());
    }

}
