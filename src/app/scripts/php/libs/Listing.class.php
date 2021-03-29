<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Listing. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-03-09
	 * 
	 */
	class Listing extends BaseTagLib {

        private $container;
		private $currentName = "";

		public function __construct() {
			parent::setTagLibXml("Listing.xml");
		}

		private function ensureDeclaration($name, $throw = true) {
			if (!array_key_exists($name, $this->container)) {
				$e = new ParameterException("name", "Missing declaration for list model named '$name'");
				if ($throw) {
					throw $e;
				} else {
					$this->log($e->getMessage());
					$this->setValue($name);
				}
			}
		}

		private function ensureName($name) {
			if ($name == "") {
				if ($this->currentName == "") {
					throw new ParameterException("name", "Missing parameter 'name'");
				}
				
				return $this->currentName;
			} else {
				if ($this->currentName != "") {
					throw new ParameterException("name", "Parameter 'name' can't be used inside of list:declare full tag");
				}
				
				$this->ensureDeclaration($name);
				return $name;
			}
		}

		public function setValue($name, $fromArray = "") {
			$this->container[$name] = new ListModel();
			if (is_array($fromArray)) {
				$this->container[$name]->items($fromArray);
			}
			
			$this->container[$name]->render();
		}

		public function setValueFullTag($template, $name) {
			$oldName = $this->currentName;
			$this->currentName = $name;
			$this->container[$name] = new ListModel();
			$template();
			$this->container[$name]->render();
			$this->currentName = $oldName;
		}
		
		public function addItem($key, $name = "") {
			$name = $this->ensureName($name);
			$this->container[$name]->addItem($key);
		}
		
		public function getProperty($name) {
			$name = explode("-", $name, 2);
			$this->ensureDeclaration($name[0], false);

			$model = $this->container[$name[0]];

			if (count($name) == 1) {
				return $model;
			}

			if ($name[1] == "_") {
				return $model->data();
			}

			return $model->field($name[1]);
		}
	}

?>