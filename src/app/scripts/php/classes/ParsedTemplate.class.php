<?php

class ParsedTemplate 
{
    private $eval;

    public function __construct($eval)
    {
        $this->eval = $eval;
    }

    public function evaluate() {
        return eval($this->eval);
    }

    public function __toString() {
        return $this->evaluate();
    }
}

?>