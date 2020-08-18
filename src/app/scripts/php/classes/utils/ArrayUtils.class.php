<?php

    class ArrayUtils {
        
        public static function isEqual($a, $b) {
            return is_array($a) && is_array($b) && count($a) == count($b) && array_diff($a, $b) === array_diff($b, $a);
        }

    }

?>