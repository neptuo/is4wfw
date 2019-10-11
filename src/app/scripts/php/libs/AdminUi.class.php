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
	}

?>