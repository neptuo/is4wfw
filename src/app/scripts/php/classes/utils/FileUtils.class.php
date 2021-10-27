<?php

    class FileUtils {
        
        public static function removeDirectory($dir) {
            self::clearDirectory($dir);
            return rmdir($dir);
        }

        public static function clearDirectory($dir) {
            if (!file_exists($dir)) {
                return;
            }

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

        public static function ensureDirectory($path) {
            if (!file_exists($path)) {
                mkdir($path);
            }
        }

        public static function tail($filepath, $lines = 1, &$startReached = false) {
            // Open file
            $f = @fopen($filepath, "rb");
            if ($f === false) {
                return false;
            }
    
            // Sets buffer size, according to the number of lines to retrieve.
            // This gives a performance boost when reading a few lines from the file.
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
    
            // Jump to last character
            fseek($f, -1, SEEK_END);
    
            // Read it and adjust line number if necessary
            // (Otherwise the result would be wrong if file doesn't end with a blank line)
            if (fread($f, 1) != "\n") {
                $lines -= 1;
            }
            
            // Start reading
            $output = '';
            $chunk = '';
    
            // While we would like more
            while (ftell($f) > 0 && $lines >= 0) {
    
                // Figure out how far back we should jump
                $seek = min(ftell($f), $buffer);
    
                // Do the jump (backwards, relative to where we are)
                fseek($f, -$seek, SEEK_CUR);
    
                // Read a chunk and prepend it to our output
                $output = ($chunk = fread($f, $seek)) . $output;
    
                // Jump back to where we started reading
                fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
    
                // Decrease our line counter
                $lines -= substr_count($chunk, "\n");
    
            }

            $startReached = ftell($f) == 0;
    
            // While we have too many lines
            // (Because of buffer size we might have read too many)
            while ($lines++ < 0) {
    
                // Find first newline and remove all text before that
                $output = substr($output, strpos($output, "\n") + 1);
    
            }
    
            // Close file and return
            fclose($f);
            return trim($output);
        }

        public static function combinePath($path1, $path2, $path3 = null, $path4 = null, $path5 = null) {
            $path1 = FileUtils::combinePathInternal($path1, $path2);
            $path1 = FileUtils::combinePathInternal($path1, $path3);
            $path1 = FileUtils::combinePathInternal($path1, $path4);
            $path1 = FileUtils::combinePathInternal($path1, $path5);

            return $path1;
        }

        private static function combinePathInternal($path1, $path2) {
            $separator = "/";
            if (!empty($path1)) {
                if ($path1[strlen($path1) - 1] == "/") {
                    $separator = "";
                }
            } else {
                $separator = "";
            }

            if (!empty($path2)) {
                if ($path2[0] == "/") {
                    if ($separator == "/") {
                        $separator = "";
                    } else {
                        $path2 = substr($path2, 1);
                    }
                }
            } else {
                $separator = "";
            }

            return $path1 . $separator . $path2;
        }

        public static function searchDirectoryRecursive($directory, $fileExtension) {
            if (!file_exists($directory)) {
                return [];
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            $result = array();
            foreach($iterator as $file) {
                $path = $file->getPathName();
                if (StringUtils::endsWith($path, $fileExtension)) {
                    $result[] = $path;
                }
            }

            return $result;
        }
    }


?>