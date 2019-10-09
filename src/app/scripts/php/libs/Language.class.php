<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

	/**
	 * 
	 *  Class Language. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2019-10-09
	 * 
	 */
	class Language extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Language.xml");
		}
		
		public function listItems($template) {
			$model = new ListModel();
			parent::pushListModel($model);

			$sql = parent::sql()->select("language", array("id", "language"), array(), array("id" => "asc"));
			$data = self::dataAccess()->fetchAll($sql);

			$model->render();
            $model->items($data);
			$result = parent::parseContent($template);

			parent::popListModel();
			return $result;
		}

		public function getListItems() {
			return parent::peekListModel();
		}

		public function getListItemId() {
			return parent::peekListModel()->field("id");
		}

		public function getListItemUrl() {
			return parent::peekListModel()->field("language");
		}
	}

?>