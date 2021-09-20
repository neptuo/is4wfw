<?php

	require_once("BaseTagLib.class.php");

	class BootstrapUi extends BaseTagLib {

		private $areResourcesIncluded = false;
		private $lastId = 0;

		public function __construct() {
			parent::setLocalizationBundle("bootstrapui");
		}

		public function resources() {
			if (!$this->areResourcesIncluded) {
				parent::js()->addjQuery("3.5.1");
				parent::js()->addScript("~/js/bootstrap/popper.min.js");
				parent::js()->addScript("~/js/bootstrap/bootstrap.min.js");
				parent::js()->addStyle("~/css/bootstrap/bootstrap.min.css");
			}
		}

		public function container($template, $fluid = false, $params = array()) {
			if ($fluid) {
				$params = $this->appendClass($params, "container-fluid");
			} else {
				$params = $this->appendClass($params, "container");
			}

			$attributes = parent::joinAttributes($params);
			$content = $template();
			return "<div$attributes>$content</div>";
		}

		public function row($template, $horizontal = "", $vertical = "", $params = array()) {
			$params = $this->appendClass($params, "row");
			if ($horizontal == "left") {
				$params = $this->appendClass($params, "justify-content-start");
			} else if ($horizontal == "center") {
				$params = $this->appendClass($params, "justify-content-center");
			} else if ($horizontal == "right") {
				$params = $this->appendClass($params, "justify-content-end");
			}

			if ($vertical == "top") {
				$params = $this->appendClass($params, "align-items-start");
			} else if ($vertical == "center") {
				$params = $this->appendClass($params, "align-items-center");
			} else if ($vertical == "bottom") {
				$params = $this->appendClass($params, "align-items-end");
			}

			$attributes = parent::joinAttributes($params);
			$content = $template();
			return "<div$attributes>$content</div>";
		}
		
		public function column($template, $default = "", $small = "", $medium = "", $large = "", $extraLarge = "", $params = array()) {
			$hasColumn = false;
			if ($default != "") {
				$params = $this->appendClass($params, "col-$default");
				$hasColumn = true;
			}
			if ($small != "") {
				$params = $this->appendClass($params, "col-sm-$small");
				$hasColumn = true;
			}
			if ($medium != "") {
				$params = $this->appendClass($params, "col-md-$medium");
				$hasColumn = true;
			}
			if ($large != "") {
				$params = $this->appendClass($params, "col-lg-$large");
				$hasColumn = true;
			}
			if ($extraLarge != "") {
				$params = $this->appendClass($params, "col-xl-$extraLarge");
				$hasColumn = true;
			}
			
			if (!$hasColumn) {
				$params = $this->appendClass($params, "col");
			}
			
			$attributes = parent::joinAttributes($params);
			$content = $template();
			return "<div$attributes>$content</div>";
		}

		private function getTagHtml($params, $defaultTag, $defaultClass = "") {
			$html = "";

			if ($defaultClass != "") {
				$params = $this->appendClass($params, $defaultClass);
			}

			if (array_key_exists("", $params)) {
				$text = $params[""];
				unset($params[""]);

				$tag = $defaultTag;
				if (array_key_exists("tag", $params)) {
					$tag = $params["tag"];
					unset($params["tag"]);
				}

				$attributes = parent::joinAttributes($params);
				$html = "<$tag$attributes>$text</$tag>";
			}

			return $html;
		}

		public function alert($template, $header = array(), $color = "primary", $isDismissible = false, $params = array()) {
			$headerHtml = $this->getTagHtml($header, "h4", "alert-heading");

			$params["role"] = "alert";
			$params = $this->appendClass($params, "alert");
			$params = $this->appendClass($params, "alert-$color");
			$dismissHtml = "";
			if ($isDismissible) {
				$params = $this->appendClass($params, "alert-dismissible fade show");
				$dismissHtml = ""
				. "<button type='button' class='close' data-dismiss='alert'>"
					. "<span aria-hidden='true'>&times;</span>"
			  	. "</button>";
			}

			$attributes = parent::joinAttributes($params);
			$content = $template();
			return "<div$attributes>$headerHtml$content$dismissHtml</div>";
		}

		public function buttonFullTag($template, $color = "primary", $isOutline = false, $size = "", $isBlock = false, $isActive = false, $params = []) {
			$content = $template();
			return $this->button($content, $color, $isOutline, $size, $isBlock, $isActive, $params);
		}

		public function button($text, $color = "primary", $isOutline = false, $size = "", $isBlock = false, $isActive = false, $params = []) {
			$params = $this->appendClass($params, "btn");
			$outline = "";
			if ($isOutline) {
				$outline = "-outline";
			}

			$params = $this->appendClass($params, "btn");
			$params = $this->appendClass($params, "btn$outline-$color");

			if ($size == "large") {
				$params = $this->appendClass($params, "btn-lg");
			} else if ($size == "small") {
				$params = $this->appendClass($params, "btn-sm");
			} else if (!empty($size)) {
				$params = $this->appendClass($params, "btn-$size");
			}

			if ($isBlock) {
				$params = $this->appendClass($params, "btn-block");
			}
			
			if ($isActive) {
				$params = $this->appendClass($params, "active");
			}

			$attributes = parent::joinAttributes($params);
			return "<button$attributes>$text</button>";
		}

		public function card($template, $header = array(), $title = array(), $params = array()) {
			$headerHtml = $this->getTagHtml($header, "div", "card-header");
			$titleHtml = $this->getTagHtml($title, "h5", "card-title");

			$params = $this->appendClass($params, "card");
			$attributes = parent::joinAttributes($params);
			$content = $template();
			return "<div$attributes>$headerHtml<div class='card-body'>$titleHtml$content</div></div>";
		}

		private function newId() {
			$this->lastId++;
			return "bs-" . $this->lastId;
		}

		public function formGroup($template, $label = array(), $field = "", $fieldCssClass = "form-control", $params = array()) {
			$labelId = "";
			if (array_key_exists("for", $label)) {
				$labelId = $label["for"];
			} else {
				$labelId = $this->newId();
				$label["for"] = $labelId;
			}

			$labelHtml = $this->getTagHtml($label, "label");
			
			parent::ui()->pushId($labelId);

			if ($field) {
				$template = function() use($template, $field, $fieldCssClass) { return $this->fieldValidator($template, $field, $fieldCssClass); };
			}

			$params = $this->appendClass($params, "form-group");
			$attributes = parent::joinAttributes($params);
			$content = $template();
			$result = "<div$attributes>$labelHtml$content</div>";

			parent::ui()->popId($labelId);
			return $result;
		}

		private $navTag = null;

		public function nav($template, $tag = "ul", $mode = "", $fill = false, $params = array()) {
			$oldNavTag = $this->navTag;
			$this->navTag = $tag;

			$params = $this->appendClass($params, "nav");

			if ($mode == "pills") {
				$params = $this->appendClass($params, "nav-pills");
			} else if ($mode == "tabs") {
				$params = $this->appendClass($params, "nav-tabs");
			}

			if ($fill) {
				$params = $this->appendClass($params, "nav-fill");
			}

			$attributes = parent::joinAttributes($params);
			$content = $template();
			$result = "<$tag$attributes>$content</$tag>";

			$this->navTag = $oldNavTag;
			return $result;
		}

		public function navItem($text, $url, $isActive = false, $isDisabled = false, $aParams = array(), $params = array()) {
			return $this->navItemFullTag(function() use($text) { return $text; }, $url, $isActive, $isDisabled, $aParams, $params);
		}

		public function navItemFullTag($template, $url, $isActive = false, $isDisabled = false, $aParams = array(), $params = array()) {
			$params = $this->appendClass($params, "nav-item");
			if ($isActive) {
				$params = $this->appendClass($params, "active");
			}

			$attributes = parent::joinAttributes($params);

			$linkClass = "nav-link";
			if ($isDisabled) {
				$linkClass .= " disabled";
			}

			$aParams = $this->appendClass($aParams, $linkClass);
			if ($isActive) {
				$aParams = $this->appendClass($aParams, "active");
			}

			$aAttributes = parent::joinAttributes($aParams);

			$result = "";
			if ($this->navTag == "ul") {
				$result .= "<li$attributes>";
			}

			$text = $template();
			$result .=  "<a href='$url'$aAttributes>$text</a>";

			if ($this->navTag == "ul") {
				$result .= "</li>";
			}

			return $result;
		}

		private $fieldValidatorCssClass;

		public function fieldValidator(callable $template, string $name, string $cssClass = "") {
			$model = parent::getEditModel();
			$isValid = empty($model->validationMessage($name));

			if (!$isValid) {
				if ($cssClass != "") {
					$cssClass .= " ";
				}

				$cssClass .= "is-invalid";
			}

			$oldValue = $this->fieldValidatorCssClass;
			$this->fieldValidatorCssClass = $cssClass;

			$result = $template();
			$result .= $this->fieldValidationMessage($name);
			

			$this->fieldValidatorCssClass = $oldValue;
			return $result;
		}

		public function fieldValidationMessage(string $name) {
			$templateContent = '
			<val:message key="' . $name . '">
				<ui:any items="val:messageList">
					<div class="invalid-feedback">
						<web:getProperty name="val:messageText" />.
					</div>
				</ui:any>
			</val:message>
			';

			$keys = ["bs", "validation", sha1($templateContent)];
			$template = $this->getParsedTemplate($keys);
			if ($template == null) {
				$template = $this->parseTemplate($keys, $templateContent);
			}

			return $template();
		}

		public function getFieldValidatorCssClass() {
			return $this->fieldValidatorCssClass;
		}

		public function paging($template, $size, $params = []) {
			$params = $this->appendClass($params, "pagination");
			
			if ($size == "large") {
				$params = $this->appendClass($params, "pagination-lg");
			} else if ($size == "small") {
				$params = $this->appendClass($params, "pagination-sm");
			} else if (!empty($size)) {
				$params = $this->appendClass($params, "pagination-$size");
			}
			
			$attributes = parent::joinAttributes($params);
			$content = $template();
			return "<nav><ul$attributes>$content</ul></nav>";
		}
		
		public function pageLink($text, $url, $isEnabled, $isActive, $aParams = [], $params = []) {
			return $this->pageLinkFullTag(function() use ($text) { return $text; }, $url, $isEnabled, $isActive, $aParams, $params);
		}

		public function pageLinkFullTag($template, $url, $isEnabled, $isActive, $aParams = [], $params = []) {
			$params = $this->appendClass($params, "page-item");
			
			if (!$isEnabled) {
				$params = $this->appendClass($params, "disabled");
				$url = "#";
			}
			
			if ($isActive) {
				$params = $this->appendClass($params, "active");
			}
			
			$aParams = $this->appendClass($aParams, "page-link");
			$aParams["href"] = $url;

			$attributes = parent::joinAttributes($params);
			$aAttributes = parent::joinAttributes($aParams);
			
			$content = $template();
			return "<li$attributes><a$aAttributes>$content</a></li>";
		}
	}

?>