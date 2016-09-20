<?php

namespace Academe\Statement;

use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Mapper\Mapper;

/**
 * TODO
 * Class FluentMapperStatement
 *
 * @package Academe\Statement
 */
class MapperStatement extends FluentStatement
{
    /**
     * @var \Academe\Contracts\Mapper\Mapper
     */
    protected $mapper;

    /**
     * FluentMapperStatement constructor.
     *
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     * @param \Academe\Contracts\Mapper\Mapper  $mapper
     */
    public function __construct(ConditionMaker $conditionMaker, Mapper $mapper)
    {
        parent::__construct($conditionMaker);

        $this->mapper = $mapper;
    }

    /**
     * @param array|null $attributes
     * @return mixed
     */
    public function all(array $attributes = null)
    {
        $terminatedStatement = parent::all($attributes);

        return $this->mapper->execute($terminatedStatement);
    }

    //todo 其它方法

}
