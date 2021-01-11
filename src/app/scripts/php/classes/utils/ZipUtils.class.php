<?php

    class ZipUtils {

        public static function extract($zipFileName, $targetPath) {
            $zip = new ZipArchive();
            $extractResult = $zip->open($zipFileName);
            if ($extractResult === true) {
                $extractResult = $zip->extractTo($targetPath);
                $zip->close();
                return $extractResult === true;
            }

            return false;
        }

    }

?>