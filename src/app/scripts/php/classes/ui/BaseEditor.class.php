<?php

	class BaseEditor {

		public static function monaco($name, $content, $language = "html") {
			BaseTagLib::js()->addScript("https://unpkg.com/monaco-editor@0.34.0/min/vs/loader.js");

			return ''
			. '<div id="' . $name . '" class="monaco-editor" data-theme="' . BaseTagLib::system()->getPropertyValue('Page.monacoTheme', 'vs') . '" data-language="' . $language . '" style="height: ' . BaseTagLib::system()->getPropertyValue('Page.monacoHeight', 600) . 'px;">'
				. '<textarea name="' . $name . '" class="d-none">' . str_replace('~', '&#126', $content) . '</textarea>'
			. '</div>';
		}
		
	}

?>
