<?php

    class ArrayUtils {
        
        public static function isEqual($a, $b) {
            return is_array($a) && is_array($b) && count($a) == count($b) && array_diff($a, $b) === array_diff($b, $a);
        }
        
        public static function removeKeysWithEmptyValues($array) {
            foreach ($array as $key => $value) {
                if (is_null($value) || $value == '') {
                    unset($array[$key]);
                }
            }

            return $array;
        }

    }

?>