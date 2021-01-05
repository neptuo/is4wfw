<?php

abstract class ParsedTemplate 
{
    private static $tagsToParse;

    private function findByPrefix(string $prefix) {
        $tagsToParse = null;
        if (ParsedTemplate::$tagsToParse != null) {
            $tagsToParse = ParsedTemplate::$tagsToParse->peek();
        }

        if (is_array($tagsToParse) && array_key_exists($prefix, $tagsToParse)) {
            return $tagsToParse[$prefix];
        }

        return null;
    }

    protected function isTagProcessed(string $prefix, string $name) {
        // Still not working.
        // We need to push nulls to stack, so that we can pop safely.
        // But when searching to current tagsToParse array, we need to dig until non-null value is peeked.
        // I think...

        $tagsToParse = null;
        if (ParsedTemplate::$tagsToParse != null) {
            $tagsToParse = ParsedTemplate::$tagsToParse->peek();
        }

        if ($tagsToParse != null) {
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

    protected function pushTagsToParse($tagsToParse) {
        if (ParsedTemplate::$tagsToParse == null) {
            ParsedTemplate::$tagsToParse = new Stack();
        }

        ParsedTemplate::$tagsToParse->push($tagsToParse);
    }

    protected function popTagsToParse() {
        ParsedTemplate::$tagsToParse->pop();
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

?>