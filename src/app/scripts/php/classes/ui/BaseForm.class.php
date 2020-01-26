<?php
	
	class BaseForm {
		
		private $fields = array();
		
		private $buttons = array();
		
		private $formAttrs = array();
		
		private $token = '';
		
		public function __construct() {
			$this->token = md5(rand(1000, 1000000).rand(1000, 1000000));
		}
		
		public function setFormAttrs($name, $method, $action, $className = "") {
			$this->formAttrs['method'] = $method;
			$this->formAttrs['action'] = $action;
			$this->formAttrs['name'] = $name;

			if ($className != "") {
				$this->formAttrs['class'] = $className;
			}
		}
		
		/**
		 *
		 *	Adds row to form
		 *	
		 *	@param	type				type of field: text|password|textarea|hidden|singlecheckbox	 		 
		 *
		 */		 		 		 		
		public function addField($type, $name, $label, $value, $labelClassName = "", $fieldClassName = "") {
			$this->fields[] = array('name' => $name, 'type' => $type, 'label' => $label, 'value' => $value, 'labelClassName' => $labelClassName, 'fieldClassName' => $fieldClassName);
		}
		
		/**
		 *
		 *	Adds dropdown
		 *	
		 *	@param	items				2D array of "key"=>"value" pairs, "key" is used in option tag as value, "value" is used as option tag content
		 *	@param	selected		matches one of keys in items		 		 		 		 
		 *
		 */		 		 		
		public function addDropDown($name, $label, $items, $selected, $labelClassName, $fieldClassName) {
			$this->fields[] = array('name' => $name, 'type' => 'dropdown', 'label' => $label, 'items' => $items, 'selected' => $selected, 'labelClassName' => $labelClassName, 'fieldClassName' => $fieldClassName);
		}
		
		/**
		 *
		 *	Not yet implemented
		 *		 
		 */
		public function addCheckboxSet($name, $labels, $values, $selected, $labelClassName, $fieldClassName) {
			$this->fields[] = array('name' => $name, 'type' => 'checkboxset', 'labels' => $labels, 'values' => $values, 'selected' => $selected, 'labelClassName' => $labelClassName, 'fieldClassName' => $fieldClassName);
		}
		
		/**
		 *
		 *	Not yet implemented
		 *		 
		 */		 		 		
		public function addRadioSet($name, $labels, $values, $selected, $labelClassName, $fieldClassName) {
			$this->fields[] = array('name' => $name, 'type' => 'radioset', 'labels' => $labels, 'values' => $values, 'selected' => $selected, 'labelClassName' => $labelClassName, 'fieldClassName' => $fieldClassName);
		}
		
		/**
		 *
		 *	Adds submit button		 
		 *
		 */		 		 		
		public function addSubmit($name, $value, $className = "") {
			$this->buttons[] = array('type' => 'submit', 'name' => $name, 'value' => $value, 'class' => $className);
		}
		
		/**
		 *
		 *	Returns html form
		 *
		 */		 		 		 		
		public function render() {
			$_SESSION['base-form'][$this->formAttrs['name']] = $this->token;
			$return = ''
			.'<form name="'.$this->formAttrs['name'].'" method="'.$this->formAttrs['method'].'" action="'.$this->formAttrs['action'].'" class="'.$this->formAttrs['class'].'">'
				.'<input type="hidden" name="'.$this->formAttrs['name'].'-token" value="'.$this->token.'" />';
				
			foreach($this->fields as $field) {
				$field['id'] = 'id-'.$this->formAttrs['name'].'-'.$field['name'];
				
				if($field['type'] != 'hidden') {
					$return .= '<div class="gray-box">';
				}
				if($field['label'] != '') {
					$return .= '<label for="'.$field['id'].'" class="'.$field['labelClassName'].'">'.$field['label'].'</label>'; 
				}
				$return .= self::renderField($field);
				if($field['type'] != 'hidden') {
					$return .= '</div>';
				}
			}
			$return .= '<div class="gray-box">';
			foreach($this->buttons as $btn) {
				$return .= '<input type="'.$btn['type'].'" name="'.$this->formAttrs['name'].'-'.$btn['name'].'" value="'.$btn['value'].'" class="'.$btn['class'].'" />';
			}
			$return .= '</div>';
			$return .= '</form>';
			
			return $return;
		}
		
		/**
		 *
		 *	Returns true if form has been submited ... 
		 *
		 */		 		 		 		
		public function isSubmited() {
			if($this->formAttrs['method'] == 'post') {
				//echo $_POST[$this->formAttrs['name'].'-token'].' == '.$_SESSION['base-form'][$this->formAttrs['name']].'<br />';
				if(array_key_exists($this->formAttrs['name'].'-token', $_POST) && $_POST[$this->formAttrs['name'].'-token'] == $_SESSION['base-form'][$this->formAttrs['name']]) {
					return true;
				} else {
					return false;
				}
			} else {
				if(array_key_exists($this->formAttrs['name'].'-token', $_GET) && $_GET[$this->formAttrs['name'].'-token'] == $_SESSION['base-form'][$this->formAttr['name']]) {
					return true;
				} else {
					return false;
				}
			} 
		}
		
		/**
		 *
		 *	Returns true if button with @param("name") was presse, false otherwise.
		 *	
		 *	@param	name				button name		 		 
		 *
		 */		 		 		 		
		public function pressed($name) {
			if($this->formAttrs['method'] == 'post') {
				return array_key_exists($this->formAttrs['name'].'-'.$name, $_POST);
			} else {
				return array_key_exists($this->formAttrs['name'].'-'.$name, $_GET);
			}
		}
		
		/***
		 *
		 *	Return value of submited field with name @param("name").
		 *
		 */		 		 		 		
		public function getValue($name) {
			if(self::isSubmited()) {
				if($this->formAttrs['method'] == 'post') {
					return $_POST[$this->formAttrs['name'].'-'.$name];
				} else {
					return $_GET[$this->formAttrs['name'].'-'.$name];
				}
			}
			return '';
		}
		
		private function renderField($field) {
			$return = '';
			switch($field['type']) {
				case 'text' : 
				case 'password' : 
				case 'hidden' : $return .= '<input type="'.$field['type'].'" name="'.$this->formAttrs['name'].'-'.$field['name'].'" value="'.$field['value'].'" id="'.$field['id'].'" class="'.$field['fieldClassName'].'" />'; break;
				case 'textarea' :
					$return .= '<textarea name="'.$this->formAttrs['name'].'-'.$field['name'].'" id="'.$field['id'].'" class="'.$fiel['fieldClassName'].'">'.$field['value'].'</textarea>';
				break; 
				case 'singlecheckbox':
					$return .= '<input type="checkbox" name="'.$this->formAttrs['name'].'-'.$field['name'].'"'.($field['value'] == true ? ' checked="checked"' : '').' id="'.$field['id'].'" class="'.$field['fieldClassName'].'" />';
				break;
				case 'dropdown' :
					$return .= '<select name="'.$this->formAttrs['name'].'-'.$field['name'].'" id="'.$field['id'].'" class="'.$fiel['fieldClassName'].'">';
					foreach($field['items'] as $item) {
						$return .= '<option value="'.$item['key'].'"'.($item['key'] == $field['selected'] ? ' selected="selected"' : '').'>'.$item['value'].'</option>';
					}
					$return .= '</select>';
				break;
			}
			
			return $return;
		}
		
	}
	
?>
