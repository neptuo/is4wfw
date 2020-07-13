<?php

require_once(APP_SCRIPTS_PHP_PATH . "classes/CustomTagParser.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/ParsedTemplate.class.php");

class FullTagParser extends CustomTagParser {

    /**
     *
     *  Regular expression for parsing full tag.     
     *
     */
    //private $FULL_TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)(\b[^>]*)>(((\s*)|(.*))*)</\1>)';
    //protected $FULL_TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)(( *([a-zA-Z0-9]+="[^"]*") *)*)>(((\s*)|(.*))*)</\1>)';
    // protected $FULL_TAG_RE = '(<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)(( *([a-zA-Z0-9:\-_]+="[^"]*") *)*)>(((\s*)|(.*)|(?R))*)</\1>)';
    // protected $FULL_TAG_RE = "/<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)([^>]*?)(([\s]*\/>)|(>((([^<]*?|<\!\-\-.*?\-\->)|(?R))*)<\/\\1[\s]*>))/sm";
    protected $FULL_TAG_RE = "#<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)((.*?)(?=\/>)\/>|([^>]*)>((?:[^<]|<(?!/?\\1[^>]*>)|(?R))+)</\\1>)#";

    
    /**
     *
     * 	Parses full tag
     * 
     *  Output of this function can't contain ' (apostrophe), as the output is evaluated as PHP code wrapped in ' (apostrophe).
     *
     */
    private function parsefulltag($ctag) {
        global $phpObject;

        // Self closing tag.
        if (count($ctag) == 4) {
            return parent::parsectag($ctag);
        }

        $object = explode(":", $ctag[1]);
        $content = $ctag[5];

        $skipped = self::isSkippedTag($ctag);

        $attributes = self::tryProcessAttributes($ctag[4]);
        if ($attributes === FALSE) {
            return '';
        }

        if ($skipped) {
            self::evalAttributesWithoutProcessingTag($attributes);

            // Parse $ctag[5].
            $parser = new FullTagParser();
            $parser->setContent($content);
            $parser->setTagsToParse($this->TagsToParse);
            $parser->startParsing();
            $parser->getResult();
            return '';
        }

        if ($phpObject->isRegistered($object[0])) {
            if ($phpObject->isFullTag($object[0], $object[1], $attributes)) {
                $attributes = $phpObject->sortFullAttributes($object[0], $object[1], $attributes, $content);
                if ($attributes === false) {
                    return "";
                }
                
                $functionName = $phpObject->getFuncToFullTag($object[0], $object[1]);
            } else if ($phpObject->isAnyFullTag($object[0], $object[1])) {
                $functionName = $phpObject->getFuncToFullTag($object[0], $object[1]);
                $attributes = $this->sortAnyTagAttributes($object[1], $attributes, $content);
            }

            if ($functionName) {
                return self::generateFunctionOutput($object[0], $functionName, $attributes);
            }
        }
        
        return '<h4 class="error">This tag "' . $object[1] . '" is not registered! [' . $object[0] . ']</h4>';
    }

    /**
     *
     * 	Parse custom tags from Content and save result to Result
     *
     */
    public function startParsing() {
        self::startMeasure();

        parent::setUseCaching(false);

        $processed = "";
        if ($this->Content != "") {
            $replaced = str_replace("'", "\\'", $this->Content);
            $processed = preg_replace_callback($this->FULL_TAG_RE, array(&$this, 'parsefulltag'), $replaced);
            self::checkPregError("parsefulltag");
        }

        $className = "Template_" . $this->generateRandomString();
        $classDefinition = "class $className extends ParsedTemplate { " . $this->Result . PHP_EOL . PHP_EOL . " public function evaluate() { return '". $processed . "'; } }";
        eval($classDefinition);
        
        $this->Result = new $className();
				
        self::stopMeasure();
    }

    public function getParsedTemplate() {
        return $this->Result;
    }

    public function getResult() {
        return $this->Result->evaluate();
    }

    public function __toString() {
        return $this->getResult();
    }
}

?>
