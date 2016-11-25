<?php

namespace Academe\Statement;

use Academe\Contracts\Mapper\Executable;
use Academe\Contracts\Mapper\Instruction;
use Academe\Contracts\Mapper\Mapper;
use Academe\Support\ClassInstanceBuilder;

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
     * @var \Academe\Statement\InstructionStatement
     */
    protected $sourceInstructionStatement;

    /**
     * TerminatedStatement constructor.
     *
     * @param string                                  $instructionClass
     * @param                                         $instructionConstructParameters
     * @param \Academe\Statement\InstructionStatement $sourceInstructionStatement
     */
    public function __construct($instructionClass,
                                array $instructionConstructParameters,
                                InstructionStatement $sourceInstructionStatement)
    {
        $this->instructionClass               = $instructionClass;
        $this->instructionConstructParameters = $instructionConstructParameters;
        $this->sourceInstructionStatement     = $sourceInstructionStatement;
    }

    /**
     * @param Mapper $mapper
     * @return mixed
     */
    public function execute(Mapper $mapper)
    {
        $instruction = $this->makeInstruction();

        $this->sourceInstructionStatement->tweakInstruction($instruction);

        return $mapper->execute($instruction);
    }

    /**
     * @return array
     */
    protected function getInstructionConstructParameters()
    {
        return $this->instructionConstructParameters;
    }

    /**
     * @return Instruction
     */
    protected function makeInstruction()
    {
        $instructionClass = $this->instructionClass;

        $instruction = ClassInstanceBuilder::makeInstance(
            $instructionClass,
            $this->getInstructionConstructParameters()
        );

        if (! $instruction instanceof Instruction) {
            throw new \LogicException("[$instructionClass] is not an instance of Instruction.");
        }

        return $instruction;
    }

}
