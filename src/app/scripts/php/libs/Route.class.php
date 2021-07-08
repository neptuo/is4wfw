<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Route. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-07-08
	 * 
	 */
	class Route extends BaseTagLib {

		public function __construct() {
			$this->setTagLibXml("Route.xml");
		}

		public function router($template) {
			$template();
		}

		public function directory($template, $path) {
			$template();
		}

		public function file($template, $path) {
			$template();
		}
	}

?>