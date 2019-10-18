<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
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

		const TableName = "language";

		public function __construct() {
			parent::setTagLibXml("Language.xml");
		}
		
		public function listItems($template, $filter = array(), $orderBy = array()) {
			$model = new ListModel();
			parent::pushListModel($model);

			if (count($orderBy) == 0) {
				$orderBy["id"] = "asc";
			}

			$sql = parent::sql()->select(self::TableName, array("id", "language", "name", "natural_name"), $filter, $orderBy);
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

		public function getListItemName() {
			return parent::peekListModel()->field("name");
		}

		public function getListItemNaturalName() {
			return parent::peekListModel()->field("natural_name");
		}

		public function getListItemUrl() {
			return parent::peekListModel()->field("language");
		}

		public function form($template, $id, $method = "POST", $submit = "", $nextPageId = "") {
			$isUpdate = !empty($id);

			$model = new EditModel();
			self::pushEditModel($model);

			if (self::isHttpMethod($method) && ($submit == "" || array_key_exists($submit, $_REQUEST))) {
                $model->submit();
                self::parseContent($template);
                $model->submit(false);
				
				if (!$isUpdate) {
					$sql = parent::sql()->insert(self::TableName, $model);
					parent::dataAccess()->execute($sql);
				} else {
					$sql = parent::sql()->update(self::TableName, $model, array("id" => $id));
					parent::dataAccess()->execute($sql);
				}
				
                if (!empty($nextPageId)) {
					self::web()->redirectTo($nextPageId);
                } else {
					if (!$isUpdate) {
						self::popEditModel();
                        $model = new EditModel();
                        self::pushEditModel($model);
                    }
                }
            }

			if ($isUpdate) {
                $model->registration();
                self::parseContent($template);
				$model->registration(false);
				
				$columns = $model->fields();
				$sql = parent::sql()->select(self::TableName, $columns, array("id" => $id));
				$data = parent::dataAccess()->fetchSingle($sql);
				if (empty($data)) {
					self::popEditModel();
					return "<h4 class='warning'>Such language doesn't exist.</h4>";
				}

				$model->copyFrom($data);
			}
			
            $model->render();
            $result = self::ui()->form($template, "post");
            self::popEditModel();
            return $result;
		}

		public function deleter($template, $id) {
            $sql = parent::sql()->delete(self::TableName, array("id" => $id));
            self::dataAccess()->execute($sql);
            
            self::parseContent($template);
		}
	}

?>