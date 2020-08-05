<?php

abstract class ParsedTemplate 
{
    public abstract function evaluate();

    public function __toString() {
        return $this->evaluate();
    }
    
    public function __invoke() {
        return $this->evaluate();
    }
}

?>