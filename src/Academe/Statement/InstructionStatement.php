<?php

namespace Academe\Statement;

use Academe\Contracts\Mapper\Instruction;
use Academe\Instructions\All;
use Academe\Instructions\Count;
use Academe\Instructions\Create;
use Academe\Instructions\Delete;
use Academe\Instructions\Exists;
use Academe\Instructions\First;
use Academe\Instructions\Paginate;
use Academe\Instructions\Segment;
use Academe\Instructions\Update;
use Academe\Contracts\Statement;
use Academe\Exceptions\BadMethodCallException;
use Academe\Instructions\Traits\WithRelation;
use Academe\Instructions\Traits\Transactional;
use Academe\Instructions\Traits\Lockable;
use Academe\Traits\ParseRelation;
use Academe\Instructions\Traits\Sortable;

class InstructionStatement extends RelationStatement
{
    use ParseRelation;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var null|int
     */
    protected $lockLevel;

    /**
     * @var array
     */
    protected $transactions = [];

    /**
     * @var array
     */
    protected $orders = [];

    /**
     * @param  Statement $statement
     * @return $this
     */
    public function loadFrom(Statement $statement)
    {
        $this->conditions = $statement->getConditions();

        if ($statement instanceof RelationStatement) {
            $this->fields = $statement->getFields();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function lockForShare()
    {
        $this->lockLevel = 1;

        return $this;
    }

    /**
     * @return $this
     */
    public function lock()
    {
        $this->lockLevel = 2;

        return $this;
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
     * @param \Academe\Contracts\Transaction|\Academe\Contracts\Transaction[] $transactions
     * @return $this
     */
    public function involve($transactions)
    {
        if (! is_array($transactions)) {
            $transactions = [$transactions];
        }

        $this->transactions = array_merge($this->transactions, $transactions);

        return $this;
    }

    /**
     * @param $field
     * @param $direction
     * @return $this
     */
    public function sort($field, $direction)
    {
        $this->orders[] = [$field, $direction];

        return $this;
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    public function tweakInstruction(Instruction $instruction)
    {
        /**
         * @var $instruction Lockable|Transactional|Sortable|WithRelation
         */
        $this->tweakLock($instruction);
        $this->tweakOrder($instruction);
        $this->tweakRelation($instruction);
        $this->tweakTransaction($instruction);
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    protected function tweakLock(Instruction $instruction)
    {
        /**
         * @var $instruction Lockable
         */
        if ($this->lockLevel) {
            $instruction->setLock($this->lockLevel);
        }
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    protected function tweakTransaction(Instruction $instruction)
    {
        /**
         * @var $instruction Transactional
         */
        if (! empty($this->transactions)) {
            $instruction->setTransactions($this->transactions);
        }
    }

    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     */
    protected function tweakOrder(Instruction $instruction)
    {
        /**
         * @var $instruction Sortable
         */
        if (! empty($this->orders)) {
            foreach ($this->orders as $order) {
                list($field, $direction) = $order;

                $instruction->sort($field, $direction);
            }
        }
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
            All::class,
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
            Count::class,
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
            Create::class,
            [$attributes]
        );
    }

    /**
     * @return \Academe\Statement\TerminatedStatement
     */
    public function delete()
    {
        return $this->makeTerminatedStatement(
            Delete::class,
            [$this->compileConditionGroup()]
        );
    }

    /**
     * @return \Academe\Statement\TerminatedStatement
     */
    public function exists()
    {
        return $this->makeTerminatedStatement(
            Exists::class,
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
            First::class,
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
            Paginate::class,
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
            Segment::class,
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
            Update::class,
            [$this->compileConditionGroup(), $attributes]
        );
    }

}

