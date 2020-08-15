<?php

    class UrlUtils {

        public static function addParameter($url, $name, $value = '') {
            $pair = ($value != '') ? $name . '=' . $value : $name;
        
            $queryIndex = strpos($url, '?');
            if ($queryIndex == '') {
                $url .= '?' . $pair;
            } else {
                if (strpos($url, $name, $queryIndex) == '') {
                    $url .= '&' . $pair;
                } else {
                    $query = substr($url, $queryIndex + 1);
                    $query = explode('&', $query);
                    $url = substr($url, 0, $queryIndex);
                    $isFirst = true;
                    foreach ($query as $item) {
                        $keyvalue = explode('=', $item);
                        if ($keyvalue[0] == $name) {
                            $item = $pair;
                        }
        
                        if ($isFirst) {
                            $url .= '?' . $item;
                            $isFirst = false;
                        } else {
                            $url .= '&' . $item;
                        }
                    }
                }
            }

            return $url;
        }

        public static function removeParameter($url, $name) {
            $queryIndex = strpos($url, '?');
            if ($queryIndex == '') {
                return $url;
            }

            if (strpos($url, $name, $queryIndex) == '') {
                return $url;
            }

            $query = substr($url, $queryIndex + 1);
            $query = explode('&', $query);
            $url = substr($url, 0, $queryIndex);
            $isFirst = true;
            foreach ($query as $item) {
                $keyvalue = explode('=', $item);
                if ($keyvalue[0] != $name) {
                    if ($isFirst) {
                        $url .= '?' . $item;
                        $isFirst = false;
                    } else {
                        $url .= '&' . $item;
                    }
                }
            }

            return $url;
        }
        
        public static function addCurrentQueryString($url) {
            foreach ($_GET as $key => $value) {
                if ($key != 'WEB_PAGE_PATH') {
                    $url = UrlUtils::addParameter($url, $key, $value);
                }
            }

            return $url;
        }

        public static function removeQueryString($url) {
            $queryIndex = strpos($url, '?');
            return substr($url, 0, $queryIndex);
        }

        public static function toValidUrl($value, $allowSlash = true) {
            $value = str_replace(' - ', '-', $value);

            $escapeChars = array("ě" => "e", "é" => "e", "ř" => "r", "ť" => "t", "ý" => "y", "ú" => "u", "ů" => "u", "í" => "i", "ó" => "o", "á" => "a", "š" => "s", "ď" => "d", "ž" => "z", "č" => "c", "ň" => "n", "Ě" => "E", "É" => "E", "Ř" => "R", "Ť" => "T", "Ý" => "Y", "Ú" => "U", "Ů" => "U", "Í" => "I", "Ó" => "O", "Á" => "A", "Š" => "S", "Ď" => "D", "Ž" => "Z", "Č" => "C", "Ň" => "N", " " => '-', "\"" => '-', "'" => '-', "(" => '-', ")" => '-', "[" => '-', "]" => '-', "{" => '-', "}" => '-', '&' => '-');
            
            if(strpos($value, "http") !== 0) {
                $escapeChars["."] = "-";
            }
            
            $value = strtr($value, $escapeChars);
            
            if(!$allowSlash) {
                $value = strtr($value, array("/" => "-"));
            }
            
            return $value;
        }
        
    }

?>