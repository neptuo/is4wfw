<?php

  require_once("scripts/php/classes/CustomTagParser.class.php");
  require_once("scripts/php/classes/ResourceBundle.class.php");
  
  
  class FullTagParser extends CustomTagParser {
  	
	/**
     *
     *  Regular expression for parsing full tag.     
     *
     */
    //private $FULL_TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)(\b[^>]*)>(((\s*)|(.*))*)</\1>)';
    //protected $FULL_TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)(( *([a-zA-Z0-9]+="[^"]*") *)*)>(((\s*)|(.*))*)</\1>)';
    protected $FULL_TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)(( *([a-zA-Z0-9:-]+="[^"]*") *)*)>(((\s*)|(.*))*)</\1>)';
    
    /**
     *
     *	Parses full tag
     *
     */		 		 		     
    private function parsefulltag($ctag) {
    	//print_r($ctag);
		$object = explode(":", $ctag[1]);
		$attributes = array();
		$this->Attributes = array();
     
		preg_replace_callback($this->ATT_RE, array( &$this,'parseatt'), $ctag[2]);
     
		foreach($this->Attributes as $tmp) {
			$att = explode("=", $tmp);
			if(strlen($att[0]) > 0) {
				$this->PropertyAttr = '';
				$this->PropertyUse = 'get';
       			$att[1] = preg_replace_callback($this->PROP_RE, array( &$this,'parsecproperty'), $att[1]);
         			$attributes[$att[0]] = str_replace("\"", "", $att[1]);
       		}
		}
     
		foreach($attributes as $key=>$att) {
      		if($key == 'security:requireGroup') {
      			global $loginObject;
	    	 	$ok = false;
  		    	foreach($loginObject->getGroups() as $group) {
  		    		if($group['name'] == $att) {
  		    			$ok = true;
  		    			break;
  		  			}
  		  		}
  		  		if(!$ok) {
	  		  		return '';
  		  		}
	  	  	}
      	}
      
      	global $phpObject;
	    if($phpObject->isRegistered($object[0]) && $phpObject->isFullTag($object[0], $object[1], $attributes)) {
        	$attributes = $phpObject->sortFullAttributes($object[0], $object[1], $attributes, $ctag[5]);
        
        	global ${$object[0]."Object"};
        	$func = $phpObject->getFuncToFullTag($object[0], $object[1]);
        	if($func && ($attributes !== false)) {
	          	$attstring = "";
    	      	$i = 0;
        		foreach($attributes as $att) {
            		$attstring .= "'" . $att . "'";
            		if($i < (count($attributes) - 1)) {
              			$attstring .= ", ";
            		}
            		$i ++;
          		}
          		//echo '$return =  $'.$object[0].'Object->'.$func.'('.$attstring.');';
          		eval('$return =  ${$object[0]."Object"}->{$func}('.$attstring.');');
          		return $return;
        	}  
      	} else {
	        echo '<h4 class="error">This tag isn\'t registered! ['.$object[0].']</h4>';
        	return "";
      	}
    }
    
    /**
	 *
	 *	Parse custom tags from Content and save result to Result
	 *
	 */		 		 		 		
	public function startParsing() {
		$this->Result = preg_replace_callback($this->FULL_TAG_RE, array( &$this,'parsefulltag'), $this->Content);
		$this->Result = preg_replace_callback($this->TAG_RE, array( &$this,'parsectag'), $this->Result);
	}

 }

?>
