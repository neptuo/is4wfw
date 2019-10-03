<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

	class Ui extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Ui.xml");
		}

		private function isRegistration() {
			return self::peekEditModel()->isRegistration();
		}

		private function isSubmit() {
			return self::peekEditModel()->isSubmit();
		}

		private function isRender() {
			return self::peekEditModel()->isRender();
		}

		private function getModelValue($key) {
			return self::peekEditModel()[$key];
		}

		private function setModelValue($key, $value) {
			self::peekEditModel()[$key] = $value;
		}

		private function setModelValueFromRequest($modelKey, $requestKey) {
			self::setModelValue($modelKey, $_REQUEST[$requestKey]);
		}

		private function joinAttributes($params) {
			$attributes = "";
			foreach ($params as $key => $value) {
				$attributes = self::joinString($attributes, "$key='$value'", " ");
			}

			if (!empty($attributes)) {
				$attributes = " $attributes";
			}

			return $attributes;
		}

		// ------- LIST -------------------------------------------------------

		public function getProperty($name) {
			$model = self::peekListModel();
			if ($model == null) {
				return null;
			}

			if ($name == "_") {
				return $model->data();
			}

			return $model->field($name);
		}

		public function forEachListModel($template, $model) {
			self::pushListModel($model);
			$result = "";
			
			if ($model->isRegistration()) {
				self::parseContent($template);
			}
			
			if ($model->isRender()) {
				foreach ($model->items() as $item) {
					$model->data($item);
					$result .= self::parseContent($template);
				}
			}

			self::popListModel();
			return $result;
		}

		private function singleListModel($template, $model, $indexGetter) {
			self::pushListModel($model);
			$result = "";
			
			if ($model->isRegistration()) {
				self::parseContent($template);
			}
			
			if ($model->isRender()) {
				$items = $model->items();
				if (count($items) > 0) {
					$item = $items[$indexGetter($items)];
					$model->data($item);
					$result .= self::parseContent($template);
				}
			}

			self::popListModel();
			return $result;
		}

		public function firstListModel($template, $model) {
			return self::singleListModel($template, $model, function($items) { return 0; });
		}

		public function lastListModel($template, $model) {
			return self::singleListModel($template, $model, function($items) { return count($items) - 1; });
		}

		// ------- EDITORS ----------------------------------------------------

		public function form($template, $method = "post", $action = NULL, $params = array()) {
			if ($action == NULL) {
				$action = $_SERVER['REQUEST_URI'];
			} else {
				$action = self::web()->composeUrl($action);
			}

			$attributes = self::joinAttributes($params);

            return ""
            . "<form method='$method' action='$action'$attributes>"
                . self::parseContent($template)
            . "</form>";
		}
		
		public function dropdownlist($name, $source, $display, $id, $params = array()) {
			if (self::isRegistration()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				self::setModelValueFromRequest($name, $name);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				$attributes = self::joinAttributes($params);
				
				$result = "<select name='$name'$attributes>";

				if (is_array($source)) {
					$data = $source;
				} else {
					$data = self::dataAccess()->fetchAll("SELECT `$id`, `$display` FROM `$source` ORDER BY `$display`;");
				}

				foreach ($data as $item) {
					$itemValue = $item[$id];
					$result .= "<option value='$itemValue'" . ($modelValue == $itemValue ? " selected='selected'" : "") . ">$item[$display]</option>";
				}

				$result .= "</select>";
				return $result;
			}
		}
		
		public function checkboxlist($name, $source, $display, $id, $repeat, $params = array()) {
			if (self::isRegistration()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				$modelValue = $_REQUEST[$name];
				self::setModelValue($name, $modelValue);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				if (!is_array($modelValue)) {
					$modelValue = explode(",", $modelValue);
				}

				if (is_array($source)) {
					$data = $source;
				} else {
					$data = self::dataAccess()->fetchAll("SELECT `$id`, `$display` FROM `$source` ORDER BY `$display`;");
				}
				
				$itemContainerTagName = $repeat == "vertical" ? "div" : "span";
				$attributes = self::joinAttributes($params);

				foreach ($data as $item) {
					$itemValue = $item[$id];
					$result .= ""
					. "<$itemContainerTagName>"
						. "<label>"
							. "<input name='" . $name . "[]' value='$itemValue' type='checkbox'" . (in_array($itemValue, $modelValue) ? " checked='checked'" : '') . "$attributes />"
							. $item[$display]
						. "</label>"
					. "</$itemContainerTagName>";
				}

				return $result;
			}
		}

		public function textbox($name, $default = "", $params = array()) {
			if (self::isRegistration()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				self::setModelValueFromRequest($name, $name);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				if (empty($modelValue)) {
					$modelValue = $default;
				}

				$attributes = self::joinAttributes($params);
				return "<input name='$name' type='text' value='$modelValue'$attributes />";
			}
		}

		public function checkbox($name, $params = array()) {
			if (self::isRegistration()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				$modelValue = $_REQUEST[$name];
				self::setModelValue($name, $modelValue == "on" ? 1 : 0);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				$attributes = self::joinAttributes($params);
				return "<input name='$name' type='checkbox'" . ($modelValue === TRUE || $modelValue === 1 || $modelValue === "1" ? " checked='checked'" : '') . "$attributes />";
			}
		}

		private function ensureModelDefaultValue($model, $name, $format) {
			$value = $model[$name];
            if (empty($value)) {
                $model[$name] = function() use ($model, $format) { return self::formatString($format, $model); };
            }
		}

        public function defaultValue($template, $name, $format) {
            $model = self::peekEditModel();
            if ($model->isRegistration()) {
				self::setModelValue($name, NULL);
			}

            if ($model->isSubmit()) {
				self::parseContent($template);
				self::ensureModelDefaultValue($model, $name, $format);
            }

            if ($model->isRender()) {
                return self::parseContent($template);
            }
        }

        public function defaultValueWithoutEditor($name, $format) {
			$model = self::peekEditModel();
            if ($model->isRegistration()) {
				self::setModelValue($name, NULL);
			}
			
            if ($model->isSubmit()) {
				self::ensureModelDefaultValue($model, $name, $format);
            }
		}
		
		public function constantValue($name, $value) {
			if (self::isSubmit()) {
				self::setModelValue($name, $value);
			}
		}
	}

?>