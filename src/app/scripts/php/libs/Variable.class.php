<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

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
		private $localScopes;

		public function __construct() {
			$this->localScopes = new Stack();
		}

		public function declareScope($template) {
			$this->localScopes->push(new RequestStorage());
			try {
				$output = $template();
			} finally {
				$this->localScopes->pop();
			}
			return $output;
		}

		public function setValue($name, $value, $scope = PhpRuntime::UnusedAttributeValue, $select = "") {
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

			if ($scope == PhpRuntime::UnusedAttributeValue) {
				$scope = $this->findScope($name);
				if ($scope == null) {
					if ($this->localScopes->isEmpty()) {
						$scope = "request";
					} else {
						$scope = "local";
					}
				}
			}

			if ($scope == 'local') {
				$this->localScopes->peek()->set($name, $value, 'variable');
			} else if ($scope == 'request') {
				parent::request()->set($name, $value, 'variable');
			} else if ($scope == 'session') {
				parent::session()->set($name, $value, 'variable');
			} else if ($scope == 'temp') {
				$this->tempValues[$name] = $value;
			} else if ($scope == 'cookie') {
				setcookie($name, $value);
			} else if ($scope == 'user') {
				if ($this->login()->isLogged()) {
					parent::dao('UserVariable')->setValue($this->login()->getUserId(), $name, $value);
				} else {
					trigger_error("User must be signed-in for variable scope '$scope'.");
				}
			} else if($scope == 'application') {
				parent::dao('ApplicationVariable')->setValue($name, $value);
			} else {
				trigger_error("Invalid scope value '$scope'.");
			}

			return '';
		}

		public function setValueFulltag($template, $name, $scope = PhpRuntime::UnusedAttributeValue) {
			$value = $template();
			return $this->setValue($name, $value, $scope);
		}

		public function setScope($name, $scope) {
			$this->nameScopes[$name] = $scope;
		}

		private function findScope($name) {
			if (array_key_exists($name, $this->nameScopes)) {
				return $this->nameScopes[$name];
			}

			return null;
		}

		private function isScopeAvailableForName($name, $scope) {
			if (array_key_exists($name, $this->nameScopes)) {
				return $this->nameScopes[$name] == $scope;
			}

			return true;
		}

		public function removeValue($name) {
			if ($this->isScopeAvailableForName($name, "local") && !$this->localScopes->isEmpty() && $this->localScopes->peek()->exists($name, 'variable')) {
				return $this->localScopes->peek()->delete($name, 'variable');
			}

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

			if ($this->isScopeAvailableForName($name, "user") && $this->login()->isLogged()) {
				if (parent::dao('UserVariable')->getValue($this->login()->getUserId(), $name) !== null) {
					parent::dao('UserVariable')->delete($this->login()->getUserId(), $name);
					return;
				}
			}

			if ($this->isScopeAvailableForName($name, "application")) {
				parent::dao('ApplicationVariable')->delete($name);
				return;
			}
		}

		public function getProperty($name) {
			if ($this->isScopeAvailableForName($name, "local") && !$this->localScopes->isEmpty() && $this->localScopes->peek()->exists($name, 'variable')) {
				// TODO: Probing all scopes
				return $this->localScopes->peek()->get($name, 'variable');
			}

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

			if ($this->isScopeAvailableForName($name, "user") && $this->login()->isLogged()) {
				$user = parent::dao('UserVariable')->getValue($this->login()->getUserId(), $name);
				if ($user !== null) {
					return $user;
				}
			}

			if ($this->isScopeAvailableForName($name, "application")) {
				$application = parent::dao('ApplicationVariable')->getValue($name);
				return $application;
			}

			return null;
		}

		public function setProperty($name, $value) {
			$this->setValue($name, $value);
			return $value;
		}

		public function dispose() {
			parent::session()->clear('variable-temp');

			foreach ($this->tempValues as $name => $value) {
				parent::session()->set($name, $value, 'variable-temp');
			}
		}
	}

?>