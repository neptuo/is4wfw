<?php

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
            parent::setTagLibXml("Utilities.xml");
        }

        public function concatValues($output, $value1, $value2, $value3 = false, $value4 = false, $value5 = false) {
            $this->OutputValues[$output] = $value1 . $value2 . $value3 . $value4 . $value5;
            return "";
        }

        public function addToArray($output, $key = array()) {
            if (!array_key_exists($output, $this->OutputValues)) {
                $this->OutputValues[$output] = array();
            }

            $this->OutputValues[$output][] = $key;
        }

        public function clear($output) {
            unset($this->OutputValues[$output]);
        }

        public function getProperty($name) {
            return $this->OutputValues[$name];
        }
    }

?>