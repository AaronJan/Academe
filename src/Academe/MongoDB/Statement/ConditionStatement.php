<?php

namespace Academe\MongoDB\Statement;

use Academe\Statement\ConditionStatement as BaseConditionStatement;

class ConditionStatement extends BaseConditionStatement
{
    use Traits\ConditionBuilder;

}