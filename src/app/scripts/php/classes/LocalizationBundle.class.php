<?php

	require_once(APP_SCRIPTS_PHP_PATH . "classes/MissingLocalizationBundleKeyException.class.php");

	class LocalizationBundle {
		
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
		 *	If the current bundle is the system one.
		 *
		 */
		private $IsSystem = true;
		
		/**
		 *
		 *	Array with Items.
		 *
		 */
		private $Items = array();
		
		/**
		 *
		 *	Sets source name.
		 *	
		 *	@param name source name		 		 
		 *
		 */		 		 		 		
		public function setSource($name) {
			$this->Source = $name;
		}
		
		/**
		 *
		 *	Sets resource bundle language.
		 *	
		 *	@param lang language		 		 
		 *
		 */		 		 		 		
		public function setLanguage($lang) {
			$this->Language = $lang;
		}
		
		/**
		 *
		 *	Sets whether resource bundle is system or user defined.
		 *	
		 *	@param isSystem whether resource bundle is system or user defined		 		 
		 *
		 */		 		 		 		
		public function setIsSystem($isSystem) {
			$this->IsSystem = $isSystem;
		}

		private function getFilePath() {
			$name = $this->Source . "_" . $this->Language;
			if ($this->IsSystem) {
				return APP_SCRIPTS_BUNDLES_PATH . $name . ".properties";
			} else {
				return USER_BUNDLES_PATH . $name . ".properties";
			}
		}
		
		/**
		 *
		 *	Tests if bundle file exists.
		 *	
		 *	@param name source name
		 *	@param lang language
		 *	@return true if file exists, false otherwise
		 *
		 */	 		 		 		
		public function exists($name = false, $lang = false, $isSystem = null) {
			if ($name != false) {
				$this->Source = $name;
			}
			
			if ($lang != false) {
				$this->Language = $lang;
			}

			if ($isSystem != null) {
				$this->IsSystem = $isSystem;
			}
			
			$filePath = self::getFilePath();
			if (strlen($filePath) > 0 && is_file($filePath) && is_readable($filePath)) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 *
		 *	Loads bundle content for use.
		 *	
		 *	@param name source name
		 *	@param lang language
		 *
		 */		 		 		 		
		public function load($name = false, $lang = false) {
			if ($name != false) {
				$this->Source = $name;
			}
			
			if ($lang != false) {
				$this->Language = $lang;
			}
			
			$filePath = self::getFilePath();
			if (strlen($filePath) > 0 && is_file($filePath) && is_readable($filePath)) {
				$content = file_get_contents($filePath);
				$pairs = explode(PHP_EOL, $content);

				foreach ($pairs as $pair) {
					$pair = trim($pair);
					if (strlen($pair) > 0) {
						$thing = explode('=' ,$pair);
						$this->Items[$thing[0]] = $thing[1];
					}
				}
			} else {
				echo 'Some error in resource bundle!';
				exit;
			}
		}

		public function save() {
			if ($this->IsSystem) {
				throw new Exception("System bundle '" . $this->Source . "' can't be overwritten.");
			} else {
				if (!file_exists(USER_BUNDLES_PATH)) {
					mkdir(USER_BUNDLES_PATH);
				}
			}

			$filePath = self::getFilePath();
			if (strlen($filePath)) {
				$content = "";
				foreach ($this->Items as $key => $value) {
					$content .= "$key=$value" . PHP_EOL;
				}

				file_put_contents($filePath, $content);
			}
		}
		
		/**
		 *
		 *	Returns value assigned to key.
		 *	
		 *	@param key blob key
		 *	@return value assigned to key		 		 		 
		 *
		 */	
		public function get($key) {
			if (array_key_exists($key , $this->Items)) {
				return $this->Items[$key];
			} else {
				throw new MissingLocalizationBundleKeyException($key, $this->Source, $this->Language);
			}
		}

		/**
		 *
		 *	Assignes value to a key.
		 *	
		 *	@param key blob key
		 *	@param value assigned value
		 *
		 */
		public function set($key, $value) {
			$this->Items[$key] = $value;
		}

		/**
		 *
		 *	Removes key from the bundle.
		 *	
		 *	@param key blob key
		 *
		 */
		public function remove($key) {
			unset($this->Items[$key]);
		}

		/**
		 *
		 *	Returns a collection of all keys.
		 *
		 */
		public function getKeys() {
			return array_keys($this->Items);
		}
	}

?>