<?php

namespace Academe\Contracts;

use Academe\Contracts\Mapper\Instruction;

interface InstructionStatement
{
    /**
     * @param \Academe\Contracts\Mapper\Instruction $instruction
     * @return void
     */
    public function tweakInstruction(Instruction $instruction);
    
}