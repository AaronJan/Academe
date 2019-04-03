<?php

namespace Academe\Statement;

use Academe\Contracts\ConditionMaker;
use Academe\Contracts\Mapper\Mapper;
use Academe\Contracts\Receipt;
use Academe\Support\Pagination;
use Academe\Aggregation;

/**
 * Class BaseMapperStatement
 *
 * @package Academe\Statement
 */
abstract class BaseMapperStatement extends BaseInstructionStatement
{
    /**
     * @var \Academe\Contracts\Mapper\Mapper
     */
    protected $mapper;

    /**
     * MapperStatement constructor.
     *
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @param \Academe\Contracts\ConditionMaker $conditionMaker
     */
    public function __construct(Mapper $mapper,
                                ConditionMaker $conditionMaker
    ) {
        parent::__construct($conditionMaker);

        $this->setMapper($mapper);
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     */
    protected function setMapper(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return \Academe\Contracts\Mapper\Mapper
     */
    protected function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @param array|null $attributes
     * @return mixed[]|mixed
     */
    public function all(array $attributes = null)
    {
        $executable = parent::all($attributes);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param string $attribute
     * @return int
     */
    public function count($attribute = '*')
    {
        $executable = parent::count($attribute);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param array $attributes
     * @return Receipt
     */
    public function create(array $attributes)
    {
        $executable = parent::create($attributes);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @return int
     */
    public function delete()
    {
        $executable = parent::delete();

        return $this->getMapper()->execute($executable);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        $executable = parent::exists();

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param array|null $fields
     * @return array|mixed|null
     */
    public function first(array $fields = null)
    {
        $executable = parent::first($fields);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param            $page
     * @param int $perPage
     * @param array|null $attributes
     * @return Pagination
     */
    public function paginate($page, $perPage = 15, array $attributes = null)
    {
        $executable = parent::paginate($page, $perPage, $attributes);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param            $limit
     * @param array|null $attributes
     * @param null $offset
     * @return array
     */
    public function segment($limit, array $attributes = null, $offset = null)
    {
        $executable = parent::segment($limit, $attributes, $offset);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param array $attributes
     * @return int
     */
    public function update(array $attributes)
    {
        $executable = parent::update($attributes);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param $field
     * @return Aggregation
     */
    public function sum($field)
    {
        $executable = parent::sum($field);

        return $this->getMapper()->execute($executable);
    }

    /**
     * @param array $aggregation
     * @param array $values
     * @return mixed
     */
    public function group($aggregation, $values)
    {
        $executable = parent::group($aggregation, $values);

        return $this->getMapper()->execute($executable);
    }
}
