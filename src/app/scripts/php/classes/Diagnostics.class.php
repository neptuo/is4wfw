<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/Formatter.class.php");

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
				.'<div>' . Formatter::toByteString($this->initMemory) . '</div>'
				.'<div style="color: red; font-weight: bold;">Current memory used:</div>'
				.'<div>' . Formatter::toByteString(memory_get_usage()) . '</div>'
				.'<div style="color: red; font-weight: bold;">Difference:</div>'
				.'<div>' . Formatter::toByteString(self::getMyUsedMemory()) . '</div>'
				.'<div style="color: red; font-weight: bold;">Peak memory:</div>'
				.'<div>' . Formatter::toByteString(memory_get_peak_usage()) . '</div>'
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
