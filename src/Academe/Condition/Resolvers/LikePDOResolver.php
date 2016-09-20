<?php

namespace Academe\Condition\Resolvers;

use Academe\Condition\Like;
use Academe\Traits\SQLValueWrapper;

class LikePDOResolver
{
    use SQLValueWrapper;

    /**
     * @param          $connectionType
     * @param Like     $like
     * @return array
     */
    static public function resolve($connectionType, Like $like)
    {
        list($name, $value, $matchMode) = $like->getParameters();

        if ($matchMode === Like::MATCH_FROM_LEFT) {
            $value = "$value%";
        } elseif ($matchMode === Like::MATCH_FROM_RIGHT) {
            $value = "%$value";
        } else {
            $value = "%$value%";
        }

        return [(static::wrap($name) . " LIKE ?"), $value];
    }
}