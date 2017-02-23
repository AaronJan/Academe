<?php

namespace Academe\Actions;

use Academe\Contracts\Connection\Action;
use Academe\Actions\Traits\BeCondtionable;
use Academe\Actions\Traits\BeFormatted;
use Academe\Actions\Traits\BeLockable;
use Academe\Contracts\Action\Conditionable;
use Academe\Contracts\Action\Directable;

class Select implements Action, Conditionable, Directable
{
    use BeCondtionable, BeFormatted, BeLockable;

    /**
     * @var array
     */
    protected $fields;

    /**
     * Select constructor.
     *
     * @param array $fields
     */
    public function __construct($fields = ['*'])
    {
        $this->fields = $fields;
    }

    public function getName()
    {
        return 'select';
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->fields;
    }

}
