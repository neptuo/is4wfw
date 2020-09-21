<?php

abstract class ParsedTemplate 
{
    private $tagsToParse;

    private function findByPrefix(string $prefix) {
        if (array_key_exists($prefix, $this->tagsToParse)) {
            return $this->tagsToParse[$prefix];
        }

        return null;
    }

    protected function isTagProcessed(string $prefix, string $name) {
        if ($this->tagsToParse != null) {
            $value = $this->findByPrefix($prefix);
            if ($value == null) {
                $value = $this->findByPrefix("*");
            }

            if ($value != null) {
                if (is_string($value)) {
                    return $value == $name || $value == "*";
                } else if (is_array($value)) {
                    return in_array($name, $value) || in_array("*", $value);
                }
            }
        }

        return true;
    }

    protected abstract function evaluateInternal();

    public function evaluate($tagsToParse = null) {
        if (is_array($tagsToParse) && count($tagsToParse) > 0) {
            $this->tagsToParse = $tagsToParse;
        }

        return $this->evaluateInternal();
    }

    public function __toString() {
        return $this->evaluateInternal();
    }
    
    public function __invoke($tagsToParse = null) {
        return $this->evaluate($tagsToParse);
    }
}

?>