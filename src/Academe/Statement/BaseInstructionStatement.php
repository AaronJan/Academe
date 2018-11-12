<?php

namespace Academe\Statement;

use Academe\Instructions\Traits\Lockable;
use Academe\Instructions\Traits\Sortable;
use Academe\Instructions\Traits\WithRelation;
use Academe\Instructions;
use Academe\Exceptions\BadMethodCallException;
use Academe\Traits\ParseRelation;
use Academe\Contracts\Mapper\Instruction;
use Academe\Contracts\Statement;
use Academe\Contracts\InstructionStatement as InstructionStatementContract;

class BaseInstructionStatement extends BaseStatement implements InstructionStatementContract
{
    use ParseRelation;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    public function tweakInstruction(Instruction $instruction)
    {
        /**
         * @var $instruction Lockable|Sortable|WithRelation
         */

        if (method_exists($this, 'tweakLock')) {
            $this->tweakLock($instruction);
        }

        if (method_exists($this, 'tweakOrder')) {
            $this->tweakOrder($instruction);
        }

        $this->tweakRelation($instruction);
    }

    /**
     * @param $relations
     * @return $this
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $parsedRelations = $this->parseRelations($relations);

        $this->relations = array_merge($this->relations, $parsedRelations);

        return $this;
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    protected function tweakRelation(Instruction $instruction)
    {
        /**
         * @var $instruction WithRelation
         */
        if (! empty($this->relations)) {
            $instruction->with($this->relations);
        }
    }

    /**
     * @param       $instructionClass
     * @param array $instructionConstructParameters
     * @return \Academe\Statement\TerminatedStatement
     */
    protected function makeTerminatedStatement($instructionClass,
                                               array $instructionConstructParameters)
    {
        return new TerminatedStatement(
            $instructionClass,
            $instructionConstructParameters,
            $this
        );
    }

    /**
     * @param $fields
     * @return array
     */
    protected function filterFields($fields)
    {
        return $fields ?: ($this->getFields() ?: ['*']);
    }

    /**
     * @return bool
     */
    protected function hadConditionAlready()
    {
        return ! empty($this->conditions);
    }

    /**
     * @param array|null $attributes
     * @return \Academe\Statement\TerminatedStatement
     */
    public function all(array $attributes = null)
    {
        $attributes = $this->filterFields($attributes);

        return $this->makeTerminatedStatement(
            Instructions\All::class,
            [$attributes, $this->compileConditionGroup()]
        );
    }

    /**
     * @param string $attribute
     * @return \Academe\Statement\TerminatedStatement
     */
    public function count($attribute = '*')
    {
        return $this->makeTerminatedStatement(
            Instructions\Count::class,
            [$attribute, $this->compileConditionGroup()]
        );
    }

    /**
     * @param array $attributes
     * @return \Academe\Statement\TerminatedStatement
     */
    public function create(array $attributes)
    {
        if ($this->hadConditionAlready()) {
            throw new BadMethodCallException("Create doesn't work with condition.");
        }

        return $this->makeTerminatedStatement(
            Instructions\Create::class,
            [$attributes]
        );
    }

    /**
     * @return \Academe\Statement\TerminatedStatement
     */
    public function delete()
    {
        return $this->makeTerminatedStatement(
            Instructions\Delete::class,
            [$this->compileConditionGroup()]
        );
    }

    /**
     * @return \Academe\Statement\TerminatedStatement
     */
    public function exists()
    {
        return $this->makeTerminatedStatement(
            Instructions\Exists::class,
            [$this->compileConditionGroup()]
        );
    }

    /**
     * @param array|null $fields
     * @return \Academe\Statement\TerminatedStatement
     */
    public function first(array $fields = null)
    {
        $fields = $this->filterFields($fields);

        return $this->makeTerminatedStatement(
            Instructions\First::class,
            [$fields, $this->compileConditionGroup()]
        );
    }

    /**
     * @param            $page
     * @param int        $perPage
     * @param array|null $attributes
     * @return \Academe\Statement\TerminatedStatement
     */
    public function paginate($page, $perPage = 15, array $attributes = null)
    {
        $attributes = $this->filterFields($attributes);

        return $this->makeTerminatedStatement(
            Instructions\Paginate::class,
            [$page, $perPage, $attributes, $this->compileConditionGroup()]
        );
    }

    /**
     * @param            $limit
     * @param array|null $attributes
     * @param null       $offset
     * @return \Academe\Statement\TerminatedStatement
     */
    public function segment($limit, array $attributes = null, $offset = null)
    {
        $attributes = $this->filterFields($attributes);

        return $this->makeTerminatedStatement(
            Instructions\Segment::class,
            [$limit, $attributes, $offset, $this->compileConditionGroup()]
        );
    }

    /**
     * @param array $attributes
     * @return \Academe\Statement\TerminatedStatement
     */
    public function update(array $attributes)
    {
        return $this->makeTerminatedStatement(
            Instructions\Update::class,
            [$this->compileConditionGroup(), $attributes]
        );
    }

    /**
     * @param $field
     * @return \Academe\Statement\TerminatedStatement
     */
    public function sum($field)
    {
        return $this->makeTerminatedStatement(
            Instructions\Sum::class,
            [$field, $this->compileConditionGroup()]
        );
    }
}
