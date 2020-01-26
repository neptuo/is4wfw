<?php

	require_once("BaseTagLib.class.php");
    require_once("Session.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

	class Ui extends BaseTagLib {

		private $id;

		public function __construct() {
			parent::setTagLibXml("Ui.xml");
		}

		private function setModelValueFromRequest($model, $modelKey, $requestKey, $index = -1) {
			$value = $model->request($requestKey, $index);
			$model->set($modelKey, $index, $value);
		}

		public function pushId($id) {
			if ($this->id == null) {
				$this->id = new Stack();
			}

			$this->id->push($id);
		}

		public function peekId() {
			if ($this->id == null) {
				return "";
			}

			return $this->id->peek();
		}

		public function popId() {
			if ($this->id == null) {
				return "";
			}

			return $this->id->pop();
		}

		private function appendId($params) {
			if (!array_key_exists("id", $params)) {
				$id = self::peekId();
				if ($id != "") {
					$params["id"] = $id;
				}
			}

			return $params;
		}

		// ------- LIST -------------------------------------------------------

		public function forEachListModel($template, $model, $params = array()) {
			$template = parent::getParsedTemplate($template);

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

		public function grid($template, $model, $thead = array(), $tbody = array(), $params = array()) {
			if ($model->isRender()) {
				$result = "";
				$isWellStructured = count($thead) > 0 || count($tbody) > 0;

				$template = parent::getParsedTemplate($template);

				$items = $model->items();
				if (count($items) > 0) {
					$attributes = self::joinAttributes($params);

					// Header
					$this->gridPhase = "header";
					$result .= "<table$attributes>";

					if ($isWellStructured) {
						$theadAttributes = parent::joinAttributes($thead);
						$result .= "<thead$theadAttributes>";
					}
					
					$result .= "<tr>";
					$result .= $template;
					$result .= "</tr>";

					if ($isWellStructured) {
						$result .= "</thead>";

						$tbodyAttributes = parent::joinAttributes($tbody);
						$result .= "<tbody$tbodyAttributes>";
					}
					
					// Body
					$this->gridPhase = "body";
					foreach ($items as $item) {
						$model->data($item);
						
						$result .= "<tr>";
						$result .= $template;
						$result .= "</tr>";
					}

					if ($isWellStructured) {
						$result .= "</tbody>";
					}
					
					// Reset
					$this->gridPhase = "";
					$result .= "</table>";
				}

				return $result;
			}
		}

		public function gridColumn($header, $value, $th = array(), $td = array()) {
			if ($this->gridPhase == "header") {
				$thAttributes = parent::joinAttributes($th);
				return "<th$thAttributes>$header</th>";
			} else if ($this->gridPhase == "body") {
				$tdAttributes = parent::joinAttributes($td);
				return "<td$tdAttributes>$value</td>";
			}

			return "";
		}

		public function gridColumnBoolean($header, $value, $trueText = "Yes", $falseText = "", $th = array(), $td = array()) {
			return self::gridColumn($header, $value ? $trueText : $falseText, $th, $td);
		}

		public function gridColumnDateTime($header, $value, $format, $th = array(), $td = array()) {
			if ($this->gridPhase == "header") {
				$thAttributes = parent::joinAttributes($th);
				return "<th$thAttributes>$header</th>";
			} else if ($this->gridPhase == "body") {
				$tdAttributes = parent::joinAttributes($td);
				$value = self::formatDateTime($value, $format);
				return "<td$tdAttributes>$value</td>";
			}

			return "";
		}

		public function gridColumnTemplate($template, $header, $th = array(), $td = array()) {
			if ($this->gridPhase == "header") {
				$thAttributes = parent::joinAttributes($th);
				return "<th$thAttributes>$header</th>";
			} else if ($this->gridPhase == "body") {
				$tdAttributes = parent::joinAttributes($td);
				return "<td$tdAttributes>" . self::parseContent($template) . "</td>";
			}

			return "";
		}



		// ------- EDITORS ----------------------------------------------------

		private $isInsideForm = false;

		public function form($template, $method = "post", $pageId = null, $params = array()) {
			if ($this->isInsideForm) {
				return self::parseContent($template);
			} else {
				$this->isInsideForm = true;

				if ($pageId == NULL) {
					$action = $_SERVER['REQUEST_URI'];
				} else {
					$action = self::web()->composeUrl($pageId);
				}

				$params["method"] = $method;
				$params["action"] = $action;
				$attributes = self::joinAttributes($params);

				$result = "<form$attributes>" . self::parseContent($template) . "</form>";
				$this->isInsideForm = false;
				return $result;
			}
		}

		private function input($params) {
			$attributes = self::joinAttributes($params);
            return "<input$attributes />";
		}

		public function inputHidden($name, $value, $params = array()) {
			$params["type"] = "hidden";
			$params["name"] = $name;
			$params["value"] = $value;
			return self::input($params);
		}

		public function inputImage($src, $params = array()) {
			$params["type"] = "image";
			$params["src"] = $src;
			return self::input($params);
		}

		public function filter($template, $session = "") {
            $model = parent::getEditModel();
			$session = explode(",", $session);

			if ($model->isSubmit()) {
				$model->request($_POST);
				self::parseContent($template);
			}

			if ($model->isSave()) {
				$url = $_SERVER['REQUEST_URI'];

                foreach ($model as $key => $value) {
					if (is_array($value)) {
						$value = implode(",", $value);
					}

					if (!empty($value)) {
						if (in_array($key, $session)) {
							parent::session()->set($key, $value, Session::StorageKey);
						} else {
							$url = self::addUrlParameter($url, $key, $value);
						}
					} else {
						if (in_array($key, $session)) {
							parent::session()->delete($key, Session::StorageKey);
						} else {
							$url = self::removeUrlParameter($url, $key);
						}
					}
				}

				self::redirectToUrl($url);
				return;
			}
			
            if ($model->isRender()) {
				foreach ($_GET as $key => $value) {
					$model[$key] = $value;
				}
				
				foreach (parent::session()->keys(Session::StorageKey) as $key) {
					$model[$key] = parent::session()->get($key, Session::StorageKey);
				}

				$result = parent::parseContent($template);
				return $result;
			}
		}
		
		public function dropdownlist($name, $nameIndex = -1, $source, $display, $id, $emptyText = "", $params = array()) {
			$model = parent::getEditModel();
			if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

			if ($model->isSubmit()) {
				self::setModelValueFromRequest($model, $name, $name, $nameIndex);
			}

			if ($model->isRender()) {
				$formName = $model->requestKey($name, $nameIndex);
				$modelValue = $model->get($name, $nameIndex);
				if ($nameIndex != -1) {
					$modelValue = $modelValue[$nameIndex];
				}
				
				$params = self::appendId($params);
				$attributes = self::joinAttributes($params);
				
				$result = "<select name='$formName'$attributes>";

				if (is_array($source)) {
					$data = $source;
				} else if ($source instanceof ListModel) {
					$data = $source->items();
				} else {
					$data = self::dataAccess()->fetchAll(parent::sql()->select($source, array($id, $display), array(), array($display => "asc")));
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
			$model = parent::getEditModel();
			if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

			if ($model->isSubmit()) {
				$modelValue = $model->request($name);
				$model->set($name, null, $modelValue);
			}

			if ($model->isRender()) {
				$formName = $model->requestKey($name);
				$modelValue = $model->get($name, -1);

				if (!is_array($modelValue)) {
					$modelValue = explode(",", $modelValue);
				}

				if (is_array($source)) {
					$data = $source;
				} else if ($source instanceof ListModel) {
					$data = $source->items();
				} else {
					$data = self::dataAccess()->fetchAll(parent::sql()->select($source, array($id, $display), array(), array($display => "asc")));
				}
				
				$itemContainerTagName = $repeat == "vertical" ? "div" : "span";
				$attributes = self::joinAttributes($params);

				$result = "";
				foreach ($data as $item) {
					$itemValue = $item[$id];
					$result .= ""
					. "<$itemContainerTagName>"
						. "<label>"
							. "<input name='" . $formName . "[]' value='$itemValue' type='checkbox'" . (in_array($itemValue, $modelValue) ? " checked='checked'" : '') . "$attributes />"
							. $item[$display]
						. "</label>"
					. "</$itemContainerTagName>";
				}

				return $result;
			}
		}

		private function inputbox($type, $name, $nameIndex = -1, $default = "", $params = array()) {
			$model = parent::getEditModel();
			if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

			if ($model->isSubmit()) {
				self::setModelValueFromRequest($model, $name, $name, $nameIndex);
			}

			if ($model->isRender()) {
				$formName = $model->requestKey($name, $nameIndex);
				$modelValue = $model->get($name, $nameIndex);
				
				if (empty($modelValue)) {
					$modelValue = $default;
				}
				
				$params = self::appendId($params);
				$params["name"] = $formName;
				$params["type"] = $type;
				$params["value"] = $modelValue;
				return self::input($params);
			}
		}

		public function textbox($name, $nameIndex = -1, $default = "", $params = array()) {
			return self::inputbox("text", $name, $nameIndex, $default, $params);
		}

		public function passwordbox($name, $nameIndex = -1, $default = "", $params = array()) {
			return self::inputbox("password", $name, $nameIndex, $default, $params);
		}
		
		public function textarea($name, $nameIndex = -1, $default = "", $params = array()) {
			$model = parent::getEditModel();
			if ($model->isRegistration()) {
				$model->set($name, null, null);
			}
			
			if ($model->isSubmit()) {
				self::setModelValueFromRequest($model, $name, $name, $nameIndex);
			}
			
			if ($model->isRender()) {
				$formName = $model->requestKey($name, $nameIndex);
				$modelValue = $model->get($name, $nameIndex);

				if (empty($modelValue)) {
					$modelValue = $default;
				}

				$params = self::appendId($params);
				$attributes = self::joinAttributes($params);
				return "<textarea name='$formName'$attributes>$modelValue</textarea>";
			}
		}

		public function checkbox($name, $nameIndex = -1, $default = false, $params = array()) {
			$model = parent::getEditModel();
			if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

			if ($model->isSubmit()) {
				$modelValue = $model->request($name, $nameIndex);
				$model->set($name, $nameIndex, $modelValue == "on" ? 1 : 0);
			}

			if ($model->isRender()) {
				$formName = $model->requestKey($name, $nameIndex);
				$modelValue = $model->get($name, $nameIndex);
				if ($modelValue === null) {
					$modelValue = $default;
				}

				$params = self::appendId($params);
				$attributes = self::joinAttributes($params);
				return "<input name='$formName' type='checkbox'" . ($modelValue === TRUE || $modelValue === 1 || $modelValue === "1" ? " checked='checked'" : '') . "$attributes />";
			}
		}

		private function ensureModelDefaultValue($model, $name, $format) {
			$value = $model[$name];
            if (empty($value)) {
                $model[$name] = function() use ($model, $format) { return self::formatString($format, $model); };
            }
		}

        public function defaultValue($template, $name, $format) {
            $model = parent::getEditModel();
            if ($model->isRegistration()) {
				$model->set($name, null, null);
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
			$model = parent::getEditModel();
            if ($model->isRegistration()) {
				$model->set($name, null, null);
			}
			
            if ($model->isSubmit()) {
				self::ensureModelDefaultValue($model, $name, $format);
            }
		}
		
		public function constantValue($name, $value) {
			$model = parent::getEditModel();
			if ($model->isSubmit()) {
				$model->set($name, null, $value);
			}
		}

		public function dateTimeValue($template, $name, $nameIndex, $format, $default = "") {
			$model = parent::getEditModel();
            if ($model->isRegistration()) {
			 	parent::parseContent($template);
			}
			
            if ($model->isSubmit()) {
				$modelValue = $model->request($name, $nameIndex);
				$modelValue = DateTime::createFromFormat($format, $modelValue);
				if ($modelValue !== false) {
					$modelValue = $modelValue->getTimestamp();
				} else {
					$modelValue = null;
				}
				$model->set($name, $nameIndex, $modelValue);
            }

            if ($model->isRender()) {
				$modelValue = $model->get($name, $nameIndex);

				if (empty($modelValue)) {
					$modelValue = $default;
				}

				$modelValue = self::formatDateTime($modelValue, $format);
				$model->set($name, $nameIndex, $modelValue);

				return parent::parseContent($template);
            }
		}

		public function formatDateTime($value, $format) {
			if ($value == null) {
				return null;
			}

			$dateTime = new DateTime();
			$dateTime->setTimestamp($value);
			$value = $dateTime->format($format);

			return $value;
		}

		private $localizableName;
		private $localizableLangId;
		private $localizableLangName;
		private $localizableLangUrl;

		private function ensureLangIds($langIds) {
			if (empty($langIds)) {
				$sql = self::sql()->select("language", array("id"));
				$langIds = self::dataAccess()->fetchAll($sql);
			}

			return $langIds;
		}

		public function localizable($template, $name, $langIds = "") {
			$langIds = explode(",", $langIds);

			$model = self::getEditModel();
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
				$sql = self::sql()->select("language", array("id", "name", "language"), array("id" => $langIds));
				$data = self::dataAccess()->fetchAll($sql);
				
				$result = "";
				foreach ($data as $lang) {
					$this->localizableName = "$name:" . $lang["id"];
					$this->localizableLangId = $lang["id"];
					$this->localizableLangName = $lang["name"];
					$this->localizableLangUrl = $lang["language"];
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
		
		public function getLocalizableLangUrl() {
			return $this->localizableLangUrl;
		}
	}

?>