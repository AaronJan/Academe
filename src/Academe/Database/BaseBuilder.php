<?php

namespace Academe\Database;

use Academe\Contracts\CastManager;

abstract class BaseBuilder
{
    /**
     * @param \Academe\Contracts\CastManager $castManager
     * @param array $attributes
     * @param                                $connectionType
     * @return array
     */
    protected function castAttributes(CastManager $castManager,
                                      array $attributes,
                                      $connectionType
    ) {
        $castedAttributes = [];

        foreach ($attributes as $field => $value) {
            $castedAttributes[$field] = $castManager->castIn($field, $value, $connectionType);
        }

        return $castedAttributes;
    }

}