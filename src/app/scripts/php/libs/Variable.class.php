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

		public function __construct() {
			parent::setTagLibXml("Variable.xml");
		}
		
		public function setValue($name, $value, $scope, $select = "") {
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
			} else if ($scope == 'cookie') {
				setcookie($name, $value);
			} else if($scope == 'application') {
				parent::dao('ApplicationVariable')->setValue($name, $value);
			} else {
				trigger_error("Invalid scope value '" . $scope . "'.");
			}

			return '';
		}

		public function getProperty($name) {
			if (parent::request()->exists($name, 'variable')) {
				return parent::request()->get($name, 'variable');
			}

			if (parent::session()->exists($name, 'variable')) {
				return parent::session()->get($name, 'variable');
			}

			$application = parent::dao('ApplicationVariable')->getValue($name);
			return $application;
		}
	}

?>