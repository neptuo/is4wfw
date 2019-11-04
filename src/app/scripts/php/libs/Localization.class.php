<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Localization. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2019-10-07
	 * 
	 */
	class Localization extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Localization.xml");
		}
		
		public function setLanguage($name) {
			self::web()->LanguageName = $name;
		}

		public function useBundle($template, $name, $system = true) {
			self::setLocalizationBundle($name, $system);
			return self::parseContent($template);
		}

		public function getProperty($name) {
			return self::rb($name);
		}

		private function submit($rb, $editModel, $listModel) {
			$keys = $_POST["key"];
			$count = count($keys);
			
			$listItems = array();
			for ($index = 0; $index < $count; $index++) { 
				$listItems[] = self::createListItem($index);
			}

			$listModel->items($listItems);
			$listModel->render();
			self::partialView("localization/edit");
			$listModel->render(false);
		}

		private function save($rb, $editModel, $listModel) {
			$keys = $_POST["key"];
			$count = count($keys);

			for ($index = 0; $index < $count; $index++) { 
				$key = $editModel["key"][$index];
				$value = $editModel["value"][$index];
				if (!empty($key)) {
					$rb->set($key, $value);
				}
			}

			$rb->save();
		}

		public function edit($bundleName, $languageName) {
			$rb = new LocalizationBundle();
			$rb->setSource($bundleName);
			$rb->setLanguage($languageName);
			$rb->setIsSystem(false);

			$editModel = parent::getEditModel();
			$listModel = new ListModel();
			self::pushListModel($listModel);

			if ($editModel->isSubmit()) {
				self::submit($rb, $editModel, $listModel);
			}

			if ($editModel->isSave()) {
				self::save($rb, $editModel, $listModel);
			}

			$result = "";
			if ($editModel->isRender()) {
				$listItems = array();

				$keys = array();
				$values = array();
				$index = 0;
				if ($rb->exists()) {
					$rb->load();
					foreach ($rb->getKeys() as $key) {
						$listItems[] = self::createListItem($index);
						$keys[] = $key;
						$values[] = $rb->get($key);
						$index++;
					}
				}

				$listItems[] = self::createListItem($index);
				$keys[] = "";
				$values[] = "";

				$editModel["key"] = $keys;
				$editModel["value"] = $values;
				$listModel->items($listItems);
				
				$listModel->render();
				$result .= self::partialView("localization/edit");
				$listModel->render(false);
			}
			
			self::popListModel();
			return $result;
		}

		private function createListItem($index) {
			return array(
				"index" => $index
			);
		}

		public function getEditItems() {
			return self::peekListModel();
		}

		public function getEditItemIndex() {
			return self::peekListModel()->field("index");
		}
	}

?>