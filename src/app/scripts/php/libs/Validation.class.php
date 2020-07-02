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

		public function __construct() {
			parent::setTagLibXml("Validation.xml");
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

		public function getMessageIdentifier() {
			$model = parent::peekListModel();
			$text = implode(", ", $model->items());
			return $text;
		}

		public function getMessageText() {
			$model = parent::peekListModel();
			$text = implode(", ", $model->items());
			// TODO: Translate.
			return $text;
		}
	}

?>