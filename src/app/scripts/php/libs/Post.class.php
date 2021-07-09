<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Post. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-07-22
	 * 
	 */
	class Post extends BaseTagLib {

		public function getProperty($name) {
			if (array_key_exists($name, $_POST)) {
				return $_POST[$name];
			}

			return "";
		}
	}

?>