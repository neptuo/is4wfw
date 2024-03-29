<?php

	class RequestStorage {
		private $data = array();
		
		public function set($key, $value, $storage = 'default') {
			if($key != '') {
				$this->data[$storage][$key] = $value;
			}
		}
		
		public function delete($key, $storage = 'default') {
			if ($key != '') {
				unset($this->data[$storage][$key]);
			}
		}
		
		public function get($key, $storage = 'default') {
			if($this->exists($key, $storage)) {
				return $this->data[$storage][$key];
			} else {
				return null;
			}
		}
		
		public function clear($storage = 'default') {
			unset($this->data[$storage]);
		}
		
		public function exists($key, $storage = 'default') {
			if(!array_key_exists($storage, $this->data)) {
				return false;
			}
		
			return array_key_exists($key, $this->data[$storage]);
		}
		
		public function dump($storage = 'default') {
			print_r($this->data[$storage]);
		}
		
		public function dumpAll() {
			print_r($this->data);
		}
	}

?>
