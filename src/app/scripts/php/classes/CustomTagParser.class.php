<?php

class CustomTagParser {

    protected $Result = "";

    /**
     *
     * 	Custom tag attributes.
     *
     */
    protected $Attributes = array();
    /**
     *
     *  Regular expression for parsing custom tag.     
     *
     */
    protected $TAG_RE = '(<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)( )+((([a-zA-Z0-9-_]+[:]?[a-zA-Z0-9-_]*)="[^"]*"( )*)*)\/>)';
    /**
     *
     *  Regular expression for parsing attribute.
     *
     */
    protected $ATT_RE = '(([a-zA-Z0-9-_]+[:]?[a-zA-Z0-9-_]*)="([^"]*)")';

    // Regular expression for parsing property value. It requires exact match (no prefix or postfix text).
    protected $ATT_PROPERTY_RE = '(^([a-zA-Z0-9-_]+:[a-zA-Z0-9-_.]+)$)';
    protected $PropertyAttr = '';
    protected $PropertyUse = '';
    /**
     *
     * 	Array of object names that must be set as global
     *
     */
    protected $GlobalObjects = array();
    protected $UseCaching = true;
	
    protected $TagsToParse = array();
    
    static $Measure = false;
    static $Measures = array();
    
	public static function saveMeasures($value) {
		CustomTagParser::$Measure = $value;
    }
    
    public static function getMeasures() {
        return CustomTagParser::$Measures;
    }

    private $startTime;

    protected function startMeasure() {
        $this->startTime = 0;
        if (CustomTagParser::$Measure) {
            $this->startTime = microtime();
        }
    }

    protected function stopMeasure($content) {
        if (CustomTagParser::$Measure) {
            $endTime = microtime();
            $elapsed = $endTime - $this->startTime;
            array_push(CustomTagParser::$Measures, array($elapsed, $content));
        }
    }
    
    /**
     *
     *  Parse all attributes to array.
     *
     *  @param  att string with attributes
     *  @return array of attributes
     *
     */
    protected function parseatt($att) {
        $this->Attributes[] = $att[0];
    }

    /**
     *
     * 	Use caching
     *
     */
    public function setUseCaching($val) {
        if ($val == false) {
            $this->UseCaching = false;
        } else {
            $this->UseCaching = true;
        }
    }
	
	public function setTagsToParse($tags) {
		$this->TagsToParse = $tags;
    }

    protected function addSingletonGlobalObject($obj) {
        if (!in_array($obj, $this->GlobalObjects)) {
            $this->GlobalObjects[] = $obj;
        }
    }

    protected function isSkippedTag($ctag) {
		if ($this->TagsToParse != array()) {
			$skip = true;
			foreach ($this->TagsToParse as $tag) {
				if($ctag[1] == $tag) {
					$skip = false;
					break;
				}
			}
			
			if ($skip) {
				return $ctag[0];
			}
        }
        
        return false;
    }

    protected function evalAttributesWithoutProcessingTag($attributes) {
        foreach ($attributes as $key => $value) {
            if ($value["type"] == "eval") {
                $eval = $value["value"] . ";";
                eval($eval);
            }
        }
    }

    protected function tryProcessAttributes($rawAttributes) {
        $this->Attributes = array();
        $attributes = array();

        preg_replace_callback($this->ATT_RE, array(&$this, 'parseatt'), $rawAttributes);

        foreach ($this->Attributes as $tmp) {
            $att = explode("=", $tmp);
            if (strlen($att[0]) > 0) {
                $this->PropertyAttr = '';
                $this->PropertyUse = 'get';
                
                $valueType = 'raw';
                if (strlen($att[1]) > 1) {
                    $att[1] = substr($att[1], 1, strlen($att[1]) - 2);
                    $evaluated = preg_replace_callback($this->ATT_PROPERTY_RE, array(&$this, 'parsecproperty'), $att[1]);

                    if ($att[1] != $evaluated) {
                        $att[1] = $evaluated;
                        $valueType = 'eval';
                    }
                } else {
                    $att[1] = '';
                }

                // #61 - Any input double quotes will be escaped
                // $att[1] = str_replace("\"", "\\\"", $att[1]);
                $attributes[$att[0]] = array('value' => $att[1], 'type' => $valueType);
            }
        }

        $globalResult = self::tryProcessGlobalAttributes($attributes);
        if ($globalResult === TRUE) {
            return FALSE;
        } else {
            $attributes = $globalResult;
        }

        return $attributes;
    }

    private function concatAttributesToString($attributes, $isItemNameIncluded = false) {
        $result = "";
        $i = 0;
        foreach ($attributes as $name => $value) {
            if ($isItemNameIncluded) {
                $result .= "'" . $name . "' => ";
            }

            if ($value['type'] == 'raw') {
                $result .= "'" . $value['value'] . "'";
            } else if($value['type'] == 'eval') {
                $result .= self::tryEvaluateAttribute($value['value']);
            } else {
                echo '<pre>';
                print_r(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
                echo '</pre>';
                die('Missing value type for attribute "' . $name . '".');
            }

            if ($i < (count($attributes) - 1)) {
                $result .= ", ";
            }
            $i++;
        }

        return $result;
    }

    protected function tryEvaluateAttribute($value) {
        if (is_array($value)) {
            $result = "array(" . self::concatAttributesToString($value, true) . ")";
            return $result;
        } else if($value === false) {
            return 'false';
        } else if($value === true) {
            return 'true';
        }

        return $value;
    }

    protected function generateFunctionOutput($tagPrefix, $functionName, $attributes, $isWrappedAsString = true) {
        $identifier = self::generateRandomString();

        if (is_array($attributes)) {
            $attributes = self::concatAttributesToString($attributes);
        }

        $identifier = 'template_' . $tagPrefix . '_' . $functionName . '_' . $identifier;

        $targetObject = '$' . $tagPrefix . 'Object';
        $logObject = '$' . 'log' . 'Object';
        $bodyExecute = 'global ' . $targetObject . '; return ' . $targetObject . '->' . $functionName . '(' . $attributes . ');';
        $functionDefinition = 'function ' . $identifier . '() { try { ' . $bodyExecute . ' } catch (Exception $e) { global ' . $logObject . '; ' . $logObject . '->exception($e); return ""; } }';
        
        $this->Result .= PHP_EOL . PHP_EOL . $functionDefinition;

        $result = '$this->' . $identifier . "()";
        if ($isWrappedAsString) {
            $result = "' . " . $result . " . '";
        }

        return $result;
    }

    /**
     *
     *  Function parses custom tag, call right function & return content.
     * 
     *  Output of this function can't contain ' (apostrophe), as the output is evaluated as PHP code wrapped in ' (apostrophe).
     *
     *  @param  ctag  custom tag as string
     *  @return return of custom tag function
     *
     */
    protected function parsectag($ctag) {
        global $phpObject;

        $object = explode(":", $ctag[1]);

        $skipped = self::isSkippedTag($ctag);

        $attributes = self::tryProcessAttributes($ctag[3]);
        if ($attributes === FALSE) {
            return '';
        }

        if ($skipped) {
            self::evalAttributesWithoutProcessingTag($attributes);
            return '';
        }

        if ($phpObject->isRegistered($object[0])) {
            $functionName = false;

            if ($phpObject->isTag($object[0], $object[1], $attributes)) {
                $attributes = $phpObject->sortAttributes($object[0], $object[1], $attributes);
                $functionName = $phpObject->getFuncToTag($object[0], $object[1]);
            } else if ($phpObject->isAnyTag($object[0], $object[1])) {
                $functionName = $phpObject->getFuncToTag($object[0], $object[1]);
                $attributes = $this->sortAnyTagAttributes($object[1], $attributes);
            }

            if ($functionName && ($attributes !== false)) {
                if ($this->UseCaching) {
                    self::addSingletonGlobalObject('$' . $object[0] . 'Object');
                    return '<?php echo $' . $object[0] . 'Object->' . $functionName . '(' . self::concatAttributesToString($attributes) . ') ?>';
                } else {
                    if ($object[0] == 'php') {
                        eval('$return =  ${$object[0]."Object"}->{$functionName}(' . self::concatAttributesToString($attributes) . ');');
                    }
                    
                    return self::generateFunctionOutput($object[0], $functionName, $attributes);
                }
            }
        }

        return '<h4 class="error">This tag "' . $object[1] . '" is not registered! [' . $object[0] . ']</h4>';
    }

    protected function sortAnyTagAttributes($tagName, $attributes, $content = null) {
        $params = array();
        foreach ($attributes as $usedName => $usedValue) {
            $params[$usedName] = $usedValue;
        }

        $result = [];
        $result["tagName"] = [
            "value" => $tagName,
            "type" => "raw"
        ];

        if ($content != null) {
            $result[DefaultPhp::$FullTagTemplateName] = [
                'value' => $content, 
                'type' => 'raw'
            ];
        }

        $result[DefaultPhp::$ParamsName] = [
            "value" => $params, 
            "type" => "eval"
        ];

        return $result;
    }

    /**
     *
     *  Function parses custom property, call right function & return content.
     *  
     *  @param  cprop  custom property as string
     *  @return return of custom property function     
     *
     */
    protected function parsecproperty($cprop) {
        $object = explode(":", $cprop[1]);
        $attributes = array();
        $this->Attributes = array();

        global $phpObject;
        if ($phpObject->isRegistered($object[0])) {
            if ($phpObject->isProperty($object[0], $object[1])) {
                $functionName = $phpObject->getFuncToProperty($object[0], $object[1], $this->PropertyUse);

                if ($this->UseCaching) {
                    self::addSingletonGlobalObject('$' . $object[0] . 'Object');
                    return '\'.$' . $object[0] . 'Object->' . $functionName . '("' . $this->PropertyAttr . '").\'';
                } else {
                    return self::generateFunctionOutput($object[0], $functionName, array(array('value' => $this->PropertyAttr, 'type' => 'raw')), false);
                }
            } else if($phpObject->isAnyProperty($object[0])) {
                $functionName = 'getProperty';

                if ($this->UseCaching) {
                    self::addSingletonGlobalObject('$' . $object[0] . 'Object');
                    return '\'.$' . $object[0] . 'Object->' . $functionName . '("' . $object[1] . '", "' . $this->PropertyAttr . '").\'';
                } else {
                    return self::generateFunctionOutput($object[0], $functionName, array(array('value' => $object[1], 'type' => 'raw'), array('value' => $this->PropertyAttr, 'type' => 'raw')), false);
                }
            }
        }

        // #131 - Apostrophes are later stripped when binding to number attribute.
        if ($object[0] == 'query' && strlen($object[1]) > 0){
            return "'" . addcslashes($_GET[$object[1]], "'") . "'";
        } elseif($object[0] == 'post' && strlen($object[1]) > 0){
            return "'" . addcslashes($_POST[$object[1]], "'") . "'";
        } else {
            return "'" . addcslashes($cprop[0], "'") . "'";
        }
    }

    /**
     *
     * 	Parse custom tags from Content and save result to Result
     *
     */
    public function parse($content) {
        self::startMeasure();

        $result = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $content);
        self::checkPregError("parsectag");

        $result = eval("return '". $result . "';");
				
        self::stopMeasure($content);
        return $result;
    }
	
	public function parsePropertyExactly($value) {
        $this->PropertyAttr = '';
        $this->PropertyUse = 'get';
        
        $result = preg_replace_callback($this->ATT_PROPERTY_RE, array(&$this, 'parsecproperty'), $value);
        self::checkPregError("parsecproperty", $value);

        if ($result == NULL) {
            return $value;
        }
        
        $result = eval("return ". $result . ";");
        return $result;
	}

    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function checkPregError($function, $value = null) {
        global $phpObject;

        $message = null;
        $error = preg_last_error();
        if ($error == PREG_NO_ERROR) {
            // $message = "There is no error.";
        }
        else if ($error == PREG_INTERNAL_ERROR) {
            $message = "There is an internal error";
        }
        else if ($error == PREG_BACKTRACK_LIMIT_ERROR) {
            $message = "Backtrack limit was exhausted";
        }
        else if ($error == PREG_RECURSION_LIMIT_ERROR) {
            $message = "Recursion limit was exhausted";
        }
        else if ($error == PREG_BAD_UTF8_ERROR) {
            $message = "Bad UTF8 error";
        }
        else if ($error == PREG_BAD_UTF8_OFFSET_ERROR) {
            $message = "Bad UTF8 offset error";
        }
        else if ($error == PREG_JIT_STACKLIMIT_ERROR) {
            $message = "JIT stack limit error";
        }

        if ($message != null) {
            self::log("CustomTagParser '$function': $message, '$value'");
        }
    }

    // Returns <c>true</c> evaluation should be stopped; Otherwise <c>false</c>.
    protected function tryProcessGlobalAttributes($attributes) {
        foreach ($attributes as $key => $att) {
            if ($key == 'security:requireGroup') {
                global $loginObject;
                $ok = false;
                foreach ($loginObject->getGroups() as $group) {
                    if ($group['name'] == $att['value']) {
                        $ok = true;
                        break;
                    }
                }
                if (!$ok) {
                    return true;
                }
                unset($attributes[$key]);
            } elseif ($key == 'security:requirePerm') {
                global $loginObject;
                $perm = $loginObject->getGroupPerm($att['value'], $loginObject->getMainGroupId(), false, 'false');
                if ($perm['value'] != 'true') {
                    return true;
                }
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }

    protected function log($var) {
        global $phpObject;
        $phpObject->logVar($var);
    }
}

?>