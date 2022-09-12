<?php

abstract class ParsedTemplate 
{
    private static $configs;
    private static $propertyReferences = [];

    public function __construct() {
        if (self::$configs == null) {
            self::$configs = new ParsedTemplateConfigStack();
        }
    }

    protected function autolib($prefix) {
        return $this->php()->autolib($prefix);
    }
    
    protected function php() {
        global $phpObject;
        return $phpObject;
    }

    protected function isTagProcessed(string $prefix, string $name) {
        return self::$configs->isTagProcessed($prefix, $name);
    }

    protected function isPropertyProcessed(string $prefix, string $name) {
        return self::$configs->isPropertyProcessed($prefix, $name);
    }

    protected function pushConfig(?ParsedTemplateConfig $config) {
        self::$configs->push($config);
    }

    protected function popConfig() {
        self::$configs->pop();
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

    public function evaluate(?ParsedTemplateConfig $config = null) {
        $this->pushConfig($config);
        $result = $this->evaluateInternal();
        $this->popConfig();
        return $result;
    }

    public function __toString() {
        return $this->evaluateInternal();
    }
    
    public function __invoke(?ParsedTemplateConfig $config = null) {
        return $this->evaluate($config);
    }
}

class ParsedTemplateConfig 
{
    public $tagsToEvalute = [];
    public $propertiesToEvaluate = [];

    public static function filtered($prefix, $tagsToEvalute = [], $propertiesToEvaluate = []): ParsedTemplateConfig {
        $config = new ParsedTemplateConfig();
        $config->tagsToEvalute[$prefix] = $tagsToEvalute;
        $config->propertiesToEvaluate[$prefix] = $propertiesToEvaluate;
        return $config;
    }

    private function findByPrefix(string $prefix, $type) {
        switch ($type) {
            case 't':
                $values = $this->tagsToEvalute;
                break;
            case 'p':
                $values = $this->propertiesToEvaluate;
                break;
        }

        if (is_array($values) && array_key_exists($prefix, $values)) {
            return $values[$prefix];
        }

        return null;
    }

    public function isTagProcessed(string $prefix, string $name) {
        return $this->isProcessted($prefix, $name, "t");
    }

    public function isPropertyProcessed(string $prefix, string $name) {
        return $this->isProcessted($prefix, $name, "p");
    }
    
    private function isProcessted(string $prefix, string $name, string $type) {
        $value = $this->findByPrefix($prefix, $type);
        if ($value == null) {
            $value = $this->findByPrefix("*", $type);
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
}

class ParsedTemplateConfigStack 
{
    private $stack;

    public function __construct() {
        $this->stack = new Stack();
    }

    public function isTagProcessed(string $prefix, string $name) {
        $config = $this->stack->peekNotNull();
        if ($config === false) {
            return true;
        }
    
        return $config->isTagProcessed($prefix, $name);
    }

    public function isPropertyProcessed(string $prefix, string $name) {
        $config = $this->stack->peekNotNull();
        if ($config === false) {
            return true;
        }

        return $config->isPropertyProcessed($prefix, $name);
    }
    
    public function push(?ParsedTemplateConfig $config) {
        $this->stack->push($config);
    }

    public function pop() {
        $this->stack->pop();
    }
}

?>