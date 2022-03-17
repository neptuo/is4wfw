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

		public function append($value) {
			$instance = $this->current->peek();
			$instance->append($value);
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

		public function equals($value, $is, $not = false) {
			$result = $value === $is;
			if ($not) {
				$result = !$result;
			}

			$this->append($result);
		}
		
		public function greater($value, $than, $orEqual = false) {
			if ($orEqual) {
				$result = $value >= $than;
			} else {
				$result = $value > $than;
			}

			$this->append($result);
		}
		
		public function lower($value, $than, $orEqual = false) {
			if ($orEqual) {
				$result = $value <= $than;
			} else {
				$result = $value < $than;
			}
	
			$this->append($result);
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