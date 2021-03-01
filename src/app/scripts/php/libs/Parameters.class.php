<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Parameters. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-02-28
	 * 
	 */
	class Parameters extends BaseTagLib {

        private $container;
		private $currentName = "";

		public function __construct() {
			parent::setTagLibXml("Parameters.xml");
		}

		private function ensureDeclaration($name) {
			if (!array_key_exists($name, $this->container)) {
				throw new ParameterException("name", "Missing declaration for parameter collection named '$name'.");
			}
		}

		private function ensureName($name) {
			if ($name == "") {
				if ($this->currentName == "") {
					throw new ParameterException("name", "Missing parameter 'name'.");
				}
				
				return $this->currentName;
			} else {
				if ($this->currentName != "") {
					throw new ParameterException("name", "Parameter 'name' can't be used inside of params:declare full tag.");
				}
				
				$this->ensureDeclaration($name);
				return $name;
			}
		}

		private function createDeclaration($name, $addEmpty) {
			$this->container[$name] = [
				"data" => [],
				"addEmpty" => $addEmpty
			];
		}
		
		public function setValue($name, $key = array(), $copyCurrent = "", $addEmpty = false) {
			$this->createDeclaration($name, $addEmpty);
			
			if ($copyCurrent != "") {
				$this->copyCurrent($copyCurrent, "", $name);
			}

			foreach ($key as $keyName => $keyValue) {
				$this->setKey($keyName, $keyValue, $name);
			}
		}

		public function setValueFullTag($template, $name, $addEmpty = false) {
			$oldName = $this->currentName;
			$this->currentName = $name;
			$this->createDeclaration($name, $addEmpty);
			$template();
			$this->currentName = $oldName;
		}
		
		public function setKey($key, $value, $name = "") {
			$name = $this->ensureName($name);
			
			if ($value != "" || $this->container[$name]["addEmpty"]) {
				$this->container[$name]["data"][$key] = $value;
			}
		}
		
		public function copyCurrent($include = "*", $exclude = "", $name = "") {
			$include = explode(",", $include);
			$exclude = explode(",", $exclude);
			foreach	(UrlUtils::getCurrentQueryString() as $key => $value) {
				if (in_array($key, $include) || (in_array("*", $include) && !in_array($key, $exclude))) {
					$this->setKey($key, $value, $name);
				}
			}
		}
		
		public function getProperty($name) {
			$this->ensureDeclaration($name);
			return $this->container[$name]["data"];
		}
	}

?>