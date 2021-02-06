<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");

    class Validator
    {
        private static $EmailRegex = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';

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

        public static function email(EditModel $model, string $key) {
            if (!self::isEmail($model[$key])) {
                self::addInvalidValue($model, $key);
            }
        }

        public static function isEmail($value) {
            return preg_match(self::$EmailRegex, $value);
        }
    }

?>