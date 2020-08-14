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

		public function __construct() {
			parent::setTagLibXml("Sort.xml");
		}
		
		public function createDefinition($name) {
			$this->definitions[$name] = [];
			return '';
		}

		public function getProperty($name) {
			if (array_key_exists($name, $this->definitions)) {
				return $this->definitions[$name];
			}

			return [];
		}
	}

?>