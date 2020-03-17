<?php

class Version {
    public static function parse($version) {
        $major = null;
        $path = null;
        $preview = null;

        if ($version[0] == 'v') {
            $version = substr($version, 1);
        }

        $version = explode('.', $version);
        $major = intval($version[0]);
        if (count($version) > 1) {

            $version = explode('-', $version[1]);
            $path = intval($version[0]);
            if (count($version) > 1) {
                $preview = $version[1];
            }
        } else {
            $path = 0;
        }

        return array('major' => $major, 'patch' => $path, 'preview' => $preview);
    }

    public static function toString($version, $includePreview = false) {
        $result = null;
        if (is_array($version)) {
            $result = 'v' . $version['major'] . '.' . $version['patch'];
        }

        if ($includePreview && !empty($version["preview"])) {
            $result .= "-" . $version["preview"];
        }

        return $result;
    }
}

?>