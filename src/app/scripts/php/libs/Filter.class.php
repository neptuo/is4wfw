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
		private $current;

		public function __construct() {
			parent::setTagLibXml("Filter.xml");
			$this->current = new Stack();
		}

		private function formatColumnName($instance, $name) {
			$alias = $instance->alias;
			if (empty($alias))
				return "`$name`";
			return "$alias.`$name`";
		}
		
		public function declareInstance($template, $name, $alias = "") {
			$instance = new FilterModel();
			$instance->alias = $alias;
			$instance->joiner = "AND";
			$this->instances[$name] = $instance;
			$this->current->push($instance);

			self::parseContent($template);
			
			$this->current->pop();
		}

		private function joiner($template, $joiner) {
			$parent = $this->current->peek();
			$instance = new FilterModel();
			$instance->alias = $parent->alias;
			$instance->joiner = $joiner;
			$this->current->push($instance);
			
			self::parseContent($template);

			$this->current->pop();
			$sql = $instance->toSql();
			$parent[] = $sql;
		}
		
		public function operatorAnd($template) {
			self::joiner($template, "AND");
		}
		
		public function operatorOr($template) {
			self::joiner($template, "OR");
		}

		public function exists($template, $from, $alias, $outerColumn, $innerColumn) {
			$parent = $this->current->peek();
			$instance = new FilterModel();
			$instance->alias = $alias;
			$instance->joiner = "AND";
			$this->current->push($instance);

			$outerColumn = self::formatColumnName($parent, $outerColumn);
			$innerColumn = self::formatColumnName($instance, $innerColumn);
			$instance[] = "$outerColumn = $innerColumn";

			self::parseContent($template);

			$this->current->pop();
			$sql = $instance->toSql();
			$sql = "EXISTS(SELECT * FROM `$from` AS $alias WHERE $sql)";
			$parent[] = $sql;
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

			if (!empty($values)) {
				$instance[] = self::formatColumnName($instance, $name) . " IN ($values)";
			}
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
			
			if (!empty($value)) {
				$instance[] = self::formatColumnName($instance, $name) . " LIKE $value";
			}
		}

		public function getProperty($name) {
			return $this->instances[$name];
		}
	}

?>