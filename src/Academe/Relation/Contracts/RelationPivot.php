<?php

namespace Academe\Relation\Contracts;

interface RelationPivot
{
    /**
     * @param $hostPrimary
     * @return array
     */
    public function getOtherKeys($hostPrimary);

    /**
     * @param       $hostPrimary
     * @param       $guestPrimary
     * @param array $additionAttributes
     */
    public function attachByKey($hostPrimary, $guestPrimary, $additionAttributes = []);

    /**
     * @param $hostPrimary
     * @param $guestPrimaries
     * @return int
     */
    public function detachByKeys($hostPrimary, $guestPrimaries);

    /**
     * @param $hostPrimary
     * @return int
     */
    public function detachAll($hostPrimary);

    /**
     * @param      $hostPrimary
     * @param      $guestPrimaries
     * @param bool $detaching
     * @return array
     */
    public function syncByKeys($hostPrimary, $guestPrimaries, $detaching = true);

}

