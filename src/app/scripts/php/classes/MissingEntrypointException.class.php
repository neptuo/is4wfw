<?php

    class MissingEntrypointException extends Exception {
        public $moduleId;
        public $entrypointId;

        public function __construct($moduleId, $entrypointId) {
            $this->moduleId = $moduleId;
            $this->entrypointId = $entrypointId;

            $this->message = "Missing entrypoint '$entrypointId' in module '$moduleId'.";
        }
    }

?>