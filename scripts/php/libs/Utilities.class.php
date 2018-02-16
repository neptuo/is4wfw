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
 *  @timestamp  2018-02-16
 * 
 */
class Utilities extends BaseTagLib {

    public function __construct() {
        parent::setTagLibXml("xml/Utilities.xml");
    }
}

?>