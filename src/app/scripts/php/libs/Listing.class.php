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

		private function ensureDeclaration($name, $throw = true) {
			if (!array_key_exists($name, $this->container)) {
				if ($throw) {
					throw new ParameterException("name", "Missing declaration for list model named '$name'");
				} else {
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
		
		public function sort($key, $name = "") {
			$name = $this->ensureName($name);
			$items = $this->container[$name]->items();
			usort($items, function($a, $b) use ($key) { 
				foreach ($key as $name => $direction) {
					if ($direction == "desc") {
						$c = $a;
						$a = $b;
						$b = $c;
					}

					$result = strcmp($a[$name], $b[$name]);
					if ($result !== 0) {
						return $result;
					}
				}

				return 0;
			});
			$this->container[$name]->items($items);
		}
		
		public function getProperty($name) {
			$name = explode("-", $name, 2);
			$this->ensureDeclaration($name[0], false);

			$model = $this->container[$name[0]];

			if (count($name) == 1) {
				return $model;
			}

			if ($name[1] == "_") {
				return $model->currentItem();
			}

			return $model->field($name[1]);
		}

		public function setProperty($name, $value) {
			$name = explode("-", $name, 2);
			$this->ensureDeclaration($name[0], false);

			$model = $this->container[$name[0]];

			if (count($name) == 1) {
				return;
			}

			$model->field($name[1], $value);
		}
	}

?>