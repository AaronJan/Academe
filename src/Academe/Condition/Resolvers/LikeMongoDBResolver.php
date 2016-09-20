<?php

namespace Academe\Condition\Resolvers;

use Academe\CommandUnit;
use Academe\Condition\Like;

class LikeMongoDBResolver
{
    /**
     * @param       $connectionType
     * @param Like  $like
     * @return CommandUnit
     */
    static public function resolve($connectionType, Like $like)
    {
        list($name, $value, $matchMode) = $like->getParameters();

        $regexp = preg_quote($value);

        if ($matchMode === Like::MATCH_FROM_LEFT) {
            $regexp = "^$regexp";
        } elseif ($matchMode === Like::MATCH_FROM_RIGHT) {
            $regexp = "$regexp$";
        } else {
            $regexp = "^$regexp$";
        }

        return new CommandUnit(
            $connectionType,
            [$name => ['$regex' => "/{$regexp}/", '$options' => '']]
        );
    }
}