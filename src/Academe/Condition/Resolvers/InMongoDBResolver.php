<?php

namespace Academe\Condition\Resolvers;

use Academe\Casting\Casters\GroupCaster;
use Academe\Condition\In;
use Academe\Contracts\Caster;
use Academe\Contracts\CastManager;

class InMongoDBResolver
{
    /**
     * @param $connectionType
     * @param In $in
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   In $in,
                                   CastManager $castManager = null)
    {
        list($name, $values) = $in->getParameters();

        /* @var $fieldCaster Caster|GroupCaster */
        $fieldCaster = $castManager === null ? null : $castManager->getCaster($name);
        if ($fieldCaster === null) {
            return [$name => ['$in' => $values]];
        }

        return [
            $name => [
                '$in' => static::castValues(
                    (
                    $fieldCaster instanceof GroupCaster ?
                        $fieldCaster->getCaster() :
                        $fieldCaster
                    ),
                    $connectionType,
                    $values
                )
            ]
        ];
    }

    /**
     * @param \Academe\Contracts\Caster $caster
     * @param $connectionType
     * @param array $values
     * @return array
     */
    protected static function castValues(Caster $caster, $connectionType, array $values)
    {
        return array_map(function ($value) use ($caster, $connectionType) {
            return $caster->castIn($value, $connectionType);
        }, $values);
    }

}