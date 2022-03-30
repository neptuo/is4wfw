<?php

abstract class ParsedTemplate 
{
    private static $tagsToParse;
    private static $propertyReferences;

    public function __construct() {
        if (self::$tagsToParse == null) {
            self::$tagsToParse = new TagsToParse();
        }
    }

    protected function autolib($prefix) {
        global $phpObject;
        return $phpObject->autolib($prefix);
    }

    protected function isTagProcessed(string $prefix, string $name) {
        return self::$tagsToParse->isProcessed($prefix, $name);
    }

    protected function pushTagsToParse($tagsToParse) {
        self::$tagsToParse->push($tagsToParse);
    }

    protected function popTagsToParse() {
        self::$tagsToParse->pop();
    }

    protected function getPropertyReference(string $prefix, string $name, callable $factory) {
        if (array_key_exists($prefix, self::$propertyReferences) && array_key_exists($name, self::$propertyReferences[$prefix])) {
            return self::$propertyReferences[$prefix][$name];
        }

        $ref = $factory();
        self::$propertyReferences[$prefix][$name] = $ref;
        return $ref;
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

    public function __construct() {
        $this->stack = new Stack();
    }

    private function findByPrefix(array $tagsToParse, string $prefix) {
        if (is_array($tagsToParse) && array_key_exists($prefix, $tagsToParse)) {
            return $tagsToParse[$prefix];
        }

        return null;
    }

    public function isProcessed(string $prefix, string $name) {
        $tagsToParse = $this->stack->peekNotNull();

        if ($tagsToParse === false) {
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
        $this->stack->push($tagsToParse);
    }

    public function pop() {
        $this->stack->pop();
    }
}

?>