<?php

	require_once("BaseTagLib.class.php");

	class BootstrapUi extends BaseTagLib {

		private $areResourcesIncluded = false;
		private $lastId = 0;

		public function __construct() {
			parent::setTagLibXml("BootstrapUi.xml");
			parent::setLocalizationBundle("bootstrapui");
		}

		private function addScript($virtualPath) {
			$script = parent::js()->formatScript($virtualPath);
			if ($script != null) {
				parent::web()->addScript($script);
			}
		}

		private function addStyle($virtualPath) {
			$script = parent::js()->formatStyle($virtualPath);
			if ($script != null) {
				parent::web()->addStyle($script);
			}
		}

		public function resources() {
			if (!$this->areResourcesIncluded) {
				self::addScript("~/js/bootstrap/jquery-3.2.1.slim.min.js");
				self::addScript("~/js/bootstrap/popper.min.js");
				self::addScript("~/js/bootstrap/bootstrap.min.js");
				self::addStyle("~/css/bootstrap/bootstrap.min.css");
			}
		}

		private function appendClass($attributes, $class) {
			if (array_key_exists("class", $attributes)) {
				$attributes["class"] .= " $class";
			} else {
				$attributes["class"] = $class;
			}

			return $attributes;
		}

		public function container($template, $fluid = false, $params = array()) {
			if ($fluid) {
				$params = self::appendClass($params, "container-fluid");
			} else {
				$params = self::appendClass($params, "container");
			}

			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			return "<div$attributes>$content</div>";
		}

		public function row($template, $params = array()) {
			$params = self::appendClass($params, "row");
			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			return "<div$attributes>$content</div>";
		}
		
		public function column($template, $default = "", $small = "", $medium = "", $large = "", $extraLarge = "", $params = array()) {
			$hasColumn = false;
			if ($default != "") {
				$params = self::appendClass($params, "col-$default");
				$hasColumn = true;
			}
			if ($small != "") {
				$params = self::appendClass($params, "col-sm-$small");
				$hasColumn = true;
			}
			if ($medium != "") {
				$params = self::appendClass($params, "col-md-$medium");
				$hasColumn = true;
			}
			if ($large != "") {
				$params = self::appendClass($params, "col-lg-$large");
				$hasColumn = true;
			}
			if ($extraLarge != "") {
				$params = self::appendClass($params, "col-xl-$extraLarge");
				$hasColumn = true;
			}
			
			if (!$hasColumn) {
				$params = self::appendClass($params, "col");
			}
			
			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			return "<div$attributes>$content</div>";
		}

		private function getTagHtml($params, $defaultTag, $defaultClass = "") {
			$html = "";

			if ($defaultClass != "") {
				$params = self::appendClass($params, $defaultClass);
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

		public function card($template, $header = array(), $title = array(), $params = array()) {
			$headerHtml = self::getTagHtml($header, "div", "card-header");
			$titleHtml = self::getTagHtml($title, "h5", "card-title");

			$params = self::appendClass($params, "card");
			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			return "<div$attributes>$headerHtml<div class='card-body'>$titleHtml$content</div></div>";
		}

		private function newId() {
			$this->lastId++;
			return "bs-" . $this->lastId;
		}

		public function formGroup($template, $label = array(), $params = array()) {
			$labelId = "";
			if (array_key_exists("for", $label)) {
				$labelId = $label["for"];
			} else {
				$labelId = self::newId();
				$label["for"] = $labelId;
			}

			$labelHtml = self::getTagHtml($label, "label");
			
			parent::ui()->pushId($labelId);

			$params = self::appendClass($params, "form-group");
			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			$result = "<div$attributes>$labelHtml$content</div>";

			parent::ui()->popId($labelId);
			return $result;
		}

		public function nav($template, $params = array()) {
			$params = self::appendClass($params, "nav");
			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			$result = "<div$attributes>$content</div>";
			return $result;
		}

		public function navItem($text, $url, $isActive = false, $isDisabled = false, $params = array()) {
			$params = self::appendClass($params, "nav-item");
			if ($isActive) {
				$params = self::appendClass($params, "active");
			}

			$attributes = parent::joinAttributes($params);

			$linkClass = "nav-link";
			if ($isDisabled) {
				$linkClass .= " disabled";
			}

			$result = ""
			. "<li$attributes>"
				. "<a href='$url' class='$linkClass'>$text</a>"
			. "</li>";
			return $result;
		}
	}

?>