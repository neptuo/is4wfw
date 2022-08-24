<?php

    require_once("CustomEntityBase.class.php");

    class CustomEntityFields extends BaseTagLib
    {
        private const defaultName = "01beee83-4a2a-4074-adaa-098c0e5796bd";
        private $currentName;
        private $storage = [];

        public function setValue(callable $template, $name = "") {
            if (empty($name)) {
                $name = CustomEntityFields::defaultName;
            }

            $oldCurrentName = $this->currentName;
            $this->currentName = $name;

            $template();
            $this->currentName = $oldCurrentName;
        }

        public function add($name, $alias = "", $aggregation = "", $function = "") {
            if (empty($this->currentName)) {
                throw new Exception("Missing fieldset name.");
            }

            $this->storage[$this->currentName][] = [
                "name" => $name,
                "alias" => $alias,
                "aggregation" => $aggregation,
                "function" => $function
            ];
        }

        public function getDefault() {
            return $this->getProperty(CustomEntityFields::defaultName);
        }

        public function getProperty($name) {
            if (array_key_exists($name, $this->storage)) {
                return $this->storage[$name];
            }

            return [];
        }
    }

?>