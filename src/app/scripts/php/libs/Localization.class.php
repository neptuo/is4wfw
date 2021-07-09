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

		public function setLanguage($name) {
			self::web()->LanguageName = $name;
		}

		public function useBundle($template, $name, $system = true) {
			self::setLocalizationBundle($name, $system);
			return $template();
		}

		public function getProperty($name) {
			return self::rb($name);
		}

		private function submit($rb, $editModel, $listModel, $template) {
			$keys = $_POST["key"];
			$count = count($keys);
			
			$listItems = array();
			for ($index = 0; $index < $count; $index++) { 
				$listItems[] = self::createListItem($index);
			}

			$listModel->items($listItems);
			$listModel->render();
			if ($template == null) {
				$this->partialView("localization/edit");
			} else {
				$template();
			}
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

		public function editFullTag($template, $bundleName, $languageName, $filterKeyPrefix = "") {
			if (!empty($filterKeyPrefix)) {
				$filterKeyPrefix = explode(",", $filterKeyPrefix);
			}

			$rb = new LocalizationBundle();
			$rb->setSource($bundleName);
			$rb->setLanguage($languageName);
			$rb->setIsSystem(false);

			$editModel = parent::getEditModel();
			$listModel = new ListModel();
			self::pushListModel($listModel);

			if ($editModel->isSubmit()) {
				self::submit($rb, $editModel, $listModel, $template);
			}

			if ($editModel->isSave()) {
				if (!empty($filterKeyPrefix)) {
					$source = new LocalizationBundle();
					$source->setSource($bundleName);
					$source->setLanguage($languageName);
					$source->setIsSystem(false);
					if ($rb->exists()) {
						$source->load();
						foreach ($source->getKeys() as $key) {
							if (!self::isKeyIncluded($filterKeyPrefix, $key)) {
								$rb->set($key, $source->get($key));
							}
						}
					}
				}

				self::save($rb, $editModel, $listModel);
			}
			
            if ($editModel->isSaved()) {
				if ($template == null) {
					$this->partialView("localization/edit");
				} else {
					$template();
				}
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
						if (self::isKeyIncluded($filterKeyPrefix, $key)) {
							$listItems[] = self::createListItem($index);
							$keys[] = $key;
							$values[] = $rb->get($key);
							$index++;
						}
					}
				}

				$listItems[] = self::createListItem($index);
				$keys[] = "";
				$values[] = "";

				$editModel["key"] = $keys;
				$editModel["value"] = $values;
				$listModel->items($listItems);
				
				$listModel->render();
				if ($template == null) {
					$result .= $this->partialView("localization/edit");
				} else {
					$result .= $template();
				}
				$listModel->render(false);
			}
			
			self::popListModel();
			return $result;
		}
		
		public function edit($bundleName, $languageName, $filterKeyPrefixes = "") {
			return self::editFullTag(null, $bundleName, $languageName, $filterKeyPrefixes);
		}

		private function isKeyIncluded($filter, $key) {
			if ($filter == null) {
				return true;
			}

			for ($i=0; $i < count($filter); $i++) { 
				if (StringUtils::startsWith($key, $filter[$i])) {
					return true;
				}
			}

			return false;
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