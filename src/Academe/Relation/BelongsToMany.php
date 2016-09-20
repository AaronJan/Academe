<?php

namespace Academe\Relation;

use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Relation;
use Academe\Contracts\Academe;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Relation\Handlers\BelongsToManyRelationHandler;

class BelongsToMany implements Relation
{
    /**
     * @var string
     */
    protected $bondClass;

    /**
     * @var bool
     */
    protected $isHost;

    /**
     * BelongsToMany constructor.
     *
     * @param      $bondClass
     * @param bool $isHost
     */
    public function __construct($bondClass, $isHost)
    {
        $this->bondClass = $bondClass;
        $this->isHost    = $isHost;
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param string                           $relationName
     * @param Academe                          $academe
     * @return \Academe\Relation\Contracts\RelationHandler
     */
    public function makeHandler(Mapper $hostMapper, $relationName, Academe $academe)
    {
        return new BelongsToManyRelationHandler($this, $relationName);
    }

    /**
     * @return string
     */
    public function getBondClass()
    {
        return $this->bondClass;
    }

    /**
     * @return bool
     */
    public function isHost()
    {
        return $this->isHost;
    }

}

