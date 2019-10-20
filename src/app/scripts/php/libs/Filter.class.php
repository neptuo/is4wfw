<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/FilterModel.class.php");
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
		private $current = new Stack();

		public function __construct() {
			parent::setTagLibXml("Filter.xml");
		}

		private function formatColumnName($instance, $name) {
			return $instance->alias . "`$name`";
		}
		
		public function declare($template, $name, $alias = "") {
			$instance = new FilterModel();
			$instance->alias = $alias;
			$this->instances[$name] = $instance;
			$this->current->push($instance);

			self::parseContent($template);
			
			$this->aliases->pop();
		}

		private function joiner($template, $joiner) {
			$parent = $this->current->peek();
			$instance = new FilterModel();
			$instance->joiner = $joiner;
			$this->current->push($instance);
			
			self::parseContent($template);

			$this->current->pop();
			$sql = $instance->toSql();
			$parent[] = $sql;
		}
		
		public function and($template) {
			self::joiner($template, "AND");
		}
		
		public function or($template) {
			self::joiner($template, "AND");
		}

		public function equals($name, $value) {
			$instance = $this->current->peek();
			$value = parent::sql()->escape($value);
			$instance[] = self::formatColumnName($instance, $name) . " = $value";
		}

		public function in($name, $values) {
			$instance = $this->current->peek();

			if (is_string($values)) {
				$values = explode(",", $values);
			}

			if (is_array($values)) {
				$valueString = "";
				foreach ($values as $item) {
					if (!empty($item)) {
						$valueString = parent::joinString($valueString, parent::sql()->escape($item));
					}
				}
				
				$values = $valueString;
			}

			$instance[] = self::formatColumnName($instance, $name) . " IN ($values)";
		}

		public function like($name, $startsWith = "", $endsWith = "", $contains = "") {
			$instance = $this->current->peek();

			$value = "";
			if ($startsWith != "") {
				$value = parent::sql()->escape($startsWith . "%");
			} else if ($endsWith != "") {
				$value = parent::sql()->escape("%" . $endsWith);
			} else if ($contains != "") {
				$value = parent::sql()->escape("%" . $contains . "%");
			}

			$instance[] = self::formatColumnName($instance, $name) . " LIKE $value";
		}

		public function getProperty($name) {
			return $this->instances[$name];
		}
	}

?>