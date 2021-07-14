<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Module.class.php");

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

		public function use($template, $id) {
			$lastCurrent = $this->current;

			$this->current = Module::getById($id);
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
			return $this->peekListModel()->data()->id;
		}

		public function getAlias() {
			return $this->peekListModel()->data()->alias;
		}

		public function getName() {
			return $this->peekListModel()->data()->name;
		}
    }

?>