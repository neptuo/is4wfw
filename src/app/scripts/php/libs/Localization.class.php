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

		private function save($rb, $editModel, $listModel) {
			$keys = $_POST["key"];
			$count = count($keys);
			
			$listItems = array();
			for ($index = 0; $index < $count; $index++) { 
				$listItems[] = self::createListItem($index);
			}

			$listModel->items($listItems);
			
			$editModel->submit();
			$listModel->render();
			self::partialView("localization/edit");
			$editModel->submit(false);
			$listModel->render(false);
			
			for ($index = 0; $index < $count; $index++) { 
				$key = $editModel["key"][$index];
				$value = $editModel["value"][$index];
				if (!empty($key)) {
					$rb->set($key, $value);
				}
			}

			$rb->save();
			self::redirectToSelf();
			return;
		}

		public function edit($bundleName, $languageName) {
			$rb = new LocalizationBundle();
			$rb->setSource($bundleName);
			$rb->setLanguage($languageName);
			$rb->setIsSystem(false);

			$editModel = new EditModel();
			$listModel = new ListModel();
			self::pushEditModel($editModel);
			self::pushListModel($listModel);

			if (array_key_exists("loc-edit-save", $_POST)) {
				self::save($rb, $editModel, $listModel);
			}

			$result = "";
			$listItems = array();

			$index = 0;
			if ($rb->exists()) {
				$rb->load();
				foreach ($rb->getKeys() as $key) {
					$listItems[] = self::createListItem($index);
					$editModel["key"][] = $key;
					$editModel["value"][] = $rb->get($key);
					$index++;
				}
			}

			$listItems[] = self::createListItem($index);
			$editModel["key"][] = "";
			$editModel["value"][] = "";

			$listModel->items($listItems);
			
			$editModel->render();
			$listModel->render();
			$result .= self::partialView("localization/edit");

			self::popEditModel();
			self::popListModel();
			return $result;
		}

		private function createListItem($index) {
			return array(
				"index" => $index
			);
		}

		public function getListItems() {
			return self::peekListModel();
		}

		public function getListItemKey() {
			return self::peekListModel()->field("key");
		}

		public function getListItemValue() {
			return self::peekListModel()->field("value");
		}

		public function getListItemIndex() {
			return self::peekListModel()->field("index");
		}
	}

?>