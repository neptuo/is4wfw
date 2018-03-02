<?php

	class Editor {
		
		public static function render($type, $name, $content, $rows, $class = '', $id = '', $label = '') {
			$return = '';
			$className = '';
			if($label != '') {
				$return .= '<label for="'.$id.'">'.$label.'</label>';
			}
			if($type == 'edit_area') {
				$className = 'edit-area';
			} elseif($type == 'wysiwyg') {
				$className = 'wysiwyg';
			}
			if($class != '') {
				$className .= ' '.$class;
			}
			$return .= '<textarea id="'.$id.'" class="'.$className.'" rows="'.$rows.'">'.$content.'</textarea>';
			return $return;
		}
		
	}

?>
