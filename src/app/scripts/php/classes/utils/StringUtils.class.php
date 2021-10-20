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

        public static function join($base, $item, $separator = ", ") {
            if (strlen($base) > 0) {
                $base .= $separator;
            }

            return $base . $item;
        }

        public static function format($format, $modelOrCallback) {
            if (is_callable($modelOrCallback)) {
                $result = preg_replace_callback(
                    "({([a-zA-Z0-9-_.]*)})", 
                    function($match) use ($modelOrCallback) {
                        return $modelOrCallback($match[1]);
                    }, 
                    $format
                );

                return $result;
            }

            foreach ($modelOrCallback as $key => $value) {
                $source = "{" . $key . "}";

                if (is_callable($value)) {
                    continue;
                }

                $format = str_replace($source, $value, $format);
            }

            return $format;
        }

    }

?>