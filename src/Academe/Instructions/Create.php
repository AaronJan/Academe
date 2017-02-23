<?php

namespace Academe\Instructions;

use Academe\Actions\Insert;
use Academe\Contracts\CastManager;
use Academe\Contracts\Connection;
use Academe\Contracts\Mapper\Instructions\Create as CreateContract;
use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\Receipt;

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
     * @param Mapper $mapper
     * @return Receipt
     */
    public function execute(Mapper $mapper)
    {
        $connection = $mapper->getConnection();
        $query      = $this->makeQuery($connection, $mapper, $this->attributes);

        /**
         * @var $receipt Receipt
         */
        $receipt = $connection->run($query);

        $receipt->setupCastManager($this->makeCastHandler(
            $mapper->getCastManager(),
            $mapper->getPrimaryKey(),
            $connection->getType()
        ));

        return $receipt;
    }

    /**
     * @param \Academe\Contracts\CastManager $castManager
     * @param                                $primaryKey
     * @param                                $connectionType
     * @return \Closure
     */
    protected function makeCastHandler(CastManager $castManager, $primaryKey, $connectionType)
    {
        return function ($id) use ($castManager, $primaryKey, $connectionType) {
            return $castManager->castOut($primaryKey, $id, $connectionType);
        };
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
