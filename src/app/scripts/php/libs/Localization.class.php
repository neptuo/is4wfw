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

		private $loaded = [];

		public function setLanguage($name) {
			$this->web()->LanguageName = $name;
		}

		public function load($name, $lang, $system = true, $moduleId = "") {
			$bundle = LocalizationBundle::create($name, $lang, $system, $moduleId);
			if ($bundle != null) {
				$this->loaded[] = $bundle;
			}
		}

		public function useBundle($template, $name, $system = true, $moduleId = "") {
			$this->setLocalizationBundle($name, $system, $moduleId);
			return $template();
		}

		public function getProperty($name) {
			foreach ($this->loaded as $bundle) {
				$result = $bundle->find($name);
				if ($result !== false) {
					return $result;
				}
			}

			return $this->rb($name);
		}

		private function submit($rb, $editModel, $listModel, $template) {
			$keys = $_POST["key"];
			$count = count($keys);
			
			$listItems = array();
			for ($index = 0; $index < $count; $index++) { 
				$listItems[] = $this->createListItem($index);
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
			$this->pushListModel($listModel);

			if ($editModel->isSubmit()) {
				$this->submit($rb, $editModel, $listModel, $template);
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
							if (!$this->isKeyIncluded($filterKeyPrefix, $key)) {
								$rb->set($key, $source->get($key));
							}
						}
					}
				}

				$this->save($rb, $editModel, $listModel);
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
						if ($this->isKeyIncluded($filterKeyPrefix, $key)) {
							$listItems[] = $this->createListItem($index);
							$keys[] = $key;
							$values[] = $rb->get($key);
							$index++;
						}
					}
				}

				$listItems[] = $this->createListItem($index);
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
			
			$this->popListModel();
			return $result;
		}
		
		public function edit($bundleName, $languageName, $filterKeyPrefixes = "") {
			return $this->editFullTag(null, $bundleName, $languageName, $filterKeyPrefixes);
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
			return $this->peekListModel();
		}
		
		public function getEditItemIndex() {
			return $this->peekListModel()->field("index");
		}

		public function download($bundleName, $languageName) {
			$rb = new LocalizationBundle();
			$rb->setSource($bundleName);
			$rb->setLanguage($languageName);
			$rb->setIsSystem(false);
			if ($rb->exists()) {
				$filePath = $rb->getFilePath();

				if (file_exists($filePath) && is_readable($filePath)) {
					$fileSize = filesize($filePath);
		
					header('Content-Type: text/plain');
					header('Accept-Ranges: bytes');
					header('Content-Length: ' . $fileSize);
					header('Content-Disposition: attachment; filename=' . $rb->getFileName());
					header('Content-Transfer-Encoding: binary');
					$file = @fopen($filePath, 'rb');
					if ($file) {
						fpassthru($file);
						exit;
					} else {
						header("HTTP/1.1 404 Not Found");
						echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
						exit;
					}
				} else {
					header("HTTP/1.1 404 Not Found");
					echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
					exit;
				}
			}
		}
	}

?>