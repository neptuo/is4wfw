<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Post. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2024-01-10
	 * 
	 */
	class HttpHeaders extends BaseTagLib {

		public function getProperty($name) {
			$headers = $this->requestHeaders();
			if (array_key_exists($name, $headers)) {
				return $headers[$name];
			}

			return "";
		}
	}

?>