<?php

	class BaseGrid {
		
		private $header = array();
		
		private $rows = array();
		
		private $classes = array();
		
		public function __contruct() {
			
		}
		
		/**
		 *
		 *	Takes array of captions for table header row.
		 *	array('id' => 'Id:', 'name' => 'Name:')
		 *	Keys must be the same as for row array!!
		 *
		 */		 		 		 		
		public function setHeader($newHeader) {
			$this->header = $newHeader;
		}
		
		/**
		 *
		 *	Adds row to grid. Takes array of fields.
		 *	array('id' => 5, 'name' => "Foo")
		 *	Keys must be the same as for header array!!		 		 
		 *
		 */		 		 		 		
		public function addRow($row) {
			$this->rows[] = $row;
		}
		
		/**
		 *
		 *	Adds set of rows, see addRow.
		 *
		 */		 		 		 		
		public function addRows($rows) {
			foreach($rows as $row) {
				self::addRow($row);
			}
		}
		
		public function addClass($className) {
			if(!in_array($className, $this->classes)) {
				$this->classes[] = $className;
			}
		}
		
		/**
		 *
		 *	Returns html table
		 *
		 */		 		 		 		
		public function render() {
			$classNames = '';
			foreach($this->classes as $class) {
				$classNames .= ' '.$class;
			}
			$return = '<table class="standart'.$classNames.'">'
			.'<tr>';
			foreach($this->header as $th) {
				$return .= '<th>'.$th.'</th>';
			}
			$return .= '</tr>';
			
			$i = 1;
			foreach($this->rows as $tr) {
				$return .= '<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">';
				foreach($this->header as $key=>$th) {
					$return .= '<td>'.$tr[$key].'</td>';
				}
				$return .= '</tr>';
				$i++;
			}
			
			$return .= '</table>';
			
			return $return;
		}
		
	}

?>
