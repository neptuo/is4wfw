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

		private function renderImage($parser, $content, $image) {
			if ($this->canUserFile($image['id'], WEB_R_READ)) {
				$this->setFileId($image['id']);
				$this->setFileUrl($image['url']);
				parent::request()->set('name', $image['name'], 'g:image');
				parent::request()->set('dir_id', $image['dir_id'], 'g:image');
				parent::request()->set('title', $image['title'], 'g:image');
				parent::request()->set('type', $image['type'], 'g:image');

				$parser->setContent($content);
				$parser->startParsing();
				return $parser->getResult();
			}

			return '';
		}
		
		//C-tag
		public function directoryList($content, $id, $pageIndex = false, $limit = false, $noDataMessage = false, $noDataImageId = false) {
			$parser = new FullTagParser();
			
			$images = parent::dao('File')->getImagesFromDirectory($id, $pageIndex, $limit);
			$return = '';

			if (count($images) == 0) {
				if($noDataImageId != '') {
					$image = parent::dao('File')->get($noDataImageId);
					if (count($image) != 0) {
						$return .= self::renderImage($parser, $content, $image);
					} else {
						$return .= $noDataMessage;
					}
				} else {
					$return .= $noDataMessage;
				}
			} else {
				foreach($images as $image) {
					$return .= self::renderImage($parser, $content, $image);
				}
			}

			return $return;
		}
		
		//C-tag
		public function file($content, $id, $noDataMessage = false) {
			$parser = new FullTagParser();
			
			$image = parent::dao('File')->get($id);
			$return = '';

			if(count($image) == 0) {
				$return .= $noDataMessage;
			} else {
				$return .= self::renderImage($parser, $content, $image);
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

			return "~/file.php?rid=" .self::getFileId(). $size;
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