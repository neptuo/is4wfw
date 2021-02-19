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

        private $OutputValues = [];
        private $Identifiers = [];

        public function __construct() {
            parent::setTagLibXml("Utilities.xml");
        }

        public function concatValues($output, $separator, $value1, $value2, $value3 = false, $value4 = false, $value5 = false, $value6 = false, $value7 = false, $value8 = false, 
            $value9 = false, $value10 = false, $value11 = false, $value12 = false, $value13 = false, $value14 = false, $value15 = false) {
            
            $values = [
                $value1, 
                $value2, 
                $value3, 
                $value4, 
                $value5, 
                $value6, 
                $value7, 
                $value8, 
                $value9, 
                $value10, 
                $value11, 
                $value12, 
                $value13, 
                $value14, 
                $value15
            ];

            if ($separator != "") {
                $values = array_filter($values, function($value) { return !($value == "" || $value == null); });
            }

            $result = implode($separator, $values);
            $this->OutputValues[$output] = $result;
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

        public function dateTimeToTimestamp($output, $value, $format, $trimTime = false) {
            $date = DateTime::createFromFormat($format, $value);
            if ($date) {
                if ($trimTime) {
                    $date->setTime(0, 0);
                }

                $this->OutputValues[$output] = $date->getTimestamp();
            }
        }

        public function escapeHtml($output, $value) {
            $this->OutputValues[$output] = htmlspecialchars($value);
        }

		public function nextIdentifier($output, $prefix = 'id-') {
            if (!array_key_exists($prefix, $this->Identifiers)) {
                $this->Identifiers[$prefix] = 0;
            }

            $this->Identifiers[$prefix]++;
            $this->OutputValues[$output] = $prefix . $this->Identifiers[$prefix];
		}

        public function clear($output) {
            unset($this->OutputValues[$output]);
        }

        public function getProperty($name) {
            return $this->OutputValues[$name];
        }
    }

?>