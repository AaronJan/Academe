<?php

namespace Academe;

use Academe\Contracts\Connection\CommandUnit as CommandUnitContract;

class CommandUnit implements CommandUnitContract
{
    /**
     * @var mixed
     */
    protected $connectionType;

    /**
     * @var mixed
     */
    protected $raw;

    /**
     * CommandUnit constructor.
     *
     * @param $connectionType
     * @param $raw
     */
    public function __construct($connectionType, $raw)
    {
        $this->connectionType = $connectionType;
        $this->raw            = $raw;
    }

    /**
     * @return mixed
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }
}