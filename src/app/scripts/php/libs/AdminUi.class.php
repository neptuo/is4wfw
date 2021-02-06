<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

	class AdminUi extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("AdminUi.xml");
			parent::setLocalizationBundle("adminui");
		}

		public function setSuccessMessage($message) {
			if ($message != "") {
				parent::autolib("var")->setValue("admin-message", $message, "temp");
			}
		}

		public function deleteButton($hiddenField, $confirmValue = null, $hiddens = [], $params = array()) {
			$hiddens[$hiddenField] = $hiddenField;

			$template = "";
			$template .= parent::ui()->inputImage("~/images/page_del.png", array("class" => "confirm", "title" => "Delete \"$confirmValue\""));
			foreach ($hiddens as $hiddenName => $hiddenValue) {
				$template .= parent::ui()->inputHidden($hiddenName, $hiddenValue);
			}

			return parent::ui()->form(function() use ($template) { return $template; });
		}

		public function newButton($pageId, $text, $paramName = "id", $param = array()) {
			$param[$paramName] = "new";
			return parent::web()->makeAnchor($pageId, $text, false, "button", "", "", "", "", "", $param);
		}

		public function saveButtons($saveName = "save", $saveParam = array(), $closePageId = "", $closeParam = array(), $message = "") {
			$model = parent::getEditModel();
			if ($model->isSaved()) {
				$this->setSuccessMessage($message);
				self::redirectAfterSave($saveName, $saveParam, $closePageId, $closeParam);
			}
			
			if ($model->isRender()) {
				$saveText = parent::rb("buttons.save");
				$saveCloseText = parent::rb("buttons.saveclose");
				$closeText = parent::rb("buttons.close");

				$result = ""
				. "<button name='$saveName' value='save'>"
					. $saveText
				. "</button> "
				. "<button name='$saveName' value='save-close'>"
					. $saveCloseText
				. "</button> "
				. parent::web()->makeAnchor($closePageId, $closeText, false, "button", "", "", "", "", "", $closeParam);

				return $result;
			}
		}

		public function redirectAfterSave($saveName = "save", $saveParam = array(), $closePageId = "", $closeParam = array())
		{
			if (array_key_exists($saveName, $_POST)) {
				$pageId = null;
				$param = null;
				if ($_POST[$saveName] == "save") {
					$pageId = parent::web()->getLastPageId();
					if ($pageId == null) {
						$pageId = parent::autolib("v")->getCurrentVirtualUrl();
					}
					$param = $saveParam;
				} else if ($_POST[$saveName] == "save-close") {
					$pageId = $closePageId;
					$param = $closeParam;
				}

				if ($pageId != null) {
					if ($pageId == "") {
						$pageId = parent::web()->getLastPageId();
					}

					parent::web()->redirectTo($pageId, false, false, false, false, $param);
				}
			}
		}

		public function field($template, $label, $params = array()) {
			$labelText = $label[""];
			if (!StringUtils::endsWith($labelText, ":")) {
				$labelText .= ":";
			}

			unset($label[""]);
			$labelAttributes = parent::joinAttributes($label);

			if (array_key_exists("class", $params)) {
				$params["class"] .= " gray-box";
			} else {
				$params["class"] = "gray-box";
			}

			$divAttributes = parent::joinAttributes($params);

			$result = ""
			. "<div$divAttributes>"
				. "<label>"
					. "<span$labelAttributes>"
						. $labelText
					. "</span>"
					. $template()
				. "</label>"
			."</div>";

			return $result;
		}

		private $edit = array();

		public function edit($template, $id) {
			$prev = $this->edit;
			$this->edit = array();

			if ($id === "") {
				$this->edit["isEdit"] = false;
				$this->edit["title"] = "";
				return;
			}

			if (!is_numeric($id)) {
				$id = 0;
			}

			$this->edit["id"] = $id;
			$this->edit["isEdit"] = true;
			if ($id == 0) {
				$this->edit["title"] = "Create";
			} else {
				$this->edit["title"] = "Edit";
			}

			$result = $template();

			$this->edit = $prev;
			return $result;
		}

		public function validation($key) {
			$templateContent = '
			<val:message key="' . $key . '">
				<span class="red">
					<strong>!</strong>
					<web:getProperty name="val:messageText" />.
				</span>
			</val:message>
			';

			$keys = ["adminui", "validation", sha1($templateContent)];
			$template = $this->getParsedTemplate($keys);
			if ($template == null) {
				$template = $this->parseTemplate($keys, $templateContent);
			}

			return $template();
		}

		public function successMessage() {
			$templateContent = '
			<var:use name="admin-message" scope="temp" />
			<web:condition when="var:admin-message">
				<h4 class="success">
					<web:getProperty name="var:admin-message" />
					<var:clear name="admin-message" />
				</h4>
			</web:condition>
			';
			
			$keys = ["adminui", "successMessage", sha1($templateContent)];
			$template = $this->getParsedTemplate($keys);
			if ($template == null) {
				$template = $this->parseTemplate($keys, $templateContent);
			}

			return $template();
		}

		public function isEdit() {
			return $this->edit["isEdit"] == true;
		}

		public function isNew() {
			return self::isEdit() && $this->edit["id"] == 0;
		}
		
		public function getEditId() {
			return $this->edit["id"];
		}
		
		public function getEditTitle() {
			return $this->edit["title"];
		}
	}
