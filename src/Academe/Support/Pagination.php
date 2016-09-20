<?php

namespace Academe\Support;

class Pagination
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var int
     */
    protected $total;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var float
     */
    protected $lastPage;

    /**
     * Pagination constructor.
     *
     * @param array $items
     * @param int   $total
     * @param int   $perPage
     * @param int   $currentPage
     */
    public function __construct(array $items, $total, $perPage, $currentPage)
    {
        $this->items       = $items;
        $this->total       = $total;
        $this->perPage     = $perPage;
        $this->currentPage = $currentPage;
        $this->lastPage    = ceil($total / $perPage);
    }

    /**
     * @return array
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function perPage()
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function currentPage()
    {
        return $this->currentPage;
    }

}
