<?php

namespace Academe\MongoDB\Actions;

use Academe\Contracts\Connection\Action;
use Academe\Contracts\Connection\ConditionGroup;
use Academe\MongoDB\Statement\MongoDBManualUpdate;

/**
 * Class AdvanceUpdate
 *
 * @package Academe\MongoDB\Actions
 */
class AdvanceUpdateAction implements Action
{
    /**
     * @var ConditionGroup|null
     */
    protected $conditionGroup;

    /**
     * @var \Academe\MongoDB\Statement\MongoDBManualUpdate
     */
    protected $mongoDBManualUpdate;

    /**
     * AdvanceUpdateAction constructor.
     *
     * @param \Academe\Contracts\Connection\ConditionGroup   $conditionGroup
     * @param \Academe\MongoDB\Statement\MongoDBManualUpdate $mongoDBManualUpdate
     */
    public function __construct(ConditionGroup $conditionGroup, MongoDBManualUpdate $mongoDBManualUpdate)
    {
        $this->conditionGroup      = $conditionGroup;
        $this->mongoDBManualUpdate = $mongoDBManualUpdate;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'advanceupdate';
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return [$this->conditionGroup, $this->mongoDBManualUpdate];
    }

}
