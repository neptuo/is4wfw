<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");

/**
 * 
 *  Class Utilities. 
 *      
 *  @author     maraf
 *  @timestamp  2018-02-16
 * 
 */
class Utilities extends BaseTagLib {

    private $OutputValues = array();

    public function __construct() {
        parent::setTagLibXml("xml/Utilities.xml");
    }

    public function concatValues($output, $value1, $value2, $value3 = false, $value4 = false, $value5 = false) {
        $this->OutputValues[$output] = $value1 . $value2 . $value3 . $value4 . $value5;
        return "";
    }

    public function getProperty($name) {
        return $this->OutputValues[$name];
    }
}

?>