<?php

use Mpdf\Writer\ObjectWriter;

require_once("BaseTagLib.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/JsonInputException.class.php");
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

        private $outputPreferedContentType = "application/json";
        private $output;
        private $outputKey;
        private $outputParentType = '';

        private $input;
        private $inputKey;
        private $inputParentType = '';
        private $inputArrayIndex;

        public function processOutput($template, $format = 'indented') {
            parent::web()->setFlushOptions("none", $this->outputPreferedContentType);

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

        public function processInput($template) {
            $contentType = parent::requestHeader("Content-Type");
            if ($contentType === "text/json" || $contentType === "application/json") {
                $value = file_get_contents('php://input');
                $value = json_decode($value);
                if ($value) {
                    $this->mode = JsonMode::Input;

                    $this->outputPreferedContentType = $contentType;
                    $this->input = new Stack();
                    $this->input->push($value);

                    $prevModel = parent::getEditModel(false);
                    $model = new EditModel();
                    parent::setEditModel($model);

                    // Submit form / bind data into the model.
                    $model->submit();
                    $template();
                    $model->submit(false);

                    if ($model->isValid()) {
                        // Save data in transaction.
                        try {
                            parent::dataAccess()->transaction(function() use ($model, $template) {
                                $model->save();
                                $template();
                                $model->save(false);
                            });
                        } catch (Exception $e) {
                            $this->internalServerError($e);
                        }

                        // Process after save redirects.
                        $model->saved(true);
                        $template();
                        $model->saved(false);
                    } else {
                        $this->badRequest($model);
                    }

                    parent::clearEditModel($prevModel);

                    $this->input = null;
                }
            } else {
                $this->badRequest();
            }
        }

        private function badRequest(EditModel $model = null) {
            header("HTTP/1.1 400 Bad Request");

            if ($model != null) {
                $response = [
                    "type" => "https://is4wfw.neptuo.com/api-responses/validation-error",
                    "validation" => []
                ];

                foreach ($model->prefixes() as $prefix) {
                    $model->prefix($prefix);
                    $response["validation"][$prefix] = $model->validationMessage();
                }

                $model->prefix(null);
                
                $response["validation"] = (object)$response["validation"];
                $responseJson = json_encode((object)$response);
                echo $responseJson;
            };

            parent::close();
        }

        private function internalServerError(Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");

            if ($e != null) {
                $response = [
                    "type" => "https://is4wfw.neptuo.com/api-responses/exception",
                    "exceptions" => []
                ];

                $exResponse = [
                    "type" => get_class($e),
                ];

                if (parent::web()->getDebugMode()) {
                    $exResponse["message"] = $e->getMessage();
                    $exResponse["stack"] = $e->getTraceAsString();
                }

                $response["exceptions"][] = (object)$exResponse;
                
                $response["exceptions"] = (object)$response["exceptions"];
                $responseJson = json_encode((object)$response);
                echo $responseJson;
            }

            parent::close();
        }

        public function processObject($template) {
            if ($this->mode == JsonMode::Output) {
                if (!in_array($this->outputParentType, ['key-value', 'array', ''])) {
                    throw new JsonOutputException("Object can't be placed outside of key-value, array or root.");
                }

                $wasEmpty = false;
                $previousOutputKey = $this->outputKey;
                $previousOutputParentType = $this->outputParentType;

                $value = [];
                if ($this->output->isEmpty()) {
                    $wasEmpty = true;
                } else if ($this->outputParentType == '') {
                    throw new JsonOutputException("Multiple root elements.");
                }

                $this->output->push($value);
                $this->outputParentType = 'object';
            
                $template();

                $this->outputKey = $previousOutputKey;
                $this->outputParentType = $previousOutputParentType;

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
            } else if ($this->mode == JsonMode::Input) {
                if (!in_array($this->inputParentType, ['key-value', 'array', ''])) {
                    throw new JsonInputException("Object can't be placed outside of key-value, array or root.");
                }

                $value = $this->input->peek();
                if (is_object($value)) {
                    $previousInputParentType = $this->inputParentType;
                    $this->inputParentType = 'object';

                    $template();
                    
                    $this->inputParentType = $previousInputParentType;
                }
            }
        }

        public function processArray($template) {
            if ($this->mode == JsonMode::Output) {
                if (!in_array($this->outputParentType, ['key-value', ''])) {
                    throw new JsonOutputException("Array can't be placed outside of key-value or root.");
                }
                
                $wasEmpty = false;
                $previousOutputKey = $this->outputKey;
                $previousOutputParentType = $this->outputParentType;
                
                $value = [];
                if ($this->output->isEmpty()) {
                    $wasEmpty = true;
                } else if ($this->outputParentType == '') {
                    throw new JsonOutputException("Multiple root elements.");
                }
                
                $this->output->push($value);
                $this->outputKey = null;
                $this->outputParentType = 'array';
            
                $template();
            
                $this->outputKey = $previousOutputKey;
                $this->outputParentType = $previousOutputParentType;

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
            }  else if ($this->mode == JsonMode::Input) {
                if (!in_array($this->inputParentType, ['key-value', ''])) {
                    throw new JsonInputException("Array can't be placed outside of key-value or root.");
                }
                
                $previousInputArrayIndex = $this->inputArrayIndex;
                $previousInputParentType = $this->inputParentType;
                
                $value = $this->input->peek();
                $this->inputParentType = 'array';
                
                if (is_array($value)) {
                    for ($i = 0; $i  < count($value); $i ++) { 
                        $item = $value[$i];
                        $this->input->push($item);
                        $this->inputArrayIndex = $i;
                        
                        $template();
                        
                        $this->input->pop();
                    }
                }
                
                $this->inputArrayIndex = $previousInputArrayIndex;
                $this->inputParentType = $previousInputParentType;
            }
        }

        public function processKey($name, $value, $type) {
            if ($this->mode == JsonMode::Output) {
                if ($this->output->isEmpty() || $this->outputParentType != 'object') {
                    throw new JsonOutputException("Key-value '$name' can't be outide of object.");
                }

                if ($type == "number") {
                    $value = (float)$value;
                } else if ($type == "bool") {
                    $value = (bool)$value;
                }

                $parent = $this->output->pop();
                $parent[$name] = $value;
                $this->output->push($parent);
            } else if ($this->mode == JsonMode::Input) {
                throw new JsonInputException("Key-value without template is not supported in input mode.");
            }
        }

        public function processKeyWithBody($template, $name) {
            $previousOutputKey = $this->outputKey;
            $previousOutputParentType = $this->outputParentType;
            if ($this->mode == JsonMode::Output) {
                if ($this->output->isEmpty() || $this->outputParentType != 'object') {
                    throw new JsonOutputException("Key-value '$name' can't be placed outside of object.");
                }
                
                $this->outputKey = $name;
                $this->outputParentType = 'key-value';
            
                $template();

                $this->outputKey = $previousOutputKey;
                $this->outputParentType = $previousOutputParentType;
            } else if ($this->mode == JsonMode::Input) {
                if ($this->inputParentType != 'object') {
                    throw new JsonInputException("Key-Value can't be placed outside of key-value or root.");
                }

                $value = (array)$this->input->peek();
                if (array_key_exists($name, $value)) {
                    $previousInputParentType = $this->inputParentType;
                    $previousKey = $this->inputKey;
                    $this->inputKey = $name;

                    $item = $value[$name];
                    $this->input->push($item);
                    $this->inputParentType = 'key-value';

                    $template();

                    $this->input->pop();
                    $this->inputParentType = $previousInputParentType;
                    $this->inputKey = $previousKey;
                }
            }
        }

        public function getArrayIndex() {
            if ($this->mode == JsonMode::Output) {
                throw new JsonOutputException("Property 'arrayIndex' are not support in output mode.");
            } else if ($this->mode == JsonMode::Input) {
                return $this->inputArrayIndex;
            }
        }

        public function getInputValue() {
            if ($this->mode == JsonMode::Output) {
                throw new JsonOutputException("Property 'value' are not support in output mode.");
            } else if ($this->mode == JsonMode::Input) {
                $value = $this->input->peek();
                return $value;
            }
        }
    }
    
    class JsonMode {
        public const Output = 'output';
        public const Input = 'input';
    }

?>