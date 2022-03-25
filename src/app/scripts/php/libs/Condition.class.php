<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	/**
	 * 
	 *  Class Condition.
	 *      
	 *  @author     maraf
	 *  @timestamp  2022-03-16
	 * 
	 */
	class Condition extends BaseTagLib {

		private $instances = [];
		private $current;

		public function __construct() {
			$this->current = new Stack();
		}

		private function appendToCurrent($value) {
			$instance = $this->current->peek();
			$instance->append($value);
		}

		public function append($value, $name) {
			if ($name) {
				$this->evaluate(function() use($value) { $this->appendToCurrent($value); }, $name);
			} else {
				$this->appendToCurrent($value);
			}
		}

		public function evaluate($template, $name) {
			$instance = new ConditionEvaluation("AND");
			$this->instances[$name] = $instance;
			$this->current->push($instance);

			$template();
			
			$this->current->pop();
		}

		private function parenthesis(callable $template, string $operator, bool $invertResult = false) {
			$parent = $this->current->peek();
			$instance = new ConditionEvaluation($operator);
			$this->current->push($instance);
			
			$template();

			$this->current->pop();
			$result = $instance->result;
			if ($invertResult) {
				$result = !$result;
			}

			$parent->append($result);
		}
		
		public function operatorAnd($template) {
			$this->parenthesis($template, "AND");
		}
		
		public function operatorOr($template) {
			$this->parenthesis($template, "OR");
		}
		
		public function not($template) {
			$operator = $this->current->peek()->operator;
			$this->parenthesis($template, $operator, true);
		}

		public function equals($value, $is, $not = false, $name = "") {
			$result = $value === $is;
			if ($not) {
				$result = !$result;
			}

			$this->append($result, $name);
		}
		
		public function greater($value, $than, $orEqual = false, $name = "") {
			if ($orEqual) {
				$result = $value >= $than;
			} else {
				$result = $value > $than;
			}

			$this->append($result, $name);
		}
		
		public function lower($value, $than, $orEqual = false, $name = "") {
			if ($orEqual) {
				$result = $value <= $than;
			} else {
				$result = $value < $than;
			}
	
			$this->append($result, $name);
		}

		public function arrayContains($value, $item, $name = "") {
			$this->append(in_array($item, $value), $name);
		}

		public function arrayLength($value, $min = -1, $max = -1, $is = -1, $orEqual = false, $name = "") {
			$length = count($value);
			return $this->testLength($length, $min, $max, $is, $orEqual, $name);
		}

		public function stringContains($value, $part, $caseSensitive = true, $name = "") {
			if ($caseSensitive) {
				$this->append(strpos($value, $part) !== false, $name);
			} else {
				$this->append(stripos($value, $part) !== false, $name);
			}
		}

		public function stringLength($value, $min = -1, $max = -1, $is = -1, $orEqual = false, $name = "") {
			$length = strlen($value);
			return $this->testLength($length, $min, $max, $is, $orEqual, $name);
		}

		public function testLength($length, $min, $max, $is, $orEqual, $name = "") {
			if ($min >= 0) {
				if ($orEqual) {
					$result = $length >= $min;
				} else {
					$result = $length > $min;
				}

				if (!$result) {
					$this->append(false, $name);
					return;
				}
			}

			if ($max >= 0) {
				if ($orEqual) {
					$result = $length <= $max;
				} else {
					$result = $length < $max;
				}

				if (!$result) {
					$this->append(false, $name);
					return;
				}
			}

			if ($is >= 0) {
				$result = $length == $is;
				if (!$result) {
					$this->append(false, $name);
					return;
				}
			}

			$this->append(true, $name);
		}

		public function getProperty($name) {
			return $this->instances[$name]->result;
		}

		public function isPassed($name) {
			return [PhpRuntime::$DecoratorExecuteName => $this->getProperty($name)];
		}
		
		public function isFailed($name) {
			return [PhpRuntime::$DecoratorExecuteName => !$this->getProperty($name)];
		}

		public function simpleEvaluation($true = PhpRuntime::UnusedAttributeValue, $stringEmpty = PhpRuntime::UnusedAttributeValue, $not = false) {
			$result = true;
			
			if ($true !== PhpRuntime::UnusedAttributeValue) {
				$result = $result && $true === true;
			}
			
			if ($stringEmpty !== PhpRuntime::UnusedAttributeValue) {
				$result = $result && ($stringEmpty === null || $stringEmpty === "");
			}

			if ($not === true) {
				$result = !$result;
			}

			return [PhpRuntime::$DecoratorExecuteName => $result];
		}
	}

	class ConditionEvaluation {
		public $result = true;
		public $operator;

		public function __construct($operator) {
			$this->operator = $operator;
			$this->result = $operator == "AND"; // true for AND; false for OR.
		}

		public function append($value) {
			if ($this->operator == "AND") {
				$this->result = $this->result && $value;
			} else {
				$this->result = $this->result || $value;
			}
		}
	}

?>