<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

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
			parent::setTagLibXml("Validation.xml");
			parent::setLocalizationBundle('validation');
		}

		public function translate($identifier, $message) {
			$this->translations[$identifier] = $message;
		}

		private function getMessages($key) {
			$model = parent::getEditModel();
			if (empty($key)) {
				return [];
			} else {
				return $model->validationMessage($key);
			}
		}

		public function message($template, $key) {
			if (parent::getEditModel()) {
				$messages = $this->getMessages($key);
				if (!empty($messages)) {
					$model = new ListModel();
					parent::pushListModel($model);

					$model->render();
					$model->items($messages);
					$result = parent::parseContent($template);
					
					parent::popListModel();
					return $result;
				}
			}
		}

		public function getMessageList() {
			return parent::peekListModel();
		}

		public function getMessageIdentifier() {
			$model = parent::peekListModel();
			if ($model->hasDataItem()) {
				return $model->data();
			}

			$text = implode(", ", $model->items());
			return $text;
		}

		public function getMessageText() {
			$model = parent::peekListModel();
			if ($model->hasDataItem()) {
				return $this->translateIdentifier($model->data());
			}

			$texts = [];
			foreach ($model->items() as $identifier) {
				$texts[] = $this->translateIdentifier($identifier);
			}

			$text = implode(", ", $texts);
			return $text;
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