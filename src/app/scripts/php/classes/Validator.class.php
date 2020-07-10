<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");

    class Validator
    {
        public static function addRequired(EditModel $model, string $key) {
            $model->validationMessage($key, "required");
        }

        public static function addUnique(EditModel $model, string $key) {
            $model->validationMessage($key, "unique");
        }

        public static function addInvalidValue(EditModel $model, string $key) {
            $model->validationMessage($key, "invalid");
        }

        public static function addMustMatch(EditModel $model, string $key, string $otherKey = null) {
            $model->validationMessage($key, "mustmatch");
            if ($otherKey != null) {
                $model->validationMessage($otherKey, "mustmatch");
            }
        }

        public static function required(EditModel $model, string $key) {
            if (empty($model[$key])) {
                self::addRequired($model, $key);
            }
        }
    }

?>