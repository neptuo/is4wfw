<?php

    class TemplateCache {

        private function getPath(array $keys) {
            $subPath = implode("/", $keys);
            $path = CACHE_TEMPLATES_PATH . $subPath . ".php";
            return $path;
        }

        public function exists(array $keys) {
            $path = $this->getPath($keys);
            return file_exists($path);
        }
        
        public function delete(array $keys) {
            $path = $this->getPath($keys);
            unlink($path);
        }
        
        public function set(array $keys, string $content) {
            $path = $this->getPath($keys);
            file_put_contents($path, $content);
        }
    }

?>