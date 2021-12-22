<?php

    require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/Formatter.class.php");

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

            $exploded = [];
            foreach ($values as $value) {
                if (is_array($value)) {
                    $exploded = array_merge($exploded, $value);
                } else if (!($value == "" || $value == null)) {
                    $exploded[] = $value;
                }
            }
            $values = $exploded;

            $result = implode($separator, $values);
            $this->OutputValues[$output] = $result;
            return "";
        }

        public function addToArray($output, $key = array(), $value = "x-x.y-y") {
            if (!array_key_exists($output, $this->OutputValues)) {
                $this->OutputValues[$output] = array();
            }

            if (is_string($key) && $value != "x-x.y-y") {
                $this->OutputValues[$output][$key] = $value;
            } else {
                $this->OutputValues[$output][] = $key;
            }
        }

        public function createArray($output, $key = array()) {
            $this->OutputValues[$output] = $key;
        }
        
        public function splitToArray($output, $value, $separator, $limit = -1) {
            if ($limit == -1) {
                $this->OutputValues[$output] = explode($separator, $value);
            } else {
                $this->OutputValues[$output] = explode($separator, $value, $limit);
            }
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

        public function timestampToDateTime($output, $value, $format) {
            $this->OutputValues[$output] = $this->autolib("ui")->formatDateTime($value, $format);
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

        public function guid($output) {
            $this->OutputValues[$output] = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
        
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
        
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
        
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        public function formatBytes($output, $value) {
            $this->OutputValues[$output] = Formatter::toByteString($value);
        }

        public function clear($output) {
            unset($this->OutputValues[$output]);
        }

        public function getProperty($name) {
            return $this->OutputValues[$name];
        }
    }

?>