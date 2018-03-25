<?php

class CustomTagParser {

    /**
     *
     * 	String for parsing.
     *
     */
    protected $Content = '';
    /**
     *
     * 	String after parsing.
     *
     */
    protected $Result = '';
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

    // Regular expression for parsing property value. It requires exact match (no prefix or appendix text).
    protected $ATT_PROPERTY_RE = '(^([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)$)';
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

                $att[1] = str_replace("\"", "\\\"", $att[1]);
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
        $functionDefinition = "function " . $identifier . "() { global $" . $tagPrefix . "Object; return $" . $tagPrefix . "Object" . '->' . $functionName . '(' . $attributes . "); }";
        
        // self::log($functionDefinition);
        eval($functionDefinition);

        $result = $identifier . "()";
        // $result = "str_replace()";
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
        if ($skipped !== FALSE) {
            return $skipped;
        }

        $attributes = self::tryProcessAttributes($ctag[3]);
        if ($attributes === FALSE) {
            return '';
        }

        if ($phpObject->isRegistered($object[0]) && $phpObject->isTag($object[0], $object[1], $attributes)) {
            $attributes = $phpObject->sortAttributes($object[0], $object[1], $attributes);

            $functionName = $phpObject->getFuncToTag($object[0], $object[1]);
            if ($functionName && ($attributes !== false)) {
                if ($this->UseCaching) {
                    self::addSingletonGlobalObject('$' . $object[0] . 'Object');
                    return '<?php echo $' . $object[0] . 'Object->' . $func . '(' . self::concatAttributesToString($attributes) . ') ?>';
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

        if ($object[0] == 'query' && strlen($object[1]) > 0){
            return "'" . addslashes($_GET[$object[1]]) . "'";
        } elseif($object[0] == 'post' && strlen($object[1]) > 0){
            return "'" . addslashes($_POST[$object[1]]) . "'";
        } else {
            return "'" . addslashes($cprop[0]) . "'";
        }
    }

    /**
     *
     * 	Set content for parsing
     * 	
     * 	@param	content			string for parsing		 		 
     *
     */
    public function setContent($content) {
        $this->Content = $content;
    }

    /**
     *
     * 	Parse custom tags from Content and save result to Result
     *
     */
    public function startParsing() {
        if ($this->UseCaching) {
            $hashName = sha1($this->Content);
            $fileName = CACHE_TEMPLATES_PATH . $hashName . '.cache.php';

            if (!file_exists($fileName)) {
                $this->Result = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $this->Content);
                $objcts = '';
                foreach ($this->GlobalObjects as $obj) {
                    $objcts .= 'global ' . $obj . '; ';
                }
                file_put_contents($fileName, '<?php ' . $objcts . ' ?>' . $this->Result);
                $this->Result = '';
            }

            ob_start();
            include $fileName;
            $this->Result = ob_get_contents();
            ob_end_clean();
        } else {
            $this->Result = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $this->Content);
            self::checkPregError();

            $this->Result = eval("return '". $this->Result . "';");
        }
    }
	
	public function parsePropertyExactly($value) {
        $this->PropertyAttr = '';
        $this->PropertyUse = 'get';
        
        $result = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $value);
        self::checkPregError();

        if ($result == NULL) {
            return $value;
        }
        
        $result = eval("return ". $result . ";");
        return $result;
	}

    public function getResult() {
        return $this->Result;
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

    protected function checkPregError() {
        global $phpObject;

        if (preg_last_error() == PREG_NO_ERROR) {
            // $phpObject->logVar('There is no error.');
        }
        else if (preg_last_error() == PREG_INTERNAL_ERROR) {
            $phpObject->logVar('There is an internal error!');
        }
        else if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
            $phpObject->logVar('Backtrack limit was exhausted!');
        }
        else if (preg_last_error() == PREG_RECURSION_LIMIT_ERROR) {
            $phpObject->logVar('Recursion limit was exhausted!');
        }
        else if (preg_last_error() == PREG_BAD_UTF8_ERROR) {
            $phpObject->logVar('Bad UTF8 error!');
        }
        else if (preg_last_error() == PREG_BAD_UTF8_ERROR) {
            $phpObject->logVar('Bad UTF8 offset error!');
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