<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

	class AdminUi extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("AdminUi.xml");
		}

		public function deleteButton($hiddenField, $confirmValue = null, $params = array()) {
			$hiddens = parent::findAttributesByPrefix($params, "hidden-");
			$hiddens[$hiddenField] = $hiddenField;

			$template = "";
			$template .= parent::ui()->inputImage("~/images/page_del.png", array("class" => "confirm", "title" => "Delete \"$confirmValue\""));
			foreach ($hiddens as $hiddenName => $hiddenValue) {
				$template .= parent::ui()->inputHidden($hiddenName, $hiddenValue);
			}

			return parent::ui()->form($template);
		}

		public function newButton($pageId, $text, $params = array()) {
			$params["id"] = "new";
			return parent::web()->makeAnchor($pageId, $text, false, "button", "", "", "", "", "", $params);
		}

		public function field($template, $label) {
			$labelText = $label[""];
			if (!parent::endsWith($labelText, ":")) {
				$labelText .= ":";
			}

			unset($label[""]);
			$labelAttributes = parent::joinAttributes($label);

			$result = ""
			. "<div class='gray-box'>"
				. "<label>"
					. "<span$labelAttributes>"
						. $labelText
					. "</span>"
					. self::parseContent($template)
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

			$result = self::parseContent($template);

			$this->edit = $prev;
			return $result;
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

?>