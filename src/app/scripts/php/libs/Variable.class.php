<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

	/**
	 * 
	 *  Class Variable. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2018-01-24
	 * 
	 */
	class Variable extends BaseTagLib {

		private $tempValues = [];
		private $nameScopes = [];

		public function __construct() {
			parent::setTagLibXml("Variable.xml");
		}
		
		public function setValue($name, $value, $scope = "request", $select = "") {
			if ($value instanceof ListModel) {
				$value = $value->items();
			}

			if (is_array($value)) {
				if (!empty($select)) {
					$result = array();
					foreach ($value as $item) {
						$result[] = $item[$select];
					}

					$value = $result;
				}
			}

			if ($scope == 'request') {
				parent::request()->set($name, $value, 'variable');
			} else if ($scope == 'session') {
				parent::session()->set($name, $value, 'variable');
			} else if ($scope == 'temp') {
				$this->tempValues[$name] = $value;
			} else if ($scope == 'cookie') {
				setcookie($name, $value);
			} else if($scope == 'application') {
				parent::dao('ApplicationVariable')->setValue($name, $value);
			} else {
				trigger_error("Invalid scope value '" . $scope . "'.");
			}

			return '';
		}

		public function setScope($name, $scope) {
			$this->nameScopes[$name] = $scope;
		}

		private function isScopeAvailableForName($name, $scope) {
			if (array_key_exists($name, $this->nameScopes)) {
				return $this->nameScopes[$name] == $scope;
			}

			return true;
		}

		public function removeValue($name) {
			if ($this->isScopeAvailableForName($name, "request") && parent::request()->exists($name, 'variable')) {
				return parent::request()->delete($name, 'variable');
			}

			if ($this->isScopeAvailableForName($name, "session") && parent::session()->exists($name, 'variable')) {
				return parent::session()->delete($name, 'variable');
			}

			if ($this->isScopeAvailableForName($name, "temp")) {
				if (array_key_exists($name, $this->tempValues)) {
					unset($this->tempValues[$name]);
					return;
				}

				if (parent::session()->exists($name, 'variable-temp')) {
					return parent::session()->delete($name, 'variable-temp');
				}
			}

			if ($this->isScopeAvailableForName($name, "application")) {
				parent::dao('ApplicationVariable')->delete($name);
				return;
			}
		}

		public function getProperty($name) {
			if ($this->isScopeAvailableForName($name, "request") && parent::request()->exists($name, 'variable')) {
				return parent::request()->get($name, 'variable');
			}

			if ($this->isScopeAvailableForName($name, "session") && parent::session()->exists($name, 'variable')) {
				return parent::session()->get($name, 'variable');
			}

			if ($this->isScopeAvailableForName($name, "temp")) {
				if (array_key_exists($name, $this->tempValues)) {
					return $this->tempValues[$name];
				}

				if (parent::session()->exists($name, 'variable-temp')) {
					return parent::session()->get($name, 'variable-temp');
				}
			}

			if ($this->isScopeAvailableForName($name, "application")) {
				$application = parent::dao('ApplicationVariable')->getValue($name);
				return $application;
			}

			return null;
		}

		public function dispose() {
			parent::session()->clear('variable-temp');

			foreach ($this->tempValues as $name => $value) {
				parent::session()->set($name, $value, 'variable-temp');
			}
		}
	}

?>