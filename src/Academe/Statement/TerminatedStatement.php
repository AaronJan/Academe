<?php

namespace Academe\Statement;

use Academe\Contracts\Mapper\Executable;
use Academe\Contracts\Mapper\Instruction;
use Academe\Contracts\Mapper\Mapper;
use Academe\Support\InstanceBuilder;
use Academe\Contracts\InstructionStatement as InstructionStatementContract;

class TerminatedStatement implements Executable
{
    /**
     * @var string
     */
    protected $instructionClass;

    /**
     * @var array
     */
    protected $instructionConstructParameters;

    /**
     * @var \Academe\Contracts\InstructionStatement
     */
    protected $sourceInstructionStatement;

    /**
     * TerminatedStatement constructor.
     *
     * @param                                         $instructionClass
     * @param array                                   $instructionConstructParameters
     * @param \Academe\Contracts\InstructionStatement $sourceInstructionStatement
     */
    public function __construct($instructionClass,
                                array $instructionConstructParameters,
                                InstructionStatementContract $sourceInstructionStatement)
    {
        $this->instructionClass               = $instructionClass;
        $this->instructionConstructParameters = $instructionConstructParameters;
        $this->sourceInstructionStatement     = $sourceInstructionStatement;
    }

    /**
     * @return \Academe\Contracts\InstructionStatement
     */
    protected function getSourceInstructionStatement()
    {
        return $this->sourceInstructionStatement;
    }

    /**
     * @return array
     */
    protected function getInstructionConstructParameters()
    {
        return $this->instructionConstructParameters;
    }

    /**
     * @return string
     */
    protected function getInstructionClass()
    {
        return $this->instructionClass;
    }
    
    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $instruction = $this->makeInstruction();

        $this->getSourceInstructionStatement()->tweakInstruction($instruction);

        return $mapper->execute($instruction);
    }

    /**
     * @return Instruction
     */
    protected function makeInstruction()
    {
        $instructionClass = $this->getInstructionClass();

        $instruction = InstanceBuilder::make(
            $instructionClass,
            $this->getInstructionConstructParameters()
        );

        if (! $instruction instanceof Instruction) {
            throw new \LogicException("[$instructionClass] is not an instance of Instruction.");
        }

        return $instruction;
    }

}
