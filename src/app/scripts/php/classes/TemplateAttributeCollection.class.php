<?php

    class TemplateAttributeCollection {
        public $HasDecorators = false;
        public $HasBodyProvidingDecorators = false;
        public $HasAttributeModifyingDecorators = false;
        public $HasConditionalDecorators = false;

        public $Attributes = [];
        public $Decorators = [];
        public $FunctionParameters = [];

        public function HasTemplateAttribute() {
            foreach ($this->Attributes as $attribute) {
                if ($this->IsTemplateAttribute($attribute)) {
                    return true;
                }
            }

            return false;
        }

        public function IsTemplateAttribute($attribute) {
            if ($attribute["type"] == "eval") {
                if (array_key_exists("content", $attribute)) {
                    return $attribute["content"] == "template";
                } else if(is_array($attribute["value"])) {
                    foreach ($attribute["value"] as $value) {
                        if ($this->IsTemplateAttribute($value)) {
                            return true;
                        }
                    }
                }

            }

            return false;
        }
    }

?>