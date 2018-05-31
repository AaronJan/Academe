<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\NotIn;
use Academe\Contracts\CastManager;
use Academe\Casting\Casters\GroupCaster;
use Academe\Contracts\Caster;

class NotInMongoDBResolver
{
    /**
     * @param $connectionType
     * @param NotIn $notIn
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   NotIn $notIn,
                                   CastManager $castManager = null)
    {
        list($name, $values) = $notIn->getParameters();

        /* @var $fieldCaster Caster|GroupCaster */
        $fieldCaster = $castManager === null ? null : $castManager->getCaster($name);
        if ($fieldCaster === null) {
            return [$name => ['$nin' => $values]];
        }

        return [
            $name => [
                '$nin' => static::castValues(
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