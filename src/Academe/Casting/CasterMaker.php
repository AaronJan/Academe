<?php

namespace Academe\Casting;

use Academe\Casting\Casters\BooleanCaster;
use Academe\Casting\Casters\DateCaster;
use Academe\Casting\Casters\DateTimeCaster;
use Academe\Casting\Casters\DoubleCaster;
use Academe\Casting\Casters\FloatCaster;
use Academe\Casting\Casters\GroupCaster;
use Academe\Casting\Casters\IntegerAsBooleanCaster;
use Academe\Casting\Casters\IntegerCaster;
use Academe\Casting\Casters\JSONCaster;
use Academe\Casting\Casters\ListCaster;
use Academe\Casting\Casters\MongoDBObjectIDCaster;
use Academe\Casting\Casters\StringCaster;

class CasterMaker
{
    /**
     * @return \Academe\Casting\Casters\BooleanCaster
     */
    static public function boolean()
    {
        return new BooleanCaster();
    }

    /**
     * @return \Academe\Casting\Casters\DateCaster
     */
    static public function date()
    {
        return new DateCaster();
    }

    /**
     * @return \Academe\Casting\Casters\DateTimeCaster
     */
    static public function dateTime()
    {
        return new DateTimeCaster();
    }

    /**
     * @return \Academe\Casting\Casters\DoubleCaster
     */
    static public function double()
    {
        return new DoubleCaster();
    }

    /**
     * @return \Academe\Casting\Casters\FloatCaster
     */
    static public function float()
    {
        return new FloatCaster();
    }

    /**
     * @param array $config
     * @return \Academe\Casting\Casters\GroupCaster
     */
    static public function group(array $config)
    {
        return new GroupCaster($config);
    }

    /**
     * @return \Academe\Casting\Casters\IntegerAsBooleanCaster
     */
    static public function integerAsBoolean()
    {
        return new IntegerAsBooleanCaster();
    }

    /**
     * @return \Academe\Casting\Casters\IntegerCaster
     */
    static public function integer()
    {
        return new IntegerCaster();
    }

    /**
     * @return \Academe\Casting\Casters\JSONCaster
     */
    static public function JSON()
    {
        return new JSONCaster();
    }

    /**
     * @param array $config
     * @return \Academe\Casting\Casters\ListCaster
     */
    static public function list(array $config)
    {
        return new ListCaster($config);
    }

    /**
     * @return \Academe\Casting\Casters\MongoDBObjectIDCaster
     */
    static public function ObjectID()
    {
        return new MongoDBObjectIDCaster();
    }

    /**
     * @return \Academe\Casting\Casters\StringCaster
     */
    static public function string()
    {
        return new StringCaster();
    }
}
