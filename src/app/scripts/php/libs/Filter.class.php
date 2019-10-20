<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	/**
	 * 
	 *  Class Filter.
	 *      
	 *  @author     maraf
	 *  @timestamp  2019-10-20
	 * 
	 */
	class Filter extends BaseTagLib {

		private $instances = array();
		private $aliases = new Stack();

		public function __construct() {
			parent::setTagLibXml("Filter.xml");
		}
		
		public function declare($template, $name, $alias = "") {
			$instance = array(
				"alias" = $alias
			);
			$this->instances[$name] = $instance;
			$this->aliases->push($alias);

			$instance["query"] = self::parseContent($template);
			
			$this->aliases->pop();
			return '';
		}

		public function getProperty($name) {
			return $this->instances[$name];
		}
	}

?>