<?php

class Formatter {
    
    public static function toByteString($value) {
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $i = 0;
        while ($i < count($sizes)) {
            if ($value > 1000) {
                $value = $value / 1000;
                $i++;
            } else {
                break;
            }
        }

        if ($i >= count($sizes)) {
            $i = count($sizes) - 1;
        }

        return round($value, 2) . ' ' . $sizes[$i];
    }
}

?>