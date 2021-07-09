<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

	/**
	 * 
	 *  Class Sort. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-08-14
	 * 
	 */
	class Sort extends BaseTagLib {

		private $definitions = [];
		private $current;

		private function ensureCurrent() {
			if (!array_key_exists($this->current, $this->definitions)) {
				throw new Exception("Missing declaration for '$this->current'.");
			}
		}
		
		public function createDefinition($template, $name) {
			$this->definitions[$name] = new SortModel();

			$oldCurrent = $this->current;
			$this->current = $name;
			$template();
			$this->current = $oldCurrent;

			return '';
		}

		public function setValue($name, $direction = "asc") {
			$this->ensureCurrent();
			if (trim($name) != "") {
				$this->definitions[$this->current][$name] = $direction;
			}
		}

		public function setDefault($name, $direction = "asc") {
			$this->ensureCurrent();
			if (!array_key_exists($name, $this->definitions[$this->current])) {
				$this->setValue($name, $direction);
			}
		}

		public function getProperty($name) {
			if (array_key_exists($name, $this->definitions)) {
				return $this->definitions[$name];
			}

			return [];
		}
	}

?>