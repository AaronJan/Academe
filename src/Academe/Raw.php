<?php

namespace Academe;

use Academe\Contracts\Raw as RawContract;

class Raw implements RawContract
{
    /**
     * @var mixed
     */
    protected $raw;

    /**
     * Raw constructor.
     *
     * @param mixed $raw
     */
    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }
}