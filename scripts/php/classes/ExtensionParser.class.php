<?php

require_once("scripts/php/classes/ResourceBundle.class.php");

class ExtensionParser {

	protected $Result = '';
	protected $Content = '';

    protected $RE = '({([a-zA-Z0-9]*) *([^}]*)})';
	protected $ConditionRE = '(\((.*)\) (\s*))';
	protected $BindingConditionRE = '(([^ ]*) ([^ ]*)( *: *([^ ]*))*)';
	
	protected $ResourceBundle;
	protected $DataItem;
	protected $I;
	protected $GlobalI = 1;
	
	private function parseExtensions($content) {
		return preg_replace_callback($this->RE, array(&$this, 'parseextension'), $content);
	}
	
	private function parsecondition($condition) {
		//print_r($condition);
		if($this->DataItem[$condition[1]]) {
			return $condition[2];
		} else {
			return $condition[4];
		}
	}
	
	private function foreachDataItems($param, $data, $selector) {
		$content = ExtensionParser::loadView($param);
		$result = '';
		
		if($selector) {
			$parts = explode(':', $selector);
			global ${$parts[0] . "Object"};
		}
		
		foreach($data as $i => $item) {
			if($selector) {
				$use = call_user_func_array(array(${$parts[0] . "Object"}, $parts[1]), array($item));
			} else {
				$use = true;
			}
			if($use) {
				$current = $this->DataItem;
				
				$this->DataItem = $item;
				$this->GlobalI++;
				$this->I = $i;
				$result .= self::parseExtensions($content);
			
				$this->DataItem = $current;
			}
		}
		return $result;
	}
	
	private function template($param, $data, $selector) {
		$content = ExtensionParser::loadView($param);
		$result = '';
		
		if($selector) {
			$parts = explode(':', $selector);
			global ${$parts[0] . "Object"};
		}
		
		if($selector) {
			$use = call_user_func_array(array(${$parts[0] . "Object"}, $parts[1]), array($data));
		} else {
			$use = true;
		}
		if($use) {
			$current = $this->DataItem;
			
			$this->DataItem = $data;
			$this->GlobalI++;
			$this->I = $i;
			$result .= self::parseExtensions($content);
			
			$this->DataItem = $current;
		}
		
		return $result;
	}
	
	private function evalFunc($param) {
		$funcParams = array($this->DataItem);
		$params = explode(' ', $param);
		if(count($params) > 1) {
			$param = $params[0];
		}
		foreach($params as $i=>$p) {
			if($i > 0) {
				$funcParams[count($funcParams)] = $p;
			}
		}
		
		$parts = explode(':', $param);
		
		global ${$parts[0] . "Object"};
		return call_user_func_array(array(${$parts[0] . "Object"}, $parts[1]), $funcParams);
	}
	
	private function getBindingValue($param) {
		$params = explode('.', $param);
		$data = $this->DataItem;
		foreach($params as $item) {
			$data = $data[$item];
		}
	
		return $data;
	}

    private function parseextension($extension) {
        //print_r($extension);
		$name = $extension[1];
		$param = $extension[2];
		
		if($name == 'Resource') {
			return $this->ResourceBundle->get($param);
		} else if($name == 'Server') {
			return $_SERVER[$param];
		} else if($name == 'Request') {
			return $_REQUEST[$param];
		} else if($name == 'Binding') {
			return self::getBindingValue($param);
		} else if($name == 'DateBinding') {
			$params = explode(' ', $param);
			return date($params[1], self::getBindingValue($params[0]));
		} else if($name == 'BindingCondition') {
			return preg_replace_callback($this->BindingConditionRE, array(&$this, 'parsecondition'), $param);
		} else if($name == 'ForEach') {
			$params = explode(' ', $param);
			if(count($params) > 1) {
				return self::foreachDataItems($params[1], $this->DataItem[$params[0]], $params[2]);
			} else {
				return self::foreachDataItems($param, $this->DataItem);
			}
		} else if($name == 'Template') {
			$params = explode(' ', $param);
			if(count($params) > 1) {
				return self::template($params[1], $params[0] == 'this' ? $this->DataItem : $this->DataItem[$params[0]], $params[2]);
			} else {
				return self::template($param, $this->DataItem);
			}
		} else if($name == 'Function') {
			return self::evalFunc($param);
		} else if($name == 'IdleEven') {
			return ($this->I % 2) == 0 ? 'idle' : 'even';
		} else if($name == 'GlobalIdleEven') {
			return ($this->GlobalI % 2) == 0 ? 'idle' : 'even';
		}
		
		return $extension[0];
    }
	
	public function setContent($content) {
		$this->Content = $content;
	}
	
	public function getResult() {
		return $this->Result;
	}

    public function startParsing() {
        $this->Result = self::parseExtensions($this->Content);
    }
	
	public function parse() {
		self::startParsing();
		return self::getResult();
	}

	
	public function setResourceBundle($rb) {
		$this->ResourceBundle = $rb;
	}
	
	public function setDataItem($dataItem) {
		$this->DataItem = $dataItem;
	}
	
	
	public static function loadView($name) {
		$content = file_get_contents(SCRIPTS.'views/'.$name.'.html');
		return $content;
	}
	
	public static function initialize($name, $rb, $data) {
		$parser = new ExtensionParser();
		$parser->setContent(ExtensionParser::loadView($name));
		$parser->setResourceBundle($rb);
		$parser->setDataItem($data);
		return $parser;
	}
}

?>
