<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Session. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2019-10-18
	 * 
	 */
	class Session extends BaseTagLib {

		const StorageKey = "user-values";

		public function setValue($name, $value) {
			if ($value != "") {
				parent::session()->set($name, $value, Session::StorageKey);
			} else {
				parent::session()->delete($name, $value, Session::StorageKey);
			}
		}

		public function getProperty($name) {
			if (parent::session()->exists($name, Session::StorageKey)) {
				return parent::session()->get($name, Session::StorageKey);
			}

			return "";
		}
	}

?>