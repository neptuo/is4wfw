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
			self::setModelValue($modelKey, self::peekEditModel()->request()[$requestKey]);
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

		public function forEachListModel($template, $model, $params = array()) {
			self::pushListModel($model);
			$result = "";

			$where = self::findAttributesByPrefix($params, "filter-");
			
			if ($model->isRegistration()) {
				self::parseContent($template);
			}
			
			if ($model->isRender()) {
				foreach ($model->items() as $item) {
					if (self::isPassedByWhere($item, $where)) {
						$model->data($item);
						$result .= self::parseContent($template);
					}
				}
			}

			self::popListModel();
			return $result;
		}

		private function isPassedByWhere($item, $where) {
			foreach ($where as $key => $value) {
				if ($item[$key] != $value) {
					return false;
				}
			}

			return true;
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

		public function emptyListModel($template, $model) {
			if ($model->isRegistration()) {
				self::parseContent($template);
			}
			
			if ($model->isRender()) {
				if (count($model->items()) == 0) {
					return self::parseContent($template);
				}
			}
		}

		public function anyListModel($template, $model) {
			if ($model->isRegistration()) {
				self::parseContent($template);
			}
			
			if ($model->isRender()) {
				if (count($model->items()) > 0) {
					return self::parseContent($template);
				}
			}
		}

		// ------- GRID -------------------------------------------------------

		private $gridPhase = "";

		public function grid($template, $model, $params = array()) {
			if ($model->isRender()) {
				$result = "";

				$items = $model->items();
				if (count($items) > 0) {
					$attributes = self::joinAttributes($params);

					// Header
					$this->gridPhase = "header";
					$result .= "<table$attributes>";
					$result .= "<tr>";
					$result .= self::parseContent($template);
					$result .= "</tr>";
					
					// Body
					$this->gridPhase = "body";
					foreach ($items as $item) {
						$model->data($item);
						
						$result .= "<tr>";
						$result .= self::parseContent($template);
						$result .= "</tr>";
					}
					
					// Reset
					$this->gridPhase = "";
					$result .= "</table>";
				}

				return $result;
			}
		}

		public function gridColumn($header, $value) {
			if ($this->gridPhase == "header") {
				return "<th>$header</th>";
			} else if ($this->gridPhase == "body") {
				return "<td>$value</td>";
			}

			return "";
		}

		public function gridColumnBoolean($header, $value, $trueText = "Yes", $falseText = "") {
			return self::gridColumn($header, $value ? $trueText : $falseText);
		}

		public function gridColumnTemplate($template, $header) {
			if ($this->gridPhase == "header") {
				return "<th>$header</th>";
			} else if ($this->gridPhase == "body") {
				return "<td>" . self::parseContent($template) . "</td>";
			}

			return "";
		}



		// ------- EDITORS ----------------------------------------------------

		public function form($template, $method = "post", $pageId = null, $params = array()) {
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

		public function filter($template, $submit, $params = array()) {
            $model = new EditModel();
			self::pushEditModel($model);

			if (array_key_exists($submit, $_POST)) {
				$model->submit();
				$model->request($_POST);
				self::parseContent($template);
				$model->submit(false);

				$url = $_SERVER['REQUEST_URI'];

                foreach ($model as $key => $value) {
					if (is_array($value)) {
						$value = implode(",", $value);
					}

					if (!empty($value)) {
						$url = self::addUrlParameter($url, $key, $value);
					} else {
						$url = self::removeUrlParameter($url, $key);
					}
				}

				self::redirectToUrl($url);
				return;
			} else {
				foreach ($_GET as $key => $value) {
					$model[$key] = $value;
				}
			}
			
            $model->render();
            $result = self::form($template, "POST", null, $params);
            self::popEditModel();
            return $result;
		}
		
		public function dropdownlist($name, $source, $display, $id, $emptyText = "", $params = array()) {
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

				if (!empty($emptyText)) {
					$result .= "<option value=''" . (empty($modelValue) ? " selected='selected'" : "") . ">$emptyText</option>";
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
				$modelValue = self::peekEditModel()->request()[$name];
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

				$result = "";
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

		public function textarea($name, $default = "", $params = array()) {
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
				return "<textarea name='$name'$attributes>$modelValue</textarea>";
			}
		}

		public function checkbox($name, $params = array()) {
			if (self::isRegistration()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				$modelValue = self::peekEditModel()->request()[$name];
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

		private $localizableName;
		private $localizableLangId;
		private $localizableLangName;

		private function ensureLangIds($langIds) {
			if (empty($langIds)) {
				$sql = self::sql()->select("language", array("id"));
				$langIds = self::dataAccess()->fetchAll($sql);
			}

			return $langIds;
		}

		public function localizable($template, $name, $langIds = "") {
			$langIds = explode(",", $langIds);

			$model = self::peekEditModel();
            if ($model->isRegistration()) {
				$langIds = self::ensureLangIds($langIds);
				foreach ($langIds as $langId) {
					$this->localizableName = "$name:$langId";
					self::parseContent($template);
				}
			}
			
            if ($model->isSubmit()) {
				$langIds = self::ensureLangIds($langIds);
				foreach ($langIds as $langId) {
					$this->localizableName = "$name:$langId";
					self::parseContent($template);
				}
            }

            if ($model->isRender()) {
				$sql = self::sql()->select("language", array("id", "language"), array("id" => $langIds));
				$data = self::dataAccess()->fetchAll($sql);
				
				$result = "";
				foreach ($data as $lang) {
					$this->localizableName = "$name:" . $lang["id"];
					$this->localizableLangId = $lang["id"];
					$this->localizableLangName = $lang["language"];
					$result .= self::parseContent($template);
				}

				return $result;
            }
		}

		public function getLocalizableName() {
			return $this->localizableName;
		}
		
		public function getLocalizableLangId() {
			return $this->localizableLangId;
		}
		
		public function getLocalizableLangName() {
			return $this->localizableLangName;
		}
	}

?>