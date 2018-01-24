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
 *  @timestamp  2018-01-24
 * 
 */
class Variable extends BaseTagLib {

    public function __construct() {
        global $webObject;

        parent::setTagLibXml("xml/Variable.xml");
    }
	
	public function setValue($name, $value, $scope) {
		if ($scope == 'request') {
			parent::request()->set($name, $value, 'variable');
		} else if ($scope == 'session') {
			parent::session()->set($name, $value, 'variable');
		}

		return '';
	}

	public function getProperty($name) {
		if (parent::request()->exists($name, 'variable')) {
			return parent::request()->get($name, 'variable');
		}

		if (parent::session()->exists($name, 'variable')) {
			return parent::session()->get($name, 'variable');
		}

		return '';
	}
}

?>