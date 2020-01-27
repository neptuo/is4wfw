<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Cookie. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-01-27
	 * 
	 */
	class Cookie extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Cookie.xml");
		}
		
		public function setValue($name, $value, $expire = 0, $path = "", $domain = "", $isSecure = false, $isHttpOnly = false) {
			if ($value != "") {
				$result = setcookie($name, $value, $expire, $path, $domain, $isSecure, $isHttpOnly);
			} else {
				unset($_COOKIE[$name]);
    			setcookie($name, "", time() - 3600, $path, $domain, $isSecure, $isHttpOnly);
			}
		}

		public function getProperty($name) {
			if (array_key_exists($name, $_COOKIE)) {
				return $_COOKIE[$name];
			}

			return "";
		}
	}

?>