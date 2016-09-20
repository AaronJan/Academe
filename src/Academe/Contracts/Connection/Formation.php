<?php

namespace Academe\Contracts\Connection;

interface Formation
{
    /**
     * @param int      $limit
     * @param int|null $offset
     * @return $this
     */
    public function setLimit($limit, $offset = null);

    /**
     * @return array
     */
    public function getLimit();

    /**
     * @param $field
     * @param $direction
     * @return $this
     */
    public function setOrder($field, $direction);

    /**
     * @param array|mixed $orders
     * @return $this
     */
    public function setOrders($orders);

    /**
     * @return array|null
     */
    public function getOrders();

}