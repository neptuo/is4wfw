<?php

	require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");

	/**
	 * 
	 *  Class Validation. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-07-01
	 * 
	 */
	class TestLibrary extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("TestLibrary.xml");
        }

        public function provideAttributes(string $a, int $b = 0, $tagPrefix, $tagName, $tagParameters) {
            return $tagParameters;
        }
    }

?>