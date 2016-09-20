<?php

namespace Academe\Statement;

use Academe\Contracts\Mapper\Executable;
use Academe\Contracts\Mapper\Instructions\All;
use Academe\Contracts\Mapper\Instructions\Count;
use Academe\Contracts\Mapper\Instructions\Create;
use Academe\Contracts\Mapper\Instructions\Delete;
use Academe\Contracts\Mapper\Instructions\Exists;
use Academe\Contracts\Mapper\Instructions\First;
use Academe\Contracts\Mapper\Instructions\Paginate;
use Academe\Contracts\Mapper\Instructions\Segment;
use Academe\Contracts\Mapper\Instructions\Update;
use Academe\Contracts\Mapper\Mapper;
use Academe\Exceptions\BadMethodCallException;

class TerminatedStatement implements Executable
{
    /**
     * @var array
     */
    static protected $instructionContractToMapMethodMap = [
        All::class      => 'makeAllInstruction',
        Count::class    => 'makeCountInstruction',
        Create::class   => 'makeCreateInstruction',
        Delete::class   => 'makeDeleteInstruction',
        Exists::class   => 'makeExistsInstruction',
        First::class    => 'makeFirstInstruction',
        Paginate::class => 'makePaginateInstruction',
        Segment::class  => 'makeSegmentInstruction',
        Update::class   => 'makeUpdateInstruction',
    ];

    /**
     * @var string
     */
    protected $instructionContract;

    /**
     * @var array
     */
    protected $instructionConstructParameters;

    /**
     * @var \Academe\Statement\InstructionStatement
     */
    protected $instructionStatement;

    /**
     * TerminatedStatement constructor.
     *
     * @param string                                  $instructionContract
     * @param                                         $instructionConstructParameters
     * @param \Academe\Statement\InstructionStatement $instructionStatement
     */
    public function __construct($instructionContract,
                                array $instructionConstructParameters,
                                InstructionStatement $instructionStatement)
    {
        $this->instructionContract            = $instructionContract;
        $this->instructionConstructParameters = $instructionConstructParameters;
        $this->instructionStatement           = $instructionStatement;
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $instruction = $this->makeInstruction($mapper);

        $this->instructionStatement->tweakInstruction($instruction);

        return $mapper->execute($instruction);
    }

    /**
     * @param \Academe\Contracts\Mapper\Mapper $mapper
     * @return mixed
     */
    protected function makeInstruction(Mapper $mapper)
    {
        $instructionContract = $this->instructionContract;

        if (! isset(static::$instructionContractToMapMethodMap[$instructionContract])) {
            $message = "Undefined Instruction contract [{$instructionContract}]";

            throw new BadMethodCallException($message);
        }

        $method = static::$instructionContractToMapMethodMap[$instructionContract];

        return call_user_func_array([$mapper, $method], $this->instructionConstructParameters);
    }

}
