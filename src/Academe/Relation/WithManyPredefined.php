<?php

namespace Academe\Relation;

use Academe\Contracts\Academe;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\Relation;
use Academe\Relation\Handlers\WithManyPredefinedRelationHandler;

class WithManyPredefined implements Relation
{
    /**
     * @var array
     */
    protected $predefined;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    public function __construct($predefined, $foreignKey, $localKey)
    {
        $this->predefined = $this->getRealData($predefined);
        $this->foreignKey = $foreignKey;
        $this->localKey   = $localKey;
    }

    /**
     * @param $predefined
     * @return mixed
     */
    private function getRealData($predefined)
    {
        return is_callable($predefined) ? call_user_func($predefined) : $predefined;
    }

    /**
     * @return string
     */
    public function getPredefined()
    {
        return $this->predefined;
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
        return new WithManyPredefinedRelationHandler($this, $hostMapper, $relationName);
    }

}

