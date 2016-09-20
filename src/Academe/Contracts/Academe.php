<?php

namespace Academe\Contracts;

use Academe\Relation\Contracts\Bond;
use Academe\Relation\Contracts\RelationManager;
use Academe\Statement\Writer;

interface Academe
{
    /**
     * @param $config
     * @return \Academe\Contracts\Academe
     * @throws \Academe\Exceptions\ErrorException
     */
    static public function initialize($config);

    /**
     * @return \Academe\Contracts\Academe
     */
    static public function getInstance();

    /**
     * @return ConditionMaker
     */
    public function getConditionMaker();

    /**
     * @return \Academe\Contracts\ActionMaker
     */
    public function getActionMaker();

    /**
     * @param $class
     * @return Bond
     */
    public function getBond($class);

    /**
     * @return Writer
     */
    public function getWriter();
    
    /**
     * @param $bondClass
     * @return RelationManager
     */
    public function getRelationManager($bondClass);

    /**
     * @param null $name
     * @return \Academe\Contracts\Connection\Connection
     */
    public function getConnection($name = null);

    /**
     * @param $class
     * @return \Academe\Contracts\Mapper\Blueprint
     */
    public function getBlueprint($class);

    /**
     * @param $blueprintClass
     * @return \Academe\Contracts\Mapper\Mapper
     */
    public function getMapper($blueprintClass);
}
