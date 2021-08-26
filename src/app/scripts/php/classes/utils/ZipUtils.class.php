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

        public static function getFileContent($zipFileName, $fileName) {
            $contents = '';
            $zip = new ZipArchive();
            if ($zip->open($zipFileName)) {
                $fileStream = $zip->getStream($fileName);
                if (!$fileStream) {
                    return false;
                }

                while (!feof($fileStream)) {
                    $contents .= fread($fileStream, 2);
                }

                fclose($fileStream);
                $zip->close();
                return $contents;
            }

            return false;
        }

    }

?>