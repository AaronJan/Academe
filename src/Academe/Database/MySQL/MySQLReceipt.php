<?php

namespace Academe\Database\MySQL;

use Academe\BaseReceipt;
use \Doctrine\DBAL\Connection as DBALConnection;

class MySQLReceipt extends BaseReceipt
{
    /**
     * @var int
     */
    protected $count;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $DBALConnection;

    /**
     * MySQLReceipt constructor.
     *
     * @param \Doctrine\DBAL\Connection $DBALConnection
     * @param                           $count
     */
    public function __construct(DBALConnection $DBALConnection, $count)
    {
        $this->DBALConnection = $DBALConnection;
        $this->count          = $count;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDBALConnection()
    {
        return $this->DBALConnection;
    }

    /**
     * @param null|string $sequence
     * @return string
     */
    public function getID($sequence = null)
    {
        $id = $this->getDBALConnection()->lastInsertId($sequence);

        return $this->castIfAvailable($id);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

}