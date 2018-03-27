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
     * @var array
     */
    protected $hint;

    /**
     * MySQLQuery constructor.
     *
     * @param $operation
     * @param $SQL
     * @param array $parameters
     * @param $hasChange
     * @param array $hint
     */
    public function __construct($operation, $SQL, array $parameters, $hasChange, $hint = [])
    {
        $this->operation = $operation;
        $this->SQL = $SQL;
        $this->parameters = $parameters;
        $this->hasChange = $hasChange;
        $this->hint = $hint;
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
     * @return string
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

    /**
     * @return array
     */
    public function getHint()
    {
        return $this->hint;
    }
}
