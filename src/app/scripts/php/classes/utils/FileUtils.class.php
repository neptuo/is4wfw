<?php

    class FileUtils {
        
        public static function removeDirectory($dir) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                $itemPath = $dir . "/" . $file;
                if (is_dir($itemPath)) {
                    FileUtils::removeDirectory($itemPath);
                } else {
                    unlink($itemPath); 
                }
            } 

            return rmdir($dir); 
        }

    }


?>