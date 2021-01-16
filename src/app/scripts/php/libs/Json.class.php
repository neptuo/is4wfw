<?php

use Mpdf\Writer\ObjectWriter;

require_once("BaseTagLib.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/JsonOutputException.class.php");

	/**
	 * 
	 *  Class Json. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-01-16
	 * 
	 */
	class Json extends BaseTagLib {

        private $mode;
        private $output;
        private $outputKey;

		public function __construct() {
			parent::setTagLibXml("Json.xml");
        }
        
        public function processOutput($template, $format = 'indented') {
            parent::web()->setFlushOptions("none", "application/json");

            $this->mode = JsonMode::Output;
            $this->output = new Stack();

            $template();

            $value = $this->output->pop();
            $options = 0;
            if ($format == 'indented') {
                $options |= JSON_PRETTY_PRINT;
            }

            return json_encode($value, $options);
        }

        public function processObject($template) {
            $wasEmpty = false;
            $previousOutputKey = $this->outputKey;
            if ($this->mode == JsonMode::Output) {
                $value = [];
                if ($this->output->isEmpty()) {
                    $wasEmpty = true;
                }

                $this->output->push($value);
            }
            
            $template();
            
            if ($this->mode == JsonMode::Output) {
                $this->outputKey = $previousOutputKey;

                $value = (object)$this->output->pop();
                if (!$wasEmpty) {
                    $parent = $this->output->pop();
                    if ($this->outputKey != null) {
                        $parent[$this->outputKey] = $value;
                    } else {
                        $parent[] = $value;
                    }
                    $this->output->push($parent);
                } else {
                    $this->output->push($value);
                }
            }
        }

        public function processArray($template) {
            $wasEmpty = false;
            $previousOutputKey = $this->outputKey;
            if ($this->mode == JsonMode::Output) {
                $value = [];
                if ($this->output->isEmpty()) {
                    $wasEmpty = true;
                }
                
                $this->output->push($value);
                $this->outputKey = null;
            }
            
            $template();
            
            if ($this->mode == JsonMode::Output) {
                $this->outputKey = $previousOutputKey;

                $value = $this->output->pop();
                if (!$wasEmpty) {
                    $parent = $this->output->pop();
                    if ($this->outputKey != null) {
                        $parent[$this->outputKey] = $value;
                    } else {
                        $parent[] = $value;
                    }
                    $this->output->push($parent);
                } else {
                    $this->output->push($value);
                }
            }
        }

        public function processKey($name, $value, $type) {
            if ($this->mode == JsonMode::Output) {
                if ($this->output->isEmpty()) {
                    throw new JsonOutputException("Key-value can't be placed in the response root.");
                } else {
                    if ($type == "number") {
                        $value = (float)$value;
                    } else if ($type == "bool") {
                        $value = (bool)$value;
                    }

                    $parent = $this->output->pop();
                    $parent[$name] = $value;
                    $this->output->push($parent);
                }
            }
        }

        public function processKeyWithBody($template, $name) {
            $previousOutputKey = $this->outputKey;
            if ($this->mode == JsonMode::Output) {
                if ($this->output->isEmpty()) {
                    throw new JsonOutputException("Key-value can't be placed in the response root.");
                } else {
                    $this->outputKey = $name;
                }
            }
            
            $template();

            if ($this->mode == JsonMode::Output) {
                $this->outputKey = $previousOutputKey;
            }
        }
    }
    
    class JsonMode {
        public const Output = 'output';
        public const Input = 'input';
    }

?>