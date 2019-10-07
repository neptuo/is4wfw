<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Localization. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2019-10-07
	 * 
	 */
	class Localization extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Localization.xml");
		}
		
		public function setLanguage($name) {
			self::web()->LanguageName = $name;
		}
	}

?>