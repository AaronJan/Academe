<?php

namespace Academe\Casting;

use Academe\Casting\Casters\BooleanCaster;
use Academe\Casting\Casters\DateCaster;
use Academe\Casting\Casters\DateTimeCaster;
use Academe\Casting\Casters\DecimalCaster;
use Academe\Casting\Casters\DoubleCaster;
use Academe\Casting\Casters\FloatCaster;
use Academe\Casting\Casters\GroupCaster;
use Academe\Casting\Casters\IntegerAsBooleanCaster;
use Academe\Casting\Casters\IntegerCaster;
use Academe\Casting\Casters\JsonAsArrayCaster;
use Academe\Casting\Casters\JSONCaster;
use Academe\Casting\Casters\ListCaster;
use Academe\Casting\Casters\MongoDBObjectIDCaster;
use Academe\Casting\Casters\MongodbObjectIdAsStringCaster;
use Academe\Casting\Casters\SetCaster;
use Academe\Casting\Casters\StringCaster;
use Academe\Casting\Casters\VersionCaster;
use Academe\Contracts\Caster;

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
     * @return \Academe\Casting\Casters\SetCaster
     */
    static public function set(array $config)
    {
        return new SetCaster($config);
    }

    /**
     * @param \Academe\Contracts\Caster|null $caster
     * @return \Academe\Casting\Casters\GroupCaster
     */
    static public function group(Caster $caster = null)
    {
        return new GroupCaster($caster);
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
    public static function integer()
    {
        return new IntegerCaster();
    }

    /**
     * @return \Academe\Casting\Casters\JSONCaster
     * @deprecated Use #JsonAsArray instead.
     */
    public static function JSON()
    {
        return new JSONCaster();
    }

    /**
     * @param int $encodeOption
     * @param int $decodeOption
     * @return \Academe\Casting\Casters\JsonAsArrayCaster
     */
    public static function jsonAsArray($encodeOption = 0, $decodeOption = 0)
    {
        return new JsonAsArrayCaster($encodeOption, $decodeOption);
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
     * @return \Academe\Casting\Casters\MongodbObjectIdAsStringCaster
     * @deprecated Use #mongodbObjectIdAsString objectId instead.
     */
    public static function ObjectID()
    {
        return new MongodbObjectIdAsStringCaster();
    }

    /**
     * @return \Academe\Casting\Casters\MongodbObjectIdCaster
     */
    public static function mongodbObjectId()
    {
        return new MongodbObjectIdCaster();
    }

    /**
     * @return \Academe\Casting\Casters\MongodbObjectIdAsStringCaster
     */
    public static function mongodbObjectIdAsString()
    {
        return new MongodbObjectIdAsStringCaster();
    }

    /**
     * @return \Academe\Casting\Casters\StringCaster
     */
    static public function string()
    {
        return new StringCaster();
    }

    /**
     * @return \Academe\Casting\Casters\DecimalCaster
     */
    static public function decimal()
    {
        return new DecimalCaster();
    }

    /**
     * @return \Academe\Casting\Casters\VersionCaster
     */
    public static function version()
    {
        return new VersionCaster();
    }

}
