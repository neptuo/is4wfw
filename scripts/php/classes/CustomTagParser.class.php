<?php

	class CustomTagParser {

		/**
		 *
		 *	String for parsing.
		 *
		 */		 		 		 		
		private $Content = '';
		
		/**
		 *
		 *	String after parsing.
		 *
		 */		 		 		 		
		private $Result = '';
	
		/**
		 *
		 *	Custom tag attributes.
		 *
		 */		 		 		 		
		private $Attributes = array();
		
		/**
     *
     *  Regular expression for parsing custom tag.     
     *
     */              
    private $TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+) ((([a-zA-Z0-9]+)="([a-zA-Z0-9\.,\*`_;:/?-]+ *[a-zA-Z0-9\.,\*`_;:/?-]*)*" )*)\/>)';
    
    /**
     *
     *  Regular expression for parsing attribute.
     *
     */                   
    private $ATT_RE = '(([a-zA-Z0-9]+)="([a-zA-Z0-9\.,\*`_;:/?-]+ *[a-zA-Z0-9\.,\*`_;:/?-]*)*")';
    
    /**
     *
     *  Parse all attributes to array.
     *  
     *  @param  att string with attributes
     *  @return array of attributes
     *
     */                   
    private function parseatt($att) {
      $this->Attributes[] = $att[0];
    }
    
    /**
     *
     *  Function parses custom tag, call right function & return content.
     *  
     *  @param  ctag  custom tag as string
     *  @return return of custom tag function     
     *
     */
    private function parsectag($ctag) {
      $object = explode(":", $ctag[1]);
      $attributes = array();
      $this->Attributes = array();
      
      preg_replace_callback($this->ATT_RE, array( &$this,'parseatt'), $ctag[2]);
      
      foreach($this->Attributes as $tmp) {
        $att = explode("=", $tmp);
        if(strlen($att[0]) > 0) {
          $attributes[$att[0]] = str_replace("\"", "", $att[1]);
        }
      }
      
      global $phpObject;
      if($phpObject->isRegistered($object[0]) && $phpObject->isTag($object[0], $object[1], $attributes)) {
        $attributes = $phpObject->sortAttributes($object[0], $object[1], $attributes);
        
        global ${$object[0]."Object"};
        $func = $phpObject->getFuncToTag($object[0], $object[1]);
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
		 *	Set content for parsing
		 *	
		 *	@param	content			string for parsing		 		 
		 *
		 */		 		 		 		
		public function setContent($content) {
			$this->Content = $content;
		}
		
		/**
		 *
		 *	Parse custom tags from Content and save result to Result
		 *
		 */		 		 		 		
		public function startParsing() {
			$this->Result = preg_replace_callback($this->TAG_RE, array( &$this,'parsectag'), $this->Content);
		}
		
		/**
		 *
		 *	Returns Result of parsing
		 *	
		 *	@return	result		 		 
		 *
		 */		 		 		 		
		public function getResult() {
			return $this->Result;
		}
	
	}

?>