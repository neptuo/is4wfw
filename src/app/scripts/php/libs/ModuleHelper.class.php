<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Module.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");

	/**
	 * 
	 *  Class ModuleHelper. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-07-14
	 * 
	 */
	class ModuleHelper extends BaseTagLib {

		private $current = null;
		private $currentViews = null;

		private function ensureCurrent() {
			if ($this->current == null) {
				throw new Exception("Current module not set.");
			}
		}

		public function use($template, $id, $alias) {
			$lastCurrent = $this->current;

			if (empty($id) && empty($alias)) {
				throw new ParameterException("id", "One for 'id' or 'alias' must be provided.");
			}

			if (!empty($id)) {
				$this->current = Module::getById($id);
			} else if (!empty($alias)) {
				$this->current = Module::getByAlias($alias);
			}
			
			$result = $template();

			$this->current = $lastCurrent;

			return $result;
		}

		public function views($template, $path) {
			$this->ensureCurrent();

			$lastViews = $this->currentViews;

			$this->currentViews = FileUtils::combinePath($this->current->getViewsPath(), $path);
			$result = $template();

			$this->currentViews = $lastViews;

			return $result;
		}

		public function getViewsPath() {
			return $this->currentViews;
		}

		public function assets($template, $path) {
			$this->ensureCurrent();

			$lastAssets = $this->currentAssets;

			$this->currentAssets = FileUtils::combinePath($this->current->getAssetsPath(), $path);
			$result = $template();

			$this->currentViews = $lastAssets;

			return $result;
		}

		public function getAssetsUrl() {
			return $this->currentAssets;
		}

		public function list($template) {
			$modules = Module::all();
			
			$model = new ListModel();
			$this->pushListModel($model);
			
			$model->render();
			$model->items($modules);
			$result = $template();
			
			$this->popListModel();
			return $result;
		}

		public function getList() {
			return $this->peekListModel();
		}

		public function getId() {
			if ($this->hasListModel()) {
				return $this->peekListModel()->data()->id;
			}

			if ($this->current) {
				return $this->current->id;
			}
		}

		public function getAlias() {
			if ($this->hasListModel()) {
				return $this->peekListModel()->data()->alias;
			}

			if ($this->current) {
				return $this->current->alias;
			}
		}

		public function getName() {
			if ($this->hasListModel()) {
				return $this->peekListModel()->data()->name;
			}

			if ($this->current) {
				return $this->current->name;
			}
		}

		public function edit($template) {
			$model = parent::getEditModel();

			if ($model->isLoad()) {
				$template();
			}

			if ($model->isSubmit()) {
				$template();
				
				Validator::required($model, "alias");
				Validator::required($model, "zip");

				if ($model->isValid()) {
					if (Module::findByAlias($model["alias"])) {
						Validator::addUnique($model, "alias");
					}

					$xml = ZipUtils::getFileContent($model["zip"]->TempName, "module.xml");
					if ($xml) {
						$xml = new SimpleXMLElement($xml);
						if (isset($xml->id) && isset($xml->name) && isset($xml->is4wfw->minVersion)) {
							if (Module::findById($xml->id)) {
								Validator::addUnique($model, "zip");
							}
							
							// TODO: Check version.
						} else {
							Validator::addInvalidValue($model, "zip");
						}
					} else {
						Validator::addInvalidValue($model, "zip");
					}
				}
            }
				
			if ($model->isSave()) {
				$file = $model["zip"];
				if ($file) {
					$tempName = $file->TempName;
					$xml = ZipUtils::getFileContent($tempName, "module.xml");
					if ($xml) {
						$xml = new SimpleXMLElement($xml);
						if (isset($xml->is4wfw->minVersion)) {
							$xml->alias = $model["alias"];

							$modules = ModuleXml::read();

							$toDom = dom_import_simplexml($modules);
							$fromDom = dom_import_simplexml($xml);
							$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));

							ModuleXml::write($modules);

							$extractPath = MODULES_PATH . $model["alias"];
							mkdir($extractPath);
							ZipUtils::extract($tempName, $extractPath);
							
							ModuleGenerator::all();
						} else {
							Validator::addInvalidValue($model, "zip");
						}
					}
				}
			}
			
            if ($model->isSaved()) {
                $template();
            }
			
            if ($model->isRender()) {
				$result = $template();
				return $result;
			}
		}

		public function rebuildInitializers($template) {
			ModuleGenerator::loader();
			ModuleGenerator::postInit();
			$template();
		}

		public function delete($template, $id) {
			$module = Module::findById($id);
			if ($module) {
				$xml = ModuleXml::read();
				for ($i=0; $i < count($xml); $i++) { 
					if ($xml->module[$i]->id == $id) {
						unset($xml->module[$i]);
						break;
					}
				}

				ModuleXml::write($xml);
				FileUtils::removeDirectory($module->getRootPath());

				$this->rebuildInitializers($template);
			}
		}

		public function entrypoint($id, $params = []) {
			$this->ensureCurrent();
			return $this->web()->renderEntrypoint($this->current->id, $id, $params);
		}
    }

?>