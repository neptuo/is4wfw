<?php

    class FileUtils {
        
        public static function removeDirectory($dir) {
            self::clearDirectory($dir);
            return rmdir($dir);
        }

        public static function clearDirectory($dir) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                $itemPath = $dir . "/" . $file;
                if (is_dir($itemPath)) {
                    FileUtils::removeDirectory($itemPath);
                } else {
                    unlink($itemPath); 
                }
            } 
        }

    }


?>