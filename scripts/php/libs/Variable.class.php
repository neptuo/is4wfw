<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");
require_once("scripts/php/classes/LocalizationBundle.class.php");

/**
 * 
 *  Class Variable. 
 *      
 *  @author     maraf
 *  @timestamp  2017-11-26
 * 
 */
class Variable extends BaseTagLib {

    public function __construct() {
        global $webObject;

        parent::setTagLibXml("xml/Variable.xml");
    }
	
	public function declare($name, $value, $scope) {
		if ($scope == 'request') {
			parent::request()->set($name, $value, 'variable');
		} else if ($scope == 'session') {
			parent::session()->set($name, $value, 'variable');
		}

		return '';
	}
}

?>