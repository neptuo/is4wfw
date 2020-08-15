<?php

    class StringUtils {

        public static function explode($s, $d, $c = 1000000) {
            if (strlen($d) == 1) {
                $res = array();
                $t = "";
                for ($i = 0; $i < strlen($s); $i++) {
                    if ($s[$i] == $d && ($i < (strlen($s) - 1) && $i > 0)) {
                        if ($c > 0) {
                            $res[] = $t;
                            $t = "";
                            $c--;
                        } else {
                            $t .= $s[$i];
                        }
                    } elseif ($s[$i] != $d) {
                        $t .= $s[$i];
                    }
                }
                $res[] = $t;
                $t = "";
                return $res;
            } else {
                return $s;
            }
        }

        public static function escapeHtmlEntities($value) {
            $escapeChars = array("&" => "&amp;", '>' => '&gt;', '<' => '&lt;', '"' => '&quot;', "~" => "&#126;");
            $value = strtr($value, $escapeChars);
            return $value;
        }

        public static function startsWith($haystack, $needle) {
            $length = strlen($needle);
            return (substr($haystack, 0, $length) === $needle);
        }

        public static function endsWith($haystack, $needle) {
            $length = strlen($needle);

            return $length === 0 || (substr($haystack, -$length) === $needle);
        }

    }

?>