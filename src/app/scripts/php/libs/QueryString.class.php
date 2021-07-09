<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class QueryString. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-07-22
	 * 
	 */
	class QueryString extends BaseTagLib {

		public function getProperty($name) {
			if (array_key_exists($name, $_GET)) {
				return $_GET[$name];
			}

			return "";
		}
	}

?>