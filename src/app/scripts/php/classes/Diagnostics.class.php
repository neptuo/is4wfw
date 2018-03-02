<?php

	class Diagnostics {
		
		private $initMemory = 0;
		private $startTime = 0;	
		
		public function __construct() {
			$this->initMemory = memory_get_usage();
			$this->startTime = microtime(true);
		}
		
		public function getMyUsedMemory() {
			return memory_get_usage() - $this->initMemory;
		}
		
		public function getAllUsedMemory() {
			return memory_get_usage();
		}
		
		public function printMemoryStats() {
			return ''
			.'<div style="border: 2px solid #666666; margin: 10px; padding: 10px; background: #eeeeee;">'
				.'<div style="color: red; font-weight: bold;">Initial memory used:</div>'
				.'<div>'.$this->initMemory.'B</div>'
				.'<div style="color: red; font-weight: bold;">Current memory used:</div>'
				.'<div>'.memory_get_usage().'B</div>'
				.'<div style="color: red; font-weight: bold;">Difference:</div>'
				.'<div>'.self::getMyUsedMemory().'B</div>'
				.'<div style="color: red; font-weight: bold;">Peak memory:</div>'
				.'<div>'.memory_get_peak_usage().'B</div>'
			.'</div>';
		}
		
		public function getDuration() {
			return (microtime(true) - $this->startTime);
		}
		
		public function printDuration() {
			return ''
			.'<div style="border: 2px solid #666666; margin: 10px; padding: 10px; background: #eeeeee;">'
				.'<div style="color: red; font-weight: bold;">Script duration:</div>'
				.'<div>'.self::getDuration().'s</div>'
			.'</div>';
		}
	}

?>
