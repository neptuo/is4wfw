<?php

	require_once("BaseTagLib.class.php");
	require_once("Router.class.php");

	/**
	 * 
	 *  Class Route. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-07-08
	 * 
	 */
	class Route extends BaseTagLib {

		private $routes = [];

		private $useName;

		public function router(): Router {
			return $this->autolib("router");
		}

		public function set($name, $url) {
			$this->routes[$name] = $url;
		}

		public function use($template, $name) {
			$lastName = $this->useName;
			$this->useName = $name;

			$result = $template();

			$this->$name = $lastName;

			return $result;
		}

		public function getName() {
			return $this->useName;
		}

		public function getUrl() {
			return $this->getProperty($this->useName);
		}

		public function getIsActive() {
			return $this->useName == $this->router()->getSelectedName();
		}

		public function getProperty($name) {
			if (array_key_exists($name, $this->routes)) {
				return $this->routes[$name];
			}

			throw new Exception("Named route '$name' not found.");
		}
	}

?>