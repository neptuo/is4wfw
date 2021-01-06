<?php

abstract class ParsedTemplate 
{
    private static $tagsToParse;

    public function __construct() {
        if (static::$tagsToParse == null) {
            static::$tagsToParse = new TagsToParse();
        }
    }

    protected function isTagProcessed(string $prefix, string $name) {
        return static::$tagsToParse->isProcessed($prefix, $name);
    }

    protected function pushTagsToParse($tagsToParse) {
        static::$tagsToParse->push($tagsToParse);
    }

    protected function popTagsToParse() {
        static::$tagsToParse->pop();
    }

    protected abstract function evaluateInternal();

    public function evaluate($tagsToParse = null) {
        $this->pushTagsToParse($tagsToParse);
        $result = $this->evaluateInternal();
        $this->popTagsToParse();
        return $result;
    }

    public function __toString() {
        return $this->evaluateInternal();
    }
    
    public function __invoke($tagsToParse = null) {
        return $this->evaluate($tagsToParse);
    }
}

class TagsToParse 
{
    private $stack;
    private $nullCounter = 0;

    private function findByPrefix(array $tagsToParse, string $prefix) {
        if (is_array($tagsToParse) && array_key_exists($prefix, $tagsToParse)) {
            return $tagsToParse[$prefix];
        }

        return null;
    }

    public function isProcessed(string $prefix, string $name) {
        $tagsToParse = null;
        if ($this->stack != null) {
            $tagsToParse = $this->stack->peek();
        }

        if ($tagsToParse == null) {
            return true;
        }

        $value = $this->findByPrefix($tagsToParse, $prefix);
        if ($value == null) {
            $value = $this->findByPrefix($tagsToParse, "*");
        }

        if ($value != null) {
            if (is_string($value)) {
                return $value == $name || $value == "*";
            } else if (is_array($value)) {
                return in_array($name, $value) || in_array("*", $value);
            }
        }

        return false;
    }

    public function push($tagsToParse) {
        if ($tagsToParse == null) {
            $this->nullCounter++;
            return;
        }

        if ($this->stack == null) {
            $this->stack = new Stack();
        }

        $this->stack->push($tagsToParse);
    }

    public function pop() {
        if ($this->nullCounter > 0) {
            $this->nullCounter--;
            return;
        }

        $this->stack->pop();
    }
}

?>