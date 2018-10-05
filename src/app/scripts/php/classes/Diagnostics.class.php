<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/Formatter.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");

	class Diagnostics extends BaseTagLib {
		
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
				.'<div style="color: black;">' . Formatter::toByteString($this->initMemory) . '</div>'
				.'<div style="color: red; font-weight: bold;">Current memory used:</div>'
				.'<div style="color: black;">' . Formatter::toByteString(memory_get_usage()) . '</div>'
				.'<div style="color: red; font-weight: bold;">Difference:</div>'
				.'<div style="color: black;">' . Formatter::toByteString(self::getMyUsedMemory()) . '</div>'
				.'<div style="color: red; font-weight: bold;">Peak memory:</div>'
				.'<div style="color: black;">' . Formatter::toByteString(memory_get_peak_usage()) . '</div>'
			.'</div>';
		}
		
		public function getDuration() {
			return (microtime(true) - $this->startTime);
		}
		
		public function printDuration() {
			return parent::debugFrame('Script duration', '' . round(self::getDuration(), 2) . 's');
		}
	}

?>
