<?php

namespace Academe;

use Academe\Contracts\Connection\Formation as FormationContract;
use Academe\Exceptions\LogicException;

/**
 * Class Option
 *
 * @package Academe
 */
class Formation implements FormationContract
{
    static protected $allowedDirections = ['asc', 'desc'];

    /**
     * @var array|null
     */
    protected $limit = null;

    /**
     * @var array
     */
    protected $orders = null;

    /**
     * @param int      $limit
     * @param int|null $offset
     * @return $this
     */
    public function setLimit($limit, $offset = null)
    {
        $this->limit = [$limit, $offset];

        return $this;
    }

    /**
     * @return array
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param $field
     * @param $direction
     * @return $this
     */
    public function setOrder($field, $direction)
    {
        $order = [$field, $direction];

        $this->validateOrder($order);

        $this->orders = [$order];

        return $this;
    }

    /**
     * @param array|mixed $orders
     * @return $this
     */
    public function setOrders($orders)
    {
        foreach ($orders as $order) {
            $this->validateOrder($order);
        }

        $this->orders = $orders;

        return $this;
    }

    /**
     * @param $order
     * @throws LogicException
     */
    protected function validateOrder($order)
    {
        list($field, $direction) = $order;

        if (! in_array($direction, static::$allowedDirections)) {
            $message = "Direction can only be one of [" .
                implode(',', static::$allowedDirections) . ']';

            throw new LogicException($message);
        }
    }

    /**
     * @return array|null
     */
    public function getOrders()
    {
        return $this->orders;
    }

}