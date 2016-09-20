<?php

namespace Academe\Relation\Managers;

use Academe\Contracts\Academe;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Bond;
use Academe\Relation\Contracts\RelationManager;
use Academe\Relation\Contracts\RelationPivot;

class ManyToManyRelationManager implements RelationManager
{
    /**
     * @var \Academe\Relation\Contracts\Bond
     */
    protected $bond;

    /**
     * @var \Academe\Contracts\Academe
     */
    protected $academe;

    /**
     * @var Mapper
     */
    protected $pivotMapper;

    /**
     * @var RelationPivot
     */
    protected $hostToGuestPivotHandler;

    /**
     * @var RelationPivot
     */
    protected $guestToHostPivotHandler;

    /**
     * ManyToManyRelationManager constructor.
     *
     * @param \Academe\Relation\Contracts\Bond $bond
     * @param \Academe\Contracts\Academe       $academe
     */
    public function __construct(Bond $bond, Academe $academe)
    {
        $this->bond        = $bond;
        $this->academe     = $academe;
        $this->pivotMapper = $academe->getMapper(get_class($bond));

        $this->setupConfig($bond);
    }

    /**
     * @return \Academe\Contracts\Academe
     */
    protected function getAcademe()
    {
        return $this->academe;
    }

    /**
     * @return \Academe\Contracts\Mapper\Mapper
     */
    protected function getPivotMapper()
    {
        return $this->pivotMapper;
    }

    /**
     * @param \Academe\Relation\Contracts\Bond $bond
     */
    protected function setupConfig(Bond $bond)
    {
        $pivotMapper = $this->getPivotMapper();

        $this->hostToGuestPivotHandler = new ManyToManyRelationPivot(
            $pivotMapper,
            $bond->hostKeyAttribute(),
            $bond->guestKeyAttribute()
        );
        $this->guestToHostPivotHandler = new ManyToManyRelationPivot(
            $pivotMapper,
            $bond->guestKeyAttribute(),
            $bond->hostKeyAttribute()
        );
    }

    /**
     * @return RelationPivot
     */
    public function fromHost()
    {
        return $this->hostToGuestPivotHandler;
    }

    /**
     * @return RelationPivot
     */
    public function fromGuest()
    {
        return $this->guestToHostPivotHandler;
    }

}