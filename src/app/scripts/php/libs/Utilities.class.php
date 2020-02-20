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

        public function concatValues($output, $value1, $value2, $value3 = false, $value4 = false, $value5 = false, $value6 = false, $value7 = false, $value8 = false, 
            $value9 = false, $value10 = false, $value11 = false, $value12 = false, $value13 = false, $value14 = false, $value15 = false) {
            
            $this->OutputValues[$output] = $value1 . $value2 . $value3 . $value4 . $value5 . $value6 . $value7 . $value8 . $value9 . $value10 . $value11 . $value12 . $value13 . $value14 . $value15;
            return "";
        }

        public function addToArray($output, $key = array()) {
            if (!array_key_exists($output, $this->OutputValues)) {
                $this->OutputValues[$output] = array();
            }

            if (count($key) == 1 && array_key_exists("", $key)) {
                $key = $key[""];
            }

            $this->OutputValues[$output][] = $key;
        }

        public function replaceHtmlNewLines($output, $input) {
            $isXhtml = parent::web()->getDoctype() == "xhtml";
            $replaced = nl2br($input, $isXhtml);
            $this->OutputValues[$output] = $replaced;
        }

        public function clear($output) {
            unset($this->OutputValues[$output]);
        }

        public function getProperty($name) {
            return $this->OutputValues[$name];
        }
    }

?>