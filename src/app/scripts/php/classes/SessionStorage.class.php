<?php

	class SessionStorage {
		
		public function set($key, $value, $storage = 'default') {
			if ($key != '') {
				$_SESSION['session-storage'][$storage][$key] = $value;
			}
		}
		
		public function delete($key, $storage = 'default') {
			if ($key != '') {
				unset($_SESSION['session-storage'][$storage][$key]);
			}
		}
		
		public function get($key, $storage = 'default') {
			if ($this->exists($key, $storage)) {
				return $_SESSION['session-storage'][$storage][$key];
			} else {
				return null;
			}
		}
		
		public function clear($storage = 'default') {
			unset($_SESSION['session-storage'][$storage]);
		}
		
		public function exists($key, $storage = 'default') {
			return array_key_exists($key, $_SESSION['session-storage'][$storage]);
		}
		
		public function keys($storage = 'default') {
			if (array_key_exists($storage, $_SESSION['session-storage'])) {
				return array_keys($_SESSION['session-storage'][$storage]);
			}

			return array();
		}
		
		public function dump($storage = 'default') {
			print_r($_SESSION['session-storage'][$storage]);
		}
		
		public function dumpAll() {
			print_r($_SESSION['session-storage']);
		}
	}

?>
