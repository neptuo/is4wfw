<?php

	class ResourceBundle {
		
		/**
		 *
		 *	Source name.
		 *
		 */		 		 		 		
		private $Source = "";
		
		/**
		 *
		 *	Bundle language.
		 *
		 */		 		 		 		
		private $Language = "";
		
		/**
		 *
		 *	Array with blobs.
		 *
		 */
		private $Blobs = array();
		
		/**
		 *
		 *	Setups source name.
		 *	
		 *	@param		name					source name		 		 
		 *
		 */		 		 		 		
		public function setSource($name) {
			$this->Source = $name;
		}
		
		/**
		 *
		 *	Setups resoubce bundle language.
		 *	
		 *	@param		lang					language		 		 
		 *
		 */		 		 		 		
		public function setLanguage($lang) {
			$this->Language = $lang;
		}
		
		/**
		 *
		 *	Tests if bundle file exists.
		 *	
		 *	@param		name					source name
		 *	@param		lang					language
		 *	@return		true if file exists, false otherwise
		 *
		 */	 		 		 		
		public function testBundleExists($name = false, $lang = false) {
			if($name != false) {
				$this->Source = $name;
			}
			
			if($lang != false) {
				$this->Language = $lang;
			}
			
			$fname = 'scripts/bundles/'.$this->Source.'_'.$this->Language.'.properties';
			
			if(strlen($fname) > 0 && is_file($fname) && is_readable($fname)) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 *
		 *	Loads bundle content for use.
		 *	
		 *	@param		name					source name
		 *	@param		lang					language
		 *
		 */		 		 		 		
		public function loadBundle($name = false, $lang = false) {
			if($name != false) {
				$this->Source = $name;
			}
			
			if($lang != false) {
				$this->Language = $lang;
			}
			
			$fname = 'scripts/bundles/'.$this->Source.'_'.$this->Language.'.properties';
			
			if(strlen($fname) > 0 && is_file($fname) && is_readable($fname)) {
				$content = file_get_contents($fname);
				$pairs = split('
', $content);

				foreach($pairs as $pair) {
					if(strlen($pair) > 0) {
						$thing = split('=' ,$pair);
						$this->Blobs[$thing[0]] = $thing[1];
					}
				}
			} else {
				echo 'Some error in resource bundle!';
				exit;
			}
		}
		
		/**
		 *
		 *	Returns value to passed key.
		 *	
		 *	@param		key					blob key
		 *	@return		value to passed key		 		 		 
		 *
		 */		 		 		 		
		public function get($key) {
			if(array_key_exists($key , $this->Blobs)) {
				return $this->Blobs[$key];
			} else {
				echo 'Error! Key "'.$key.'" doesn\'t exist.';
				exit;
				// some error message
			}
		}
	}

?>