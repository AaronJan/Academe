<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\SizeIs;
use Academe\Contracts\CastManager;

class SizeIsMongoDBResolver
{
    /**
     * @param                                     $connectionType
     * @param SizeIs                              $sizeIs
     * @param \Academe\Contracts\CastManager|null $castManager
     * @return array
     */
    static public function resolve($connectionType,
                                   SizeIs $sizeIs,
                                   CastManager $castManager = null)
    {
        list($field, $size) = $sizeIs->getParameters();

        return [$field => ['$size' => $size]];
    }
}