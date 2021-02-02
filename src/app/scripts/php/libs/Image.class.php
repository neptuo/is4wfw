<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/dataaccess/Select.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/RoleHelper.class.php");

	/**
	 * 
	 *  Class Image. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2017-11-26
	 * 
	 */
	class Image extends BaseTagLib {

		public function __construct() {
			global $webObject;

			parent::setTagLibXml("Image.xml");
		}
		
		protected function canUserFile($objectId, $rightType) {
			return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$FileRightDesc, $objectId, $rightType));
		}

		private function renderImage($template, $image) {
			if ($this->canUserFile($image['id'], WEB_R_READ)) {
				$this->setFileId($image['id']);
				$this->setFileUrl($image['url']);
				parent::request()->set('name', $image['name'], 'g:image');
				parent::request()->set('dir_id', $image['dir_id'], 'g:image');
				parent::request()->set('title', $image['title'], 'g:image');
				parent::request()->set('type', $image['type'], 'g:image');

				return $template();
			}

			return '';
		}
		
		//C-tag
		public function directoryList($template, $id, $pageIndex = false, $limit = false, $noDataMessage = false, $noDataImageId = false, $orderBy = "name") {
			$images = parent::dao('File')->getImagesFromDirectory($id, $orderBy, $pageIndex, $limit);
			$return = '';

			if (count($images) == 0) {
				if($noDataImageId != '') {
					$image = parent::dao('File')->get($noDataImageId);
					if (count($image) != 0) {
						$return .= $this->renderImage($template, $image);
					} else {
						$return .= $noDataMessage;
					}
				} else {
					$return .= $noDataMessage;
				}
			} else {
				foreach($images as $image) {
					$return .= $this->renderImage($template, $image);
				}
			}

			return $return;
		}
		
		//C-tag
		public function file($template, $id, $noDataMessage = false) {
			$image = parent::dao('File')->get($id);
			$return = '';

			if(count($image) == 0) {
				$return .= $noDataMessage;
			} else {
				$return .= $this->renderImage($template, $image);
			}

			return $return;
		}

		//C-Tag
		public function imageUrl($width = false, $height = false) {
			$size = '';
			if($width > 0 && $height > 0) {
				$size = '&width='.$width.'&height='.$height;
			} elseif($width > 0) {
				$size = '&width='.$width;
			} elseif($height > 0) {
				$size = '&height='.$height;
			}

			return "~/file.php?rid=" . $this->getFileId() . $size;
		}

		//C-Tag
		public function imageName() {
			return parent::request()->get('name', 'g:image');
		}

		//C-Tag
		public function imageTitle() {
			return parent::request()->get('title', 'g:image');
		}

		//C-Tag
		public function imageType() {
			return parent::request()->get('type', 'g:image');
		}

        public function getFavicon($fileId) {
			if ($this->canUserFile($fileId, WEB_R_READ)) {
				$image = parent::dao('File')->get($fileId);
				$url = "~/file.php?rid=" . $fileId;
				$contentType = FileAdmin::$FileMimeTypes[$image['type']];
				return $this->web()->getFavicon($url, $contentType);
			}

			return '';
        }
		
		public function setFileId($value) {
			parent::request()->set('file-id', $value);
			return $value;
		}

		public function getFileId() {
			return parent::request()->get('file-id');
		}
		
		public function setFileUrl($value) {
			parent::request()->set('file-url', $value);
			return $value;
		}

		public function getFileUrl() {
			return parent::request()->get('file-url');
		}
	}

?>