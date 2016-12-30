<?php

namespace Academe\Relation\Handlers;

use Academe\Academe;
use Academe\Contracts\Mapper\Mapper;
use Academe\Relation\Contracts\RelationHandler;
use Academe\Statement\RelationSubStatement;

abstract class BaseRelationHandler implements RelationHandler
{
    /**
     * @var Mapper
     */
    protected $hostMapper;

    /**
     * @return \Academe\Contracts\Academe
     */
    protected function getAcademe()
    {
        return Academe::getInstance();
    }

    /**
     * @param \Academe\Contracts\Academe $academe
     * @return \Academe\Statement\RelationSubStatement
     */
    protected function makeLimitedFluentStatement(\Academe\Contracts\Academe $academe)
    {
        return new RelationSubStatement($academe->getConditionMaker());
    }

}