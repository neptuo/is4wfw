<?php

	class BaseEditor {

		public static function monaco($name, $content, $language = "html") {
			BaseTagLib::js()->addScript("https://unpkg.com/monaco-editor@0.34.0/min/vs/loader.js");

			return ''
			. '<div id="' . $name . '" class="monaco-container" data-theme="' . BaseTagLib::system()->getPropertyValue('Page.monacoTheme', 'vs') . '" data-language="' . $language . '" style="height: ' . BaseTagLib::system()->getPropertyValue('Page.monacoHeight', 600) . 'px;">'
				. '<textarea name="' . $name . '" class="d-none">' . str_replace('~', '&#126', $content) . '</textarea>'
			. '</div>';
		}

		public static function monacoList($id, $editors, $language = "html") {
			BaseTagLib::js()->addScript("https://unpkg.com/monaco-editor@0.34.0/min/vs/loader.js");

			$result = '<div class="mb-2">';

			foreach ($editors as $editor) {
				$result .= ''
				. '<button type="button" data-editor="' . $editor['name'] . '">' . $editor['title'] . '</button> '
				. '<textarea name="' . $editor['name'] . '" class="d-none">' . str_replace('~', '&#126', $editor['content']) . '</textarea>';
			}

			$result .= '</div>';

			return ''
			. '<div id="' . $id . '" class="monaco-container monaco-container-list" data-theme="' . BaseTagLib::system()->getPropertyValue('Page.monacoTheme', 'vs') . '" data-language="' . $language . '">'
				. $result
				. '<div class="monaco-container-target" style="height: ' . BaseTagLib::system()->getPropertyValue('Page.monacoHeight', 600) . 'px;"></div>'
			. '</div>';
		}
		
	}

?>
