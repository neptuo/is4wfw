<?php

class Version {
    public static function parse($version) {
        $major = null;
        $path = null;

        if ($version[0] == 'v') {
            $version = substr($version, 1);
        }

        $version = explode('.', $version);
        $major = intval($version[0]);
        if (count($version) > 1) {
            $path = $version[1];
        } else {
            $path = 0;
        }

        return array('major' => $major, 'patch' => $path);
    }

    public static function toString($version) {
        if (is_array($version)) {
            return 'v' . $version['major'] . '.' . $version['patch'];
        }

        return null;
    }
}

?>