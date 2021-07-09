<?php

	require_once("BaseTagLib.class.php");
    require_once("Session.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FileUploadModel.class.php");

	class Ui extends BaseTagLib {

		private $id;

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

		private $forEachIndex;

		public function forEachListModel($template, $model, $filter = []) {
			$this->pushListModel($model);
			$result = "";

			if ($model->isRegistration()) {
				$template();
			}
			
			if ($model->isRender()) {
				$prevIndex = $this->forEachIndex;
				$this->forEachIndex = 0;
				foreach ($model->items() as $item) {
					if ($this->isPassedByWhere($item, $filter)) {
						$model->data($item);
						$result .= $template();
						$this->forEachIndex++;
					}
				}
				$this->forEachIndex = $prevIndex;
			}

			$this->popListModel();
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
				$template();
			}
			
			if ($model->isRender()) {
				$items = $model->items();
				if (count($items) > 0) {
					$item = $items[$indexGetter($items)];
					$model->data($item);
					$result .= $template();
				}
			}

			self::popListModel();
			return $result;
		}

		public function getForEachIndex() {
			return $this->forEachIndex;
		}

		public function firstListModel($template, $model) {
			return self::singleListModel($template, $model, function($items) { return 0; });
		}

		public function lastListModel($template, $model) {
			return self::singleListModel($template, $model, function($items) { return count($items) - 1; });
		}

		public function emptyListModel($template, $model) {
			if ($model->isRegistration()) {
				$template();
			}
			
			if ($model->isRender()) {
				if (count($model->items()) == 0) {
					return $template();
				}
			}
		}

		public function anyListModel($template, $model) {
			if ($model->isRegistration()) {
				$template();
			}
			
			if ($model->isRender()) {
				if (count($model->items()) > 0) {
					return $template();
				}
			}
		}

		private $countListModel;

		public function countListModel($template, $model) {
			if ($model->isRegistration()) {
				$template();
			}
			
			if ($model->isRender()) {
				$prevCount = $this->countListModel;
				$this->countListModel = count($model->items());
				$result = $template();
				$this->countListModel = $prevCount;
				return $result;
			}
		}

		public function countListModelSelfClosing($model) {
			if ($model->isRender()) {
				return count($model->items());
			}
		}

		public function getCountListModel() {
			return $this->countListModel;
		}

		// ------- GRID -------------------------------------------------------

		private $gridPhase = "";

		public function grid($template, $model, $thead = array(), $tbody = array(), $params = array()) {
			if ($model->isRender()) {
				$result = "";
				$isWellStructured = count($thead) > 0 || count($tbody) > 0;

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
					$result .= $template();
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
						$result .= $template();
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
				return "<td$tdAttributes>" . $template() . "</td>";
			}

			return "";
		}



		// ------- EDITORS ----------------------------------------------------

		private $isInsideForm = false;

		public function form(callable $template, $method = "post", $pageId = null, $isEditable = true, $params = array()) {
			$beforeBody = '';
			$afterBody = '';

			if (!$isEditable) {
				$beforeBody .= '<fieldset disabled="disabled">';
				$afterBody .= '</fieldset>';
			}

			if ($this->isInsideForm) {
				return $beforeBody . $template() . $afterBody;
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

				$result = "<form$attributes>$beforeBody" . $template() . "$afterBody</form>";
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

		public function filter($template, $session = "", $pageId = "") {
            $model = parent::getEditModel();
			$session = explode(",", $session);

			if ($model->isSubmit()) {
				$model->request($_POST);
				$template();
			}

			if ($model->isSave()) {
				if ($pageId == "") {
					$url = $_SERVER['REQUEST_URI'];
				} else {
					$url = parent::web()->composeUrl($pageId);
				}

                foreach ($model as $key => $value) {
					if (is_callable($value)) {
						$value = $value();
					}

					if (is_array($value)) {
						$value = implode(",", $value);
					}

					if (!empty($value)) {
						if (in_array($key, $session)) {
							parent::session()->set($key, $value, Session::StorageKey);
						} else {
							$url = UrlUtils::addParameter($url, $key, $value);
						}
					} else {
						if (in_array($key, $session)) {
							parent::session()->delete($key, Session::StorageKey);
						} else {
							$url = UrlUtils::removeParameter($url, $key);
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

				$result = $template();
				return $result;
			}
		}

		public function editable($template, $is) {
			$model = $this->getEditModel();
			$modelEditabled = $model->editable();

			$result = "";
			if ($is === false) {
				$result .= '<fieldset disabled="disabled">';
				$model->editable(false);
			} else if ($model->editable()) {
				throw new Exception("Unnable to enable form part when disabled in parent.");
			}
			
			$result .= $template();
			
			if ($is === false) {
				$result .= '</fieldset>';
				$model->editable($modelEditabled);
			}

			return $result;
		}
		
		public function dropdownlist($name, $nameIndex = -1, $source, $display, $id, $emptyText = "", $mode = "single", $params = array()) {
			if ($mode == "multi" && $nameIndex != -1) {
				throw new ParameterException("nameIndex", "In mode=multi nameIndex is not supported.");
			}

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

				if ($mode == "multi") {
					$formName .= "[]";
					$params["multiple"] = "multiple";

					if (!is_array($modelValue)) {
						$modelValue = explode(",", $modelValue);
					}
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
					if (is_array($modelValue)) {
						$isSelected = in_array($itemValue, $modelValue);
					} else {
						$isSelected = $modelValue == $itemValue;
					}

					$result .= "<option value='$itemValue'" . ($isSelected ? " selected='selected'" : "") . ">$item[$display]</option>";
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
					. "<$itemContainerTagName$attributes>"
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

		public function rangebox($name, $nameIndex = -1, $default = "", $params = array()) {
			return self::inputbox("range", $name, $nameIndex, $default, $params);
		}

		public function filebox($name, $isMulti = false, $params = array()) {
			$model = parent::getEditModel();

			$formParams = $model->metadata("form");
			$formParams["enctype"] = "multipart/form-data";
			$model->metadata("form", $formParams);

			if ($model->isSubmit()) {
				$value = $_FILES[$name];
				$file = null;
				if ($value != null) {
					if ($isMulti) {
						$file = [];
						for ($i = 0; $i < count($value["name"]); $i++) { 
							$item = new FileUploadModel();
							$item->Name = $value["name"][$i];
							$item->Type = $value["type"][$i];
							$item->TempName = $value["tmp_name"][$i];
							$item->Size = $value["size"][$i];
							$file[] = $item;
						}
					} else {
						$file = new FileUploadModel();
						$file->Name = $value["name"];
						$file->Type = $value["type"];
						$file->TempName = $value["tmp_name"];
						$file->Size = $value["size"];
					}
				}

				$model->set($name, null, $file);
			} else {
				$nameIndex = -1;
				if ($isMulti) {
					$params["multiple"] = "multiple";
					$nameIndex = 0;
				}

				return $this->inputbox("file", $name, $nameIndex, "", $params);
			}
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
				$model[$name] = function() use ($model, $name, $format) { 
					$items = [];
					foreach ($model as $key => $value) {
						if ($name != $key) {
							if (is_callable($value)) {
								$value = $value();
							}

							$items[$key] = $value;
						}
					}

					return StringUtils::format($format, $items); 
				};
            }
		}

        public function defaultValue($template, $name, $format) {
            $model = parent::getEditModel();
            if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

            if ($model->isSubmit()) {
				$template();
				self::ensureModelDefaultValue($model, $name, $format);
            }

            if ($model->isRender()) {
                return $template();
            }
        }

        public function toUpperValue($template, $name) {
            $model = parent::getEditModel();
            if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

            if ($model->isSubmit()) {
				$template();
				$value = $model[$name];
				$model[$name] = function() use ($value) { 
					if (is_callable($value)) {
						$value = $value();
					}

					return strtoupper($value); 
				};
            }

            if ($model->isRender()) {
                return $template();
            }
        }

        public function toLowerValue($template, $name) {
            $model = parent::getEditModel();
            if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

            if ($model->isSubmit()) {
				$template();
				$value = $model[$name];
				$model[$name] = function() use ($value) { 
					if (is_callable($value)) {
						$value = $value();
					}

					return strtolower($value); 
				};
            }

            if ($model->isRender()) {
                return $template();
            }
        }

        public function toUrlValue($template, $name) {
            $model = parent::getEditModel();
            if ($model->isRegistration()) {
				$model->set($name, null, null);
			}

            if ($model->isSubmit()) {
				$template();
				$value = $model[$name];
				$model[$name] = function() use ($value) { 
					if (is_callable($value)) {
						$value = $value();
					}

					return UrlUtils::toValidUrl($value); 
				};
            }

            if ($model->isRender()) {
                return $template();
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
				$template();
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

				return $template();
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
		
		public function formatNumber($value, $thousandsSeparator = "", $decimalsSeparator = "", $decimals = "") {
			if ($thousandsSeparator) {
				if ($decimals == "0") {
					$decimals = 0;
				} else if (!$decimals) {
					$decimals = 2;
				}

				$value = number_format($value, $decimals, $decimalsSeparator, $thousandsSeparator);
			} else if (is_numeric($decimals)) {
				$value = round($value, $decimals);
			}

			return $value;
		}

		public function formatNumberEditor($template, $name, $nameIndex, $thousandsSeparator = "", $decimalsSeparator = "", $decimals = "") {
			$model = parent::getEditModel();
            if ($model->isRegistration()) {
				$template();
			}
			
            if ($model->isSubmit()) {
				$modelValue = $model->request($name, $nameIndex);
				$modelValue = str_replace($thousandsSeparator, "", $modelValue);
				$modelValue = str_replace($decimalsSeparator, ".", $modelValue);
				$model->set($name, $nameIndex, $modelValue);
            }

            if ($model->isRender()) {
				$modelValue = $model->get($name, $nameIndex);
				$modelValue = $this->formatNumber($modelValue, $thousandsSeparator, $decimalsSeparator, $decimals);
				$model->set($name, $nameIndex, $modelValue);
				return $template();
            }
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
					$template();
				}
			}
			
            if ($model->isSubmit()) {
				$langIds = self::ensureLangIds($langIds);
				foreach ($langIds as $langId) {
					$this->localizableName = "$name:$langId";
					$template();
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
					$result .= $template();
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