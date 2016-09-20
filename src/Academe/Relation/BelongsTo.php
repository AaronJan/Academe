<?php

namespace Academe\Relation;

use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Relation;
use Academe\Contracts\Academe;
use Academe\Relation\Handlers\BelongsToRelationHandler;

class BelongsTo implements Relation
{
    /**
     * @var string
     */
    protected $parentBlueprintClass;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $otherKey;

    /**
     * BelongsTo constructor.
     *
     * @param string $parentBlueprintClass
     * @param string $foreignKey
     * @param string $otherKey
     */
    public function __construct($parentBlueprintClass, $foreignKey, $otherKey)
    {
        $this->parentBlueprintClass = $parentBlueprintClass;
        $this->foreignKey           = $foreignKey;
        $this->otherKey             = $otherKey;
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param string                           $relationName
     * @param Academe                          $academe
     * @return \Academe\Relation\Handlers\BelongsToRelationHandler
     */
    public function makeHandler(Mapper $hostMapper, $relationName, Academe $academe)
    {
        return new BelongsToRelationHandler($this, $hostMapper, $relationName);
    }

    /**
     * @return string
     */
    public function getParentBlueprintClass()
    {
        return $this->parentBlueprintClass;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getOtherKey()
    {
        return $this->otherKey;
    }
}

