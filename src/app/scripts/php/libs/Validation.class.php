<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");

	/**
	 * 
	 *  Class Validation. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-07-01
	 * 
	 */
	class Validation extends BaseTagLib {

		private $translations = [];

		public function __construct() {
			parent::setLocalizationBundle('validation');
		}

		public function addMessage($key, $identifier) {
			$model = parent::getEditModel();
            $model->validationMessage($key, $identifier);
		}

		public function required($key) {
			$model = parent::getEditModel();
			Validator::required($model, $key);
		}

		public function email($key) {
			$model = parent::getEditModel();
			Validator::email($model, $key);
		}

		public function uploadSize($key, $maxBytes, $maxKiloBytes, $maxMegaBytes) {
			if (empty($maxBytes)) {
				$maxBytes = 0;
			}

			if (!empty($maxKiloBytes)) {
				$maxBytes += 1024 * $maxKiloBytes;
			}

			if (!empty($maxMegaBytes)) {
				$maxBytes += 1024 * 1024 * $maxMegaBytes;
			}

			if ($maxBytes > 0) {
				$model = parent::getEditModel();
				$upload = $model[$key];
				if ($upload instanceof FileUploadModel) {
					if ($upload->Size > $maxBytes) {
						$model->validationMessage($key, "invalidsize");
					}
				} else if (is_array($upload)) {
					foreach ($upload as $item) {
						if ($item instanceof FileUploadModel) {
							if ($item->Size > $maxBytes) {
								$model->validationMessage($key, "invalidsize");
							}
						}
					}
				}
            }
		}

		public function translate($identifier, $message) {
			$this->translations[$identifier] = $message;
		}

		private function getMessages($key) {
			$model = parent::getEditModel();
			$messages = [];
			if (empty($key)) {
				foreach ($model->validationMessage() as $key => $items) {
					foreach ($items as $identifier) {
						$messages[] = ["key" => $key, "identifier" => $identifier];
					}
				}

			} else {
				foreach ($model->validationMessage($key) as $identifier) {
					$messages[] = ["key" => $key, "identifier" => $identifier];
				}
			}

			return $messages;
		}

		public function message($template, $key) {
			if (parent::getEditModel()) {
				$messages = $this->getMessages($key);
				if (!empty($messages)) {
					$model = new ListModel();
					parent::pushListModel($model);

					$model->render();
					$model->items($messages);
					$result = $template();
					
					parent::popListModel();
					return $result;
				}
			}
		}

		public function getMessageList() {
			return parent::peekListModel();
		}

		private function projectMessages(string $field, $transform = null) {
			$model = parent::peekListModel();
			if ($transform == null) {
				$transform = function($value) { 
					return $value; 
				};
			}
			
			if ($model->hasCurrentItem()) {
				return $transform($model->field($field));
			}

			$values = [];
			foreach ($model->items() as $item) {
				$values[] = $transform($item[$field]);
			}

			$result = implode(", ", $values);
			return $result;
		}

		public function getMessageKey() {
			return $this->projectMessages("key");
		}

		public function getMessageIdentifier() {
			return $this->projectMessages("identifier");
		}

		public function getMessageText() {
			return $this->projectMessages("identifier", function($value) {
				return $this->translateIdentifier($value);
			});
		}

		private function translateIdentifier($identifier) {
			if (array_key_exists($identifier, $this->translations)) {
				return $this->translations[$identifier];
			}
			
			$locIdentifier = "message.$identifier";
			if (in_array($locIdentifier, parent::rb()->getKeys())) {
				return parent::rb()->get($locIdentifier);
			}

			return $identifier;
		}
	}

?>