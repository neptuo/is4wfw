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
    protected $TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)( )+((([a-zA-Z0-9-]+[:]?[a-zA-Z0-9-]*)="[^"]*"( )*)*)\/>)';
    /**
     *
     *  Regular expression for parsing attribute.
     *
     */
    protected $ATT_RE = '(([a-zA-Z0-9-]+[:]?[a-zA-Z0-9-]*)="([^"]*)")';
    protected $PROP_RE = '(([a-zA-Z0-9]+:[a-zA-Z0-9-]+))';
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

    /**
     *
     *  Function parses custom tag, call right function & return content.
     *
     *  @param  ctag  custom tag as string
     *  @return return of custom tag function
     *
     */
    protected function parsectag($ctag) {
        $object = explode(":", $ctag[1]);
        $attributes = array();
        $this->Attributes = array();
		
		if($this->TagsToParse != array()) {
			$skip = true;
			foreach($this->TagsToParse as $tag) {
				if($ctag[1] == $tag) {
					$skip = false;
					break;
				}
			}
			
			if($skip) {
				return $ctag[0];
			}
		}

        preg_replace_callback($this->ATT_RE, array(&$this, 'parseatt'), $ctag[3]);

        foreach ($this->Attributes as $tmp) {
            $att = explode("=", $tmp);
            if (strlen($att[0]) > 0) {
                $this->PropertyAttr = '';
                $this->PropertyUse = 'get';
                $att[1] = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $att[1]);
                $attributes[$att[0]] = str_replace("\"", "", $att[1]);
            }
        }

        foreach ($attributes as $key => $att) {
            if ($key == 'security:requireGroup') {
                global $loginObject;
                $ok = false;
                foreach ($loginObject->getGroups() as $group) {
                    if ($group['name'] == $att) {
                        $ok = true;
                        break;
                    }
                }
                if (!$ok) {
                    return '';
                }
            } elseif ($key == 'security:requirePerm') {
                global $loginObject;
                $perm = $loginObject->getGroupPerm($att, $loginObject->getMainGroupId(), false, 'false');
                if ($perm['value'] != 'true') {
                    return '';
                }
            }
        }

        global $phpObject;
        if ($phpObject->isRegistered($object[0]) && $phpObject->isTag($object[0], $object[1], $attributes)) {
            $attributes = $phpObject->sortAttributes($object[0], $object[1], $attributes);

            global ${$object[0] . "Object"};
            $func = $phpObject->getFuncToTag($object[0], $object[1]);
            if ($func && ($attributes !== false)) {
                $attstring = "";
                $i = 0;
                foreach ($attributes as $att) {
                    $attstring .= "'" . $att . "'";
                    if ($i < (count($attributes) - 1)) {
                        $attstring .= ", ";
                    }
                    $i++;
                }
                //echo '$return =  $'.$object[0].'Object->'.$func.'('.$attstring.');<br />';
                if ($this->UseCaching) {
                    self::addSingletonGlobalObject('$' . $object[0] . 'Object');
                    //$return = '\'.$'.$object[0].'Object->'.$func.'('.$attstring.').\'';
                    $return = '<?php echo $' . $object[0] . 'Object->' . $func . '(' . $attstring . ') ?>';
                } else {
                    eval('$return =  ${$object[0]."Object"}->{$func}(' . $attstring . ');');
                }
                return $return;
            }
        } else {
            echo '<h4 class="error">This tag isn\'t registered! [' . $object[0] . ']</h4>';
            return "";
        }
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
        if ($phpObject->isRegistered($object[0]) && $phpObject->isProperty($object[0], $object[1])) {
            global ${$object[0] . "Object"};
            $func = $phpObject->getFuncToProperty($object[0], $object[1], $this->PropertyUse);
            //eval('$return =  ${$object[0]."Object"}->{$func}("'.$this->PropertyAttr.'");');

            if ($this->UseCaching) {
                self::addSingletonGlobalObject('$' . $object[0] . 'Object');
                $return = '\'.$' . $object[0] . 'Object->' . $func . '("' . $this->PropertyAttr . '").\'';
            } else {
                eval('$return =  ${$object[0]."Object"}->{$func}("' . $this->PropertyAttr . '");');
            }
            return $return;
        } elseif($object[0] == 'query' && strlen($object[1]) > 0){
            return $_GET[$object[1]];
        } elseif($object[0] == 'post' && strlen($object[1]) > 0){
            return $_POST[$object[1]];
        } else {
            //echo "<h4 class=\"error\">This tag isn't registered! [".$object[0]."]</h4>";
            return $cprop[0];
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
        $return = '';
        if ($this->UseCaching) {
            $hashName = sha1($this->Content);
            $fileName = TEMPLATES_CACHE_DIR . $hashName . '.cache.php';
            if (!file_exists($fileName)) {
                $this->Result = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $this->Content);
                $objcts = '';
                foreach ($this->GlobalObjects as $obj) {
                    $objcts .= 'global ' . $obj . '; ';
                }
                file_put_contents($fileName, '<?php ' . $objcts . ' ?>' . $this->Result);
                $this->Result = '';
            }

            ob_start(array(&$this, 'OnOutput'));
            include $fileName;
            ob_end_clean();
        } else {
            $this->Result = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $this->Content);
        }
    }
	
	public function parseProperty($value) {		
		$this->PropertyAttr = '';
		$this->PropertyUse = 'get';
		return preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $value);
	}

    public function OnOutput($data) {
        $this->Result .= $data;
    }

    /**
     *
     * 	Returns Result of parsing
     *
     * 	@return	result
     *
     */
    public function getResult() {
        return $this->Result;
    }

}

?>