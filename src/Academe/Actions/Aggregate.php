<?php

namespace Academe\Actions;

use Academe\Contracts\Connection\Action;
use Academe\Exceptions\BadMethodCallException;
use Academe\Actions\Traits\BeCondtionable;
use Academe\Actions\Traits\BeLockable;
use Academe\Contracts\Conditionable;

class Aggregate implements Action, Conditionable
{
    use BeCondtionable, BeLockable;

    const METHOD_COUNT = 'count';
    const METHOD_SUM   = 'sum';
    const METHOD_AVG   = 'avg';
    const METHOD_MAX   = 'max';
    const METHOD_MIN   = 'min';

    /**
     * @var array
     */
    static protected $allowedMethods = [
        'count',
        'sum',
        'avg',
        'max',
        'min',
    ];

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $field;

    /**
     * Aggregate constructor.
     *
     * @param        $method
     * @param string $field
     */
    public function __construct($method, $field = '*')
    {
        $this->validateMethod($method);

        $this->method = $method;
        $this->field  = $field;
    }

    /**
     * @param $method
     */
    static protected function validateMethod($method)
    {
        if (! in_array($method, static::$allowedMethods)) {
            throw new BadMethodCallException("Method [{$method}] not allowed.");
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aggregate';
    }

    public function getParameters()
    {
        return [$this->method, $this->field];
    }
}
