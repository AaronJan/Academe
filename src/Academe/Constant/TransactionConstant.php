<?php

namespace Academe\Constant;

use Doctrine\DBAL\Connection;

class TransactionConstant
{
    const READ_UNCOMMITTED = Connection::TRANSACTION_READ_UNCOMMITTED;
    const READ_COMMITTED   = Connection::TRANSACTION_READ_COMMITTED;
    const REPEATABLE_READ  = Connection::TRANSACTION_REPEATABLE_READ;
    const SERIALIZABLE     = Connection::TRANSACTION_SERIALIZABLE;
}