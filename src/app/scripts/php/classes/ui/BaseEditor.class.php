<?php

	class BaseEditor {

		public static function monaco($name, $content) {
			return ''
			. '<div id="' . $name . '" class="monaco-editor" data-theme="' . BaseTagLib::system()->getPropertyValue('Page.monacoTheme', 'vs') . '" style="height: ' . BaseTagLib::system()->getPropertyValue('Page.monacoHeight', 600) . 'px;">'
				. '<textarea name="' . $name . '" class="d-none">' . str_replace('~', '&#126', $content) . '</textarea>'
			. '</div>';
		}
		
	}

?>
