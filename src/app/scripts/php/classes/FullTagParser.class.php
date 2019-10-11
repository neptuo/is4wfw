<?php

require_once(APP_SCRIPTS_PHP_PATH . "classes/CustomTagParser.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");

class FullTagParser extends CustomTagParser {

    /**
     *
     *  Regular expression for parsing full tag.     
     *
     */
    //private $FULL_TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)(\b[^>]*)>(((\s*)|(.*))*)</\1>)';
    //protected $FULL_TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)(( *([a-zA-Z0-9]+="[^"]*") *)*)>(((\s*)|(.*))*)</\1>)';
    protected $FULL_TAG_RE = '(<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)(( *([a-zA-Z0-9:\-_]+="[^"]*") *)*)>(((\s*)|(.*))*)</\1>)';

    
    /**
     *
     * 	Parses full tag
     * 
     *  Output of this function can't contain ' (apostrophe), as the output is evaluated as PHP code wrapped in ' (apostrophe).
     *
     */
    private function parsefulltag($ctag) {
        global $phpObject;

        $object = explode(":", $ctag[1]);

        $skipped = self::isSkippedTag($ctag);

        $attributes = self::tryProcessAttributes($ctag[2]);
        if ($attributes === FALSE) {
            return '';
        }

        if ($skipped) {
            self::evalAttributesWithoutProcessingTag($attributes);

            // Parse $ctag[5].
            $parser = new FullTagParser();
            $parser->setContent($ctag[5]);
            $parser->setTagsToParse($this->TagsToParse);
            $parser->startParsing();
            $parser->getResult();
            return '';
        }

        if ($phpObject->isRegistered($object[0]) && $phpObject->isFullTag($object[0], $object[1], $attributes)) {
            $attributes = $phpObject->sortFullAttributes($object[0], $object[1], $attributes, $ctag[5]);
            if ($attributes === false) {
                return "";
            }
            
            $functionName = $phpObject->getFuncToFullTag($object[0], $object[1]);
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
        parent::setUseCaching(false);

        $this->Result = $this->Content;
        $this->Result = str_replace("'", "\\'", $this->Result);
        
        $this->Result = preg_replace_callback($this->FULL_TAG_RE, array(&$this, 'parsefulltag'), $this->Result);
        self::checkPregError();

        $this->Result = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $this->Result);
        self::checkPregError();

        $this->Result = eval("return '". $this->Result . "';");
    }
}

?>
