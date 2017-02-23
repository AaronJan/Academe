<?php

namespace Academe\Database\MySQL;

use Academe\Constant\ConnectionConstant;
use Academe\Database\MySQL\Contracts\MySQLQuery as MySQLQueryContract;

class MySQLQuery implements MySQLQueryContract
{
    /**
     * @var string
     */
    protected $operation;

    /**
     * @var string
     */
    protected $SQL;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var bool
     */
    protected $hasChange;

    /**
     * MySQLQuery constructor.
     *
     * @param       $operation
     * @param       $SQL
     * @param array $params
     * @param       $hasChange
     */
    public function __construct($operation, $SQL, array $params, $hasChange)
    {
        $this->operation  = $operation;
        $this->SQL        = $SQL;
        $this->parameters = $params;
        $this->hasChange  = $hasChange;
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @return int
     */
    public function getConnectionType()
    {
        return ConnectionConstant::TYPE_MYSQL;
    }

    /**
     * @return array
     */
    public function getSQL()
    {
        return $this->SQL;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return bool
     */
    public function hasChange()
    {
        return $this->hasChange;
    }
}
