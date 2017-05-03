<?php

namespace Academe\Constant;

use Doctrine\DBAL\Connection;

class TransactionConstant
{
    const LOCK_UNSET      = null;
    const LOCK_NONE       = 0;
    const LOCK_FOR_SHARE  = 1;
    const LOCK_FOR_UPDATE = 2;

    const READ_UNCOMMITTED = Connection::TRANSACTION_READ_UNCOMMITTED;
    const READ_COMMITTED   = Connection::TRANSACTION_READ_COMMITTED;
    const REPEATABLE_READ  = Connection::TRANSACTION_REPEATABLE_READ;
    const SERIALIZABLE     = Connection::TRANSACTION_SERIALIZABLE;
}