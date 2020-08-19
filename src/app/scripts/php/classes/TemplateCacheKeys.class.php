<?php

    class TemplateCacheKeys {

        public static function page(int $pageId, int $langId, string $contentPart) {
            return ["page", $pageId, $langId, $contentPart];
        }

    }

?>