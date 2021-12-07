<?php

class TemplateCacheKeys {
        
        public static function webProject(int $projectId) {
            return ["webproject", $projectId];
        }

        public static function page(int $pageId, int $langId, string $contentPart) {
            return ["page", $pageId, $langId, $contentPart];
        }

        public static function pageCleanUp(int $pageId, int $langId) {
            return ["page", $pageId, $langId];
        }

        public static function template(int $templateId) {
            return ["template", $templateId];
        }

    }

?>