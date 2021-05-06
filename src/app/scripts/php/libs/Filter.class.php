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

		public static function formatColumnName($instance, $name) {
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

			$template();
			
			$this->current->pop();
		}

		private function joiner($template, $joiner) {
			$parent = $this->current->peek();
			$instance = new FilterModel();
			$instance->alias = $parent->alias;
			$instance->joiner = $joiner;
			$this->current->push($instance);
			
			$template();

			$this->current->pop();
			$sql = $instance->toSql();
			$parent[] = $sql;
		}
		
		public function operatorAnd($template) {
			$this->joiner($template, "AND");
		}
		
		public function operatorOr($template) {
			$this->joiner($template, "OR");
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

			$template();

			$this->current->pop();
			$sql = $instance->toSql();
			$sql = "EXISTS(SELECT * FROM `$from` AS $alias WHERE $sql)";
			$parent[] = $sql;
		}

		public function emptyValue($name, $not = false) {
			$instance = $this->current->peek();
			$operator = "=";
			if ($not) {
				$operator = "!" . $operator;
			}
			
			$instance[] = self::formatColumnName($instance, $name) . " $operator ''";
		}

		public function nullValue($name, $not = false) {
			$instance = $this->current->peek();
			$operator = "IS";
			if ($not) {
				$operator = $operator . " NOT";
			}
			
			$instance[] = self::formatColumnName($instance, $name) . " $operator NULL";
		}

		public function equals($name, $value, $not = false) {
			if (!empty($value)) {
				$instance = $this->current->peek();
				$value = parent::sql()->escape($value);
				$operator = "=";
				if ($not) {
					$operator = "!" . $operator;
				}

				$instance[] = self::formatColumnName($instance, $name) . " $operator $value";
			}
		}

		public function in($name, $values, $not = false) {
			$instance = $this->current->peek();

			if (is_string($values)) {
				$values = explode(",", $values);
			}

			if (is_array($values)) {
				$valueString = "";
				foreach ($values as $item) {
					if (!empty($item)) {
						$valueString = StringUtils::join($valueString, parent::sql()->escape($item));
					}
				}
				
				$values = $valueString;
			}

			$operator = "IN";
			if ($not) {
				$operator = "NOT " . $operator;
			}

			if (!empty($values)) {
				$instance[] = self::formatColumnName($instance, $name) . " $operator ($values)";
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

		private function greateOrLower($name, $than, $orEqual, $operator) {
			$instance = $this->current->peek();
			
			if ($orEqual) {
				$operator .= "=";
			}

			if (!empty($than)) {
				$instance[] = self::formatColumnName($instance, $name) . " $operator $than";
			}
		}
		
		public function greater($name, $than, $orEqual = false) {
			$this->greateOrLower($name, $than, $orEqual, ">");
		}
		
		public function lower($name, $than, $orEqual = false) {
			$this->greateOrLower($name, $than, $orEqual, "<");
		}

		public function geoContains(callable $template, $latitudeName, $longitudeName) {
			$parent = $this->current->peek();
			
			$instance = new FilterModel();
			$this->current->push($instance);
			$template();
			$this->current->pop();

			$area = $instance[0];
			if (!empty($area)) {
				$latitudeName = $this->formatColumnName($parent, $latitudeName);
				$longitudeName = $this->formatColumnName($parent, $longitudeName);
				$parent[] = "ST_CONTAINS($area, point($latitudeName, $longitudeName))";
			}
		}

		public function geoPolygon(callable $template) {
			$instance = $this->current->peek();

			$parentPoints = $this->points;
			$this->points = [];

			$template();

			$points = implode(", ", $this->points);
			if (!empty($points)) {
				$instance[] = "ST_GEOMFROMTEXT('POLYGON(($points))')";
			}

			$this->points = $parentPoints;
		}

		private $points;

		public function geoPoint($latitude, $longitude) {
			$this->points[] = "$latitude $longitude";
		}


		public function getProperty($name) {
			return $this->instances[$name];
		}
	}

?>