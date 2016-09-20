<?php

namespace Academe\Relation;

use Academe\Contracts\Academe;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Relation;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Relation\Handlers\HasManyRelationHandler;

class HasMany implements Relation
{
    /**
     * @var string
     */
    protected $childBlueprintClass;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    public function __construct($childBlueprintClass, $foreignKey, $localKey)
    {
        $this->childBlueprintClass = $childBlueprintClass;
        $this->foreignKey          = $foreignKey;
        $this->localKey            = $localKey;
    }

    /**
     * @return string
     */
    public function getChildBlueprintClass()
    {
        return $this->childBlueprintClass;
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
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $hostMapper
     * @param string                           $relationName
     * @param Academe                          $academe
     * @return \Academe\Relation\Contracts\RelationHandler
     */
    public function makeHandler(Mapper $hostMapper, $relationName, Academe $academe)
    {
        return new HasManyRelationHandler($this, $hostMapper, $relationName);
    }

}

