<?php

abstract class ParsedTemplate 
{
    protected function isTagProcessed(string $prefix, string $name) {
        return true;
    }

    public abstract function evaluate();

    public function __toString() {
        return $this->evaluate();
    }
    
    public function __invoke() {
        return $this->evaluate();
    }
}

?>