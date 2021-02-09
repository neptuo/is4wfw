<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/dataaccess/Select.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/RoleHelper.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FileUploadModel.class.php");

	/**
	 * 
	 *  Class FileAdmin. Replacement for admin function from File class.
	 *      
	 *  @author     Marek SMM
	 *  @timestamp  2012-01-14
	 * 
	 */
	class FileAdmin extends BaseTagLib {
		public static $DirectoryRightDesc = array(
			'directory_right', 'did', 'gid', 'type'
		);
		public static $FileRightDesc = array(
			'file_right', 'fid', 'gid', 'type'
		);

		public static $FileSystemItemPath = 'id';
		
		public static $FileExtensions = array(
			WEB_TYPE_CSS => "css", WEB_TYPE_JS => "js", WEB_TYPE_JSON => "json", WEB_TYPE_JPG => "jpg", WEB_TYPE_GIF => "gif", 
			WEB_TYPE_PNG => "png", WEB_TYPE_PDF => "pdf", WEB_TYPE_RAR => "rar", WEB_TYPE_ZIP => "zip", 
			WEB_TYPE_TXT => "txt", WEB_TYPE_XML => "xml", WEB_TYPE_XSL => "xsl", WEB_TYPE_DTD => "dtd",
			WEB_TYPE_HTML => "html", WEB_TYPE_PHP => "php", WEB_TYPE_SQL => "sql", WEB_TYPE_C => "c",
			WEB_TYPE_CPP => "cpp", WEB_TYPE_H => "h", WEB_TYPE_JAVA => "java", WEB_TYPE_SWF => "swf",
			WEB_TYPE_MP3 => "mp3", WEB_TYPE_PSD => "psd", WEB_TYPE_DOC => "doc", WEB_TYPE_PPT => "ppt",
			WEB_TYPE_XLS => "xls", WEB_TYPE_MPEG => "mpeg", WEB_TYPE_MOV => "mov",
			WEB_TYPE_BMP => "bmp", WEB_TYPE_AVI => "avi", WEB_TYPE_ICO => "ico", WEB_TYPE_HTM => "htm",
			WEB_TYPE_TTF => "ttf", WEB_TYPE_WOFF => "woff", WEB_TYPE_WOFF2 => "woff2", WEB_TYPE_EOT => "eot"
		);
		
		public static $FileMimeTypes = array(
			WEB_TYPE_CSS => "text/css", WEB_TYPE_JS => "application/x-javascript", WEB_TYPE_JSON => "application/json", WEB_TYPE_JPG => "image/jpeg", WEB_TYPE_GIF => "image/gif", 
			WEB_TYPE_PNG => "image/png", WEB_TYPE_PDF => "application/pdf", WEB_TYPE_RAR => "application/octet-stream", WEB_TYPE_ZIP => "application/zip", 
			WEB_TYPE_TXT => "text/plain", WEB_TYPE_XML => "text/xml", WEB_TYPE_XSL => "text/plain", WEB_TYPE_DTD => "text/plain",
			WEB_TYPE_HTML => "text/html", WEB_TYPE_HTM => "text/html", WEB_TYPE_PHP => "application/octet-stream", WEB_TYPE_SQL => "text/plain", WEB_TYPE_C => "text/plain",
			WEB_TYPE_CPP => "text/plain", WEB_TYPE_H => "text/plain", WEB_TYPE_JAVA => "text/plain", WEB_TYPE_SWF => "application/x-shockwave-flash",
			WEB_TYPE_MP3 => "audio/mpeg", WEB_TYPE_PSD => "application/octet-stream", WEB_TYPE_DOC => "application/msword", WEB_TYPE_PPT => "application/vnd.ms-powerpoint",
			WEB_TYPE_XLS => "application/vnd.ms-excel", WEB_TYPE_MPEG => "video/mpeg", WEB_TYPE_MOV => "video/quicktime",
			WEB_TYPE_BMP => "image/bmp", WEB_TYPE_AVI => "video/x-msvideo", WEB_TYPE_ICO => "image/x-icon", WEB_TYPE_HTM => "text/html",
			WEB_TYPE_TTF => "font/ttf", WEB_TYPE_WOFF => "font/woff", WEB_TYPE_WOFF2 => "font/woff2", WEB_TYPE_EOT => "application/vnd.ms-fontobject"
		);

		public function __construct() {
			global $webObject;

			parent::setTagLibXml("FileAdmin.xml");
			parent::setLocalizationBundle('fileadmin');
			
			self::transformFileSystem();
		}
		
		protected function canUserDir($objectId, $rightType) {
			return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$DirectoryRightDesc, $objectId, $rightType));
		}
		
		protected function canUserFile($objectId, $rightType) {
			return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$FileRightDesc, $objectId, $rightType));
		}
        
        /**
        *
        *  Generates physical path to dir in fs (/hosting/www/user/filesystem/1/2).
        *  
        *  @param    dirId   dir id
		*  @param    itemPath   Name of field to use to combine path.
        *  @return   physical path to dir in fs
        *
        */                   
        public function getPhysicalPathTo($dirId, $itemPath = '') {
            return USER_FILESYSTEM_PATH . self::getDirectoryPath($dirId, $itemPath);
        }

        /**
        *
        *  Generates URL to dir in fs (/files/1/2).
        *  
		*  @param    dirId   dir id
		*  @param    itemPath   Name of field to use to combine path.
        *  @return   physical path to dir in fs
        *
        */
        public function getPhysicalUrlTo($dirId, $itemPath = '') {
            return USER_FILESYSTEM_URL . self::getDirectoryPath($dirId, $itemPath);
        }
		
        /**
        *
        *  Generates only directory path combine from parents.
        *  
		*  @param    dirId   dir id
		*  @param    itemPath   Name of field to use to combine path.
        *  @return   physical path to dir in fs
        *
        */
		private function getDirectoryPath($dirId, $itemPath = '', $separator = '/') {
			$path = "";
			if ($itemPath == '') {
				$itemPath = FileAdmin::$FileSystemItemPath;
			}
			
			if ($dirId >= 0) {
				while ($dirId != 0) {
					parent::db()->getDataAccess()->disableCache();
					$dirInfo = parent::dao('Directory')->select(parent::select()->where('id', '=', $dirId), false, array($itemPath, 'parent_id'));
					parent::db()->getDataAccess()->enableCache();
					if (count($dirInfo) == 1) {
						$dirId = $dirInfo[0]['parent_id'];
						$path = $dirInfo[0][$itemPath] . $separator . $path;
					} else {
						self::ThrowMissingDirectory();
					}
				}
			} else {
				self::ThrowMissingDirectory();
			}
			
			return $path;
		}

		private function ThrowMissingDirectory() {
			$message = "Directory doesn't exists!";
			echo parent::getError($message);
			trigger_error($message, E_USER_ERROR);
		}
		
		public function getPhysicalPathToFile($file) {
			$path = self::getPhysicalPathTo($file['dir_id']);
			$filePath = $path.$file[FileAdmin::$FileSystemItemPath].".".self::getFileExtension($file);
			return $filePath;
		}
		
		public function getPhysicalUrlToFile($file) {
			$path = $this->getPhysicalUrlTo($file['dir_id']);
			$filePath = $path.$file[FileAdmin::$FileSystemItemPath] . "." . self::getFileExtension($file);
			return $filePath;
		}
		
		public function getWebFileType($fileName) {
			foreach(FileAdmin::$FileExtensions as $key => $ext) {
				$ext = ".".$ext;
				if(strtolower(substr($fileName, strlen($fileName) - strlen($ext))) == $ext) {
					return $key;
				}
			}
			return -1;
		}
		
		public function getFileExtension($file) {
			return self::$FileExtensions[$file['type']];
		}
		
		public function getFileDirectUrl($file) {
			return self::getPhysicalUrlTo($file['dir_id'], false).$file[FileAdmin::$FileSystemItemPath].".".self::$FileExtensions[$file['type']];
		}
		
		/* ================== ADMIN ======================================================= */
		
		private $useRights;
		
		public function canReadDirectory($data) {
			if($data == array() || $data['id'] == 0) {
				return false;
			}
		
			return self::canUserDir($data['id'], WEB_R_READ);
		}
		
		public function canWriteDirectory($data) {
			if($data == array() || $data['id'] == 0) {
				return false;
			}
		
			return self::canUserDir($data['id'], WEB_R_WRITE);
		}
		
		public function canReadFile($data) {
			return self::canUserFile($data['id'], WEB_R_READ);
		}
		
		public function generatePermsForm($data, $objectType, $type) {
			if($type == 'read') {
				$type = WEB_R_READ;
			} elseif($type == 'write') {
				$type = WEB_R_WRITE;
			} elseif($type == 'delete') {
				$type = WEB_R_DELETE;
			}
			
			$parentDirId = null;
			$desc = array();
			if($objectType == 'file') {
				$desc = FileAdmin::$FileRightDesc;
				$parentDirId = $data['dir_id'];
			} elseif($objectType == 'directory') {
				$desc = FileAdmin::$DirectoryRightDesc;
				$parentDirId = $data['parent_id'];
			}
			
			return RoleHelper::getFormPart($desc, $objectType.'-right-', $data['id'], $type, FileAdmin::$DirectoryRightDesc, $parentDirId);
		}
		
		public function canUseRights($data) {
			return $this->useRights;
		}
		
		protected function deleteFile($fileId) {
			if (self::canUserFile($fileId, WEB_R_DELETE)) {
				$file = parent::dao('File')->get($fileId);
				if (!parent::dao('File')->delete($file)) {
					unlink(self::getPhysicalPathToFile($file));
					RoleHelper::deleteRights(FileAdmin::$FileRightDesc, $fileId);
					return true;
				} else {
					return parent::getError(parent::dao('File')->getErrorMessage());
				}
			} else {
				return parent::getError(parent::rb('permissiondenied'));
			}
		}
		
		public function deleteDirectory($directoryId, $recursive = true) {
			if(self::canUserDir($directoryId, WEB_R_DELETE)) {
				$dirs = parent::dao('Directory')->getFromDirectory($directoryId);
				$files = parent::dao('File')->getFromDirectory($directoryId);
				
				if(!$recursive && (count($dirs) > 0 || count($files) > 0)) {
					return parent::getError(parent::rb('dir.notempty'));
				} elseif($recursive) {
					foreach($dirs as $dir) {
						self::deleteDirectory($dir['id'], true);
					}
					foreach($files as $file) {
						self::deleteFile($file['id']);
					}
				}
				
				$directory = parent::dao('Directory')->get($directoryId);
				$path = self::getPhysicalPathTo($directoryId);
				if(!parent::dao('Directory')->delete($directory)) {
					//echo $path;
					rmdir($path);
					RoleHelper::deleteRights(FileAdmin::$DirectoryRightDesc, $directoryId);
					return parent::getSuccess(parent::rb('dir.deleted'));
				} else {
					return parent::getError(parent::dao('Directory')->getErrorMessage());
				}
			} else {
				return parent::getError(parent::rb('permissiondenied'));
			}
		}
		
		public function importFileSystem($rootId, $readRights = null, $writeRights = null, $deleteRights = null) {
			if(!self::canUserDir($rootId, WEB_R_WRITE)) {
				return parent::rb('permissiondenied');
			}
		
			$dirs = parent::dao('Directory')->getFromDirectory($rootId);
			$files = parent::dao('File')->getFromDirectory($rootId);
			
			$dirNames = array();
			foreach($dirs as $dir) {
				$dirNames[count($dirNames)] = $dir[FileAdmin::$FileSystemItemPath];
			}
			
			$fileNames = array();
			foreach($files as $file) {
				$fileNames[count($fileNames)] = $file[FileAdmin::$FileSystemItemPath].'.'.self::getFileExtension($file);
			}
			
			if($readRights == null) {
				$readRights = RoleHelper::getRights(FileAdmin::$DirectoryRightDesc, $rootId, WEB_R_READ);
			}
			if($writeRights == null) {
				$writeRights = RoleHelper::getRights(FileAdmin::$DirectoryRightDesc, $rootId, WEB_R_WRITE);
			}
			if($deleteRights == null) {
				$deleteRights = RoleHelper::getRights(FileAdmin::$DirectoryRightDesc, $rootId, WEB_R_DELETE);
			}
			
			$path = self::getPhysicalPathTo($rootId);
		
			if ($handle = opendir($path)) {
				$newDirs = array();
				$newFiles = array();
			
				$itemsList = array();
				while (false !== ($entry = readdir($handle))) {
					$itemsList[] = array('name' => $entry, 'mtime' => filemtime($path.$entry));
				}
				closedir($handle);
				usort($itemsList, 'CompareFileImport');
				
				foreach($itemsList as $item) {
					$entry = $item['name'];
					if(is_file($path.$entry)) {
						if(in_array($entry, $fileNames)) {
							continue;
						}
						
						$info = pathinfo($path.$entry);
						
						if(self::getWebFileType($info['basename']) == -1) {
							continue;
						}
						
						$dataItem = array(
							'name' => $info['filename'], 
							'type' => self::getWebFileType($info['basename']), 
							'dir_id' => $rootId, 
							'timestamp' => time(), 
							'url' => UrlUtils::toValidUrl($info['filename'])
						);
						
						if(parent::dao('File')->insert($dataItem) != 0) {
							continue;
						}
						$dataItem['id'] = parent::dao('File')->getLastId();
						
						//Rename to match filesystem item path
						self::transformFileSystemFiles(array($dataItem));
						array_push($newFiles, $dataItem);
						
						RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $readRights, WEB_R_READ);
						RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $writeRights, WEB_R_WRITE);
						RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $deleteRights, WEB_R_DELETE);
						
						//echo 'Imported file: '.$path.$entry.'<br />';
					} else {
						if(in_array($entry, $dirNames) || $entry == '.' || $entry == '..') {
							continue;
						}
					
						$dataItem = array(
							'name' => $entry, 
							'url' => UrlUtils::toValidUrl($entry), 
							'parent_id' => $rootId, 
							'timestamp' => time()
						);
						
						if(parent::dao('Directory')->insert($dataItem) != 0) {
							continue;
						}
						$dataItem['id'] = parent::dao('Directory')->getLastId();
						
						//Rename to match filesystem item path
						self::transformFileSystemDirs(array($dataItem));
						array_push($newDirs, $dataItem);
						
						RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $readRights, WEB_R_READ);
						RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $writeRights, WEB_R_WRITE);
						RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $deleteRights, WEB_R_DELETE);
					
						//echo 'Imported folder: '.$path.$entry.'<br />';
						parent::db()->getDataAccess()->disableCache();
						self::importFileSystem($dataItem['id'], $readRights, $writeRights, $deleteRights);
						parent::db()->getDataAccess()->enableCache();
					}
				}
				
				//self::transformFileSystemDirs($newDirs);
				//self::transformFileSystemFiles($newFiles);
			}
		}
		
		//C-tag
		public function fileBrowser($dirId, $browsable, $useFrames, $orderBy = "name") {
			$return = '';
		
			if($browsable) {
				if(array_key_exists('dir-id', $_POST)) {
					$dirId = $_POST['dir-id'];
				}
			}
			
			if($_POST['delete-file'] == parent::rb('file.delete')) {
				$fileId = $_POST['file-id'];
				$message = self::deleteFile($fileId);
				if ($message === true) {
					$message = parent::getSuccess(parent::rb('file.deleted'));
				}

				$return .= $message;
			}
			
			if($_POST['delete-dir']  == parent::rb('dir.delete')) {
				$directoryId = $_POST['directory-id'];
				$return .= self::deleteDirectory($directoryId, true);
			}

			if ($_POST['new-import'] == parent::rb('button.import')) {
				$result = self::importFileSystem($dirId);
				if($result != null) {
					$return .= parent::getError($result);
				} else {
					$return .= parent::getSuccess(parent::rb('message.imported'));
				}
			}
			
			$parentDir = parent::dao('Directory')->get($dirId);
			if ($dirId == 0) {
				$parentDir = array('id' => $dirId);
			}
			
			$dirs = parent::dao('Directory')->getFromDirectory($dirId, $orderBy);
			$files = parent::dao('File')->getFromDirectory($dirId, $orderBy);
			$dataModel = array('files' => $files, 'dirs' => $dirs, 'parent' => $parentDir);
			
			if($useFrames) {
				return parent::getFrame(parent::rb('title.browser').' :: '.self::getDirectoryPath($dirId, 'name'), $return . parent::view('fileadmin-list', $dataModel), true);
			} else {
				return $return.parent::view('fileadmin-list', $dataModel);
			}
		}

		public function fileBrowserWithTemplate($template, $dirId, $grouped = true, $parentName = "", $orderBy = "name") {
			$dirs = parent::dao('Directory')->getFromDirectory($dirId, $orderBy);
			$files = parent::dao('File')->getFromDirectory($dirId, $orderBy);

			$resultDirs = array();
			foreach ($dirs as $key => $dir) {
				if (self::canReadDirectory($dir)) {
					$item = array(
						"id" => $dir["id"],
						"name" => $dir["name"],
						"url" => $dir["url"],
						"timestamp" => $dir["timestamp"],
						"type" => 0,
						"title" => ""
					);
					$resultDirs[] = $item;
				}
			}

			$resultFiles = array();
			foreach ($files as $key => $file) {
				if (self::canReadFile($file)) {
					$item = array(
						"id" => $file["id"],
						"name" => $file["name"],
						"url" => $file["url"],
						"timestamp" => $file["timestamp"],
						"type" => $file["type"],
						"title" => $file["title"],
					);
					$resultFiles[] = $item;
				}
			}

			$parentResult = array();
			if ($parentName != "") {
				$parentId = parent::dao('Directory')->getParentId($dirId);
				if ($parentId != null) {
					$parent = parent::dao('Directory')->get($parentId);
				} else {
					$parentId = 0;
					$parent = array();
				}
				$parent = array(
					"id" => $parentId,
					"name" => $parentName,
					"url" => $parent["url"],
					"timestamp" => $$parent["timestamp"],
					"type" => 0,
					"title" => "",
				);
				$parentResult[] = $parent;
			}

			$items = array_merge($parentResult, $resultDirs, $resultFiles);
			if ($grouped == false) {
				usort($items, function($a, $b) use ($orderBy) { return strcmp($a[$orderBy], $b[$orderBy]); });
			}

			$model = new ListModel();
			self::pushListModel($model);
			
			$model->items($items);
			$model->render();
            $result = $template();

            self::popListModel();
            return $result;
		}

		public function getFileBrowserListData() {
			return self::peekListModel();
		}

		public function getFileBrowserItemId() {
			return self::peekListModel()->field("id");
		}

		public function getFileBrowserItemName() {
			return self::peekListModel()->field("name");
		}

		public function getFileBrowserItemType() {
			return self::peekListModel()->field("type");
		}

		public function getFileBrowserItemExtension() {
			$type = self::peekListModel()->field("type");
			if ($type == 0) {
				return "";
			}

			return self::$FileExtensions[$type];
		}

		public function getFileBrowserItemTitle() {
			return self::peekListModel()->field("title");
		}

		public function getFileBrowserItemTimestamp() {
			return self::peekListModel()->field("timestamp");
		}
		
		public function processFileUploadBasic($dataItem, $fileTmpName) {
			$read = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $dataItem['dir_id'], WEB_R_READ);
			$write = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $dataItem['dir_id'], WEB_R_WRITE);
			$delete = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $dataItem['dir_id'], WEB_R_DELETE);
			
			return self::processFileUpload($dataItem, $fileTmpName, $read, $write, $delete);
		}
		
		public function processFileUpload($dataItem, $fileTmpName, $readRights, $writeRights, $deleteRights) {
			$new = $dataItem['id'] == '';
			
			if ($dataItem['url'] == '') {
				$dataItem['url'] = UrlUtils::toValidUrl($dataItem['name']);
			}
			
			if($fileTmpName == '') {
				$fileTmpName = null;
			}
			
			if($new) {
				if(!self::canUserDir($dataItem['dir_id'], WEB_R_WRITE)) {
					return parent::rb('permissiondenied');
				}
			} else {
				if(!self::canUserFile($dataItem['id'], WEB_R_WRITE)) {
					return parent::rb('permissiondenied');
				}
			}	
			
			$file = array();
			if(!$new) {
				$file = parent::dao('File')->get($dataItem['id']);
				$dataItem['type'] = $file['type'];
			}
			
			if($dataItem['type'] == -1) {
				return parent::rb('file.unsupportedtype');
			}
			
			if(strlen($dataItem['name']) == 0) {
				return parent::rb('file.namelength');
			}
			
			$select = parent::select()->where('dir_id', '=', $dataItem['dir_id'])->conjunct('name', '=', $dataItem['name'])->conjunct('type', '=', $dataItem['type']);
			if(!$new) {
				$select = $select->conjunct('id', '!=', $dataItem['id']);
			}
			
			$existing = parent::dao('File')->select($select);
			if(count($existing) > 0) {
				return parent::rb('file.notuniquename').' "'.$dataItem['name'].'.'.self::getFileExtension($dataItem).'"';
			}
			
			if($new && $fileTmpName == null) {
				return parent::rb('file.nofile');
			}
			
			if(!$new) {
				if($fileTmpName != null) {
					unlink(self::getPhysicalPathToFile($file));
				} else {
					//rename(self::getPhysicalPathToFile($file), self::getPhysicalPathToFile($dataItem));
				}
			}
			
			if($fileTmpName != null) {
				//echo self::getPhysicalPathToFile($dataItem);			
				if($new) {
					if(parent::dao('File')->insert($dataItem) != 0) {
						return parent::dao('File')->getErrorMessage();
					}
					$dataItem['id'] = parent::dao('File')->getLastId();
				}
				$moved = move_uploaded_file($fileTmpName, self::getPhysicalPathToFile($dataItem));
				//TODO: Show error!
			}
			
			if(!$new) {
				if(parent::dao('File')->update($dataItem) != 0) {
					return parent::dao('File')->getErrorMessage();
				}
			}
			
			RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $readRights, WEB_R_READ);
			RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $writeRights, WEB_R_WRITE);
			RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $deleteRights, WEB_R_DELETE);
			
			return null;
		}
		
		protected function zipOutFile($dirId, $fileTmpName, $readRights, $writeRights, $deleteRights) {
			//echo $fileTmpName.'<br />';
			
			$zip = new ZipArchive();
			if ($zip->open($fileTmpName) === TRUE) {
				$zip->extractTo(self::getPhysicalPathTo($dirId));
				$zip->close();
				
				parent::db()->getDataAccess()->disableCache();
				self::importFileSystem($dirId, $readRights, $writeRights, $deleteRights);
				parent::db()->getDataAccess()->enableCache();
			} else {
				return parent::rb('message.cantextract');
			}
		}
		
		//C-tag
		public function fileUpload($dirId = false, $pageId = "", $namePrefix = "", $useRights = false, $useFrames = false, $isStandalone = false) {
			$this->useRights = $useRights;
			
			$return = "";

			if ($namePrefix != "") {
				$namePrefix = "$namePrefix-";
			}

			if (($dirId == '' || $dirId == 0) && array_key_exists('dir-id', $_POST)) {
				$dirId = $_POST['dir-id'];
			}
			
			if ($_POST[$namePrefix . 'file-save'] == parent::rb('button.save')) {
				$parentId = $_POST[$namePrefix . 'dir-id'];
			
				$read = $_POST[$namePrefix . 'file-right-r'];
				if($read == array()) {
					$read = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_READ);
				}
				$write = $_POST[$namePrefix . 'file-right-w'];
				if($write == array()) {
					$write = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_WRITE);
				}
				$delete = $_POST[$namePrefix . 'file-right-d'];
				if($delete == array()) {
					$delete = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_DELETE);
				}
					
				if(!$_POST[$namePrefix . 'zip-out']) {
					$fileNames = $_POST[$namePrefix . 'file-name'];
					
					foreach($fileNames as $i => $name) {
						$requestFileName = $_FILES[$namePrefix . 'file-upload']['name'][$i];

						$dataItem = array(
							'id' => $_POST[$namePrefix . 'file-id'], 
							'name' => $name, 
							'title' => $_POST[$namePrefix . 'file-title'][$i], 
							'dir_id' => $parentId, 
							'type' => self::getWebFileType($requestFileName), 
							'timestamp' => time(),
							'url' => $_POST[$namePrefix . 'file-url'][$i]
						);
						
						//print_r($read);
						//print_r($write);
						//print_r($delete);
						$result = self::processFileUpload($dataItem, $_FILES[$namePrefix . 'file-upload']['tmp_name'][$i], $read, $write, $delete);
						if ($result != null) {
							$_POST[$namePrefix . 'new-file'] = parent::rb('button.newfile');
							$return .= parent::getError($result);
						} else if(!empty($pageId)) {
							parent::web()->redirectTo($pageId);
						}
					}
				} else {
					self::zipOutFile($_POST[$namePrefix . 'dir-id'], $_FILES[$namePrefix . 'file-upload']['tmp_name'][0], $read, $write, $delete);
				}
			}
			
			if ($_POST[$namePrefix . 'new-file'] == parent::rb('button.newfile') || $_POST[$namePrefix . 'new-zipfile'] == parent::rb('button.newzipfile') || $_POST[$namePrefix . 'edit-file'] == parent::rb('file.edit') || $isStandalone) {
				
				$dataItem = array('name' =>  'file'.rand(1000, 9999).rand(1000, 9999), 'dir_id' => $dirId);
				if (array_key_exists($namePrefix . 'file-id', $_POST)) {
					$fileId = $_POST[$namePrefix . 'file-id'];
					$dataItem = parent::dao('File')->get($fileId);

					if($dataItem['dir_id'] == '') {
						$dataItem['dir_id'] = $dirId;
					}
				}
				$dataItem["form-id"] = "fa-upload-" . rand(1, 1000);
				$dataItem["name-prefix"] = $namePrefix;
				$dataItem['zip-out'] = ($_POST[$namePrefix . 'new-zipfile'] == parent::rb('button.newzipfile'));
			
				if($useFrames) {
					return parent::getFrame(parent::rb('title.fileupload'), $return.parent::view('fileadmin-fileupload', $dataItem), true);
				} else {
					return $return.parent::view('fileadmin-fileupload', $dataItem);
				}	
			}
		}

		public function uploadFormTag($template, int $dirId) {
			$model = parent::getEditModel();

			if ($model->isSubmit()) {
				$template();

				$hasAccess = true;
				if(!$this->canUserDir($dirId, WEB_R_WRITE)) {
					$hasAccess = false;
				}

				$this->forEachModelFile($model, function($file, $key) use ($model, $hasAccess, $dirId) {
					if (!$hasAccess) {
						$model->validationMessage($key, parent::rb('permissiondenied'));
					}

					if (strlen($file->Name) == 0) {
						$model->validationMessage($key, parent::rb('file.namelength'));
					}

					$type = $this->getWebFileType($file->Name);
					if ($type == -1) {
						$model->validationMessage($key, parent::rb('file.unsupportedtype'));
					}

					$name = $this->getFileNameWithoutExtension($file->Name);
					$select = parent::select()->where('dir_id', '=', $dirId)->conjunct('name', '=', $name)->conjunct('type', '=', $type);
					$existing = parent::dao('File')->select($select);
					if (count($existing) > 0) {
						$model->validationMessage($key, parent::rb('file.notuniquename').' "' . $file->Name . '"');
					}
				});
			} else if ($model->isSave()) {
				$this->forEachModelFile($model, function($file) use ($dirId) {
					$dataItem = $this->mapFileUploadModelToDataItem($file, $dirId);
					$result = $this->processFileUploadBasic($dataItem, $file->TempName);
					if ($result != null) {
						throw new Error($result);
					}
				});
			} else if ($model->isRender()) {
				$result = $template();
				return $result;
			} else {
				$template();
			}
		}

		private function forEachModelFile($model, $handler) {
			foreach ($model->keys() as $key) {
				$files = $model[$key];
				if (is_array($files)) {
					foreach ($files as $file) {
						$handler($file, $key);
					}
				} else {
					$handler($files, $key);
				}
			}
		}

		private function mapFileUploadModelToDataItem(FileUploadModel $file, int $dirId) {
			$name = $this->getFileNameWithoutExtension($file->Name);
			$dataItem = [
				"name" => $name,
				"title" => $name,
				"type" => $this->getWebFileType($file->Name),
				'timestamp' => time(), 
				'url' => UrlUtils::toValidUrl($name),
				"dir_id" => $dirId
			];
			return $dataItem;
		}

		private function getFileNameWithoutExtension($name) {
			return implode('.', explode('.', $name, -1));
		}

		public function createDirectory($parentId, $name, $url = '') {
			$read = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_READ);
			$write = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_WRITE);
			$delete = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_DELETE);
			
			$dataItem = array('id' => '', 'name' => $name, 'url' => $url, 'parent_id' => $parentId, 'timestamp' => time());
			$result = self::processDirectoryEdit($dataItem, $read, $write, $delete);
			$dataItem['id'] = $_POST['new-directory-id'];

			return $dataItem;
		}
		
		protected function processDirectoryEdit($dataItem, $readRights, $writeRights, $deleteRights) {
			$new = $dataItem['id'] == '';
			
			if ($dataItem['url'] == '') {
				$dataItem['url'] = UrlUtils::toValidUrl($dataItem['name']);
			}
			
			if($new) {
				if(!self::canUserDir($dataItem['parent_id'], WEB_R_WRITE)) {
					return parent::rb('permissiondenied');
				}
			} else {
				if(!self::canUserDir($dataItem['id'], WEB_R_WRITE)) {
					return parent::rb('permissiondenied');
				}
			}
			
			$dir = array();
			if(!$new) {
				$dir = parent::dao('Directory')->get($dataItem['id']);
			}
			
			if(strlen($dataItem['name']) == 0) {
				return parent::rb('file.namelength');
			}
			
			$select = parent::select()->where('parent_id', '=', $dataItem['parent_id'])->conjunct('name', '=', $dataItem['name']);
			if(!$new) {
				$select = $select->conjunct('id', '!=', $dataItem['id']);
			}
			
			$existing = parent::dao('Directory')->select($select);
			if(count($existing) > 0) {
				return parent::rb('dir.notuniquename').' "'.$dataItem['name'].'"';
			}
			
			if(!$new) {
				$path = self::getPhysicalPathTo($dir['parent_id']);
				//rename($path.$dir['name'], $path.$dataItem['name']);
				
				if(parent::dao('Directory')->update($dataItem) != 0) {
					return parent::dao('Directory')->getErrorMessage();
				}
			} else {
				if(parent::dao('Directory')->insert($dataItem) != 0) {
					return parent::dao('Directory')->getErrorMessage();
				}
				$dataItem['id'] = parent::dao('Directory')->getLastId();
				$_POST['new-directory-id'] = $dataItem['id'];
				
				$path = self::getPhysicalPathTo($dataItem['parent_id']).$dataItem[FileAdmin::$FileSystemItemPath];
				mkdir($path);
			}
			
			RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $readRights, WEB_R_READ);
			RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $writeRights, WEB_R_WRITE);
			RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $deleteRights, WEB_R_DELETE);
			
			return null;
		}
		
		//C-tag
		public function directoryEditor($useRights = false, $useFrames = false) {
			$this->useRights = $useRights;

			$return = "";
			
			if(array_key_exists('dir-id', $_POST)) {
				$dirId = $_POST['dir-id'];
			}
			
			if($_POST['directory-save'] == parent::rb('button.save')) {
				$parentId = $_POST['dir-id'];
				
				$read = $_POST['directory-right-r'];
				if($read == array()) {
					$read = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_READ);
				}
				$write = $_POST['directory-right-w'];
				if($write == array()) {
					$write = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_WRITE);
				}
				$delete = $_POST['directory-right-d'];
				if($delete == array()) {
					$delete = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $parentId, WEB_R_DELETE);
				}
				
				
				$dataItem = array('id' => $_POST['directory-id'], 'name' => $_POST['dir-name'], 'url' => $_POST['dir-url'], 'parent_id' => $parentId, 'timestamp' => time());
				
				//print_r($read);
				//print_r($write);
				//print_r($delete);
				$result = self::processDirectoryEdit($dataItem, $read, $write, $delete);
				if($result != null) {
					$_POST['new-directory'] = parent::rb('button.newdirectory');
					$return .= parent::getError($result);
				}
			}
			
			if($_POST['new-directory'] == parent::rb('button.newdirectory') || $_POST['edit-dir'] == parent::rb('dir.edit')) {
				
				$dataItem = array('parent_id' => $dirId);
				if(array_key_exists('directory-id', $_POST)) {
					$directoryId = $_POST['directory-id'];
					$dataItem = parent::dao('Directory')->get($directoryId);
				}
			
				if($useFrames) {
					return parent::getFrame(parent::rb('title.directory'), $return.parent::view('fileadmin-directory', $dataItem), true);
				} else {
					return $return.parent::view('fileadmin-fileupload', $dataItem);
				}	
			}
		}

		//C-fulltag
		public function fileDeleter($template, $id) {
			if (self::deleteFile($id) === true) {
				$template();
			}
		}
		
		protected function transformFileSystem() {
			$transformed = parent::getSystemProperty('FileAdmin.fileSystemTransformed');
			if(!$transformed) {
				self::transformSubFileSystem(0);
				parent::setSystemProperty('FileAdmin.fileSystemTransformed', 1);
			}
		}
		
		protected function transformSubFileSystem($dirId) {
			$dirs = parent::dao('Directory')->getFromDirectory($dirId);
			self::transformFileSystemDirs($dirs);
			
			$files = parent::dao('File')->getFromDirectory($dirId);
			self::transformFileSystemFiles($files);
		}
		
		protected function transformFileSystemDirs($dirs) {
			foreach($dirs as $dir) {
				$path = self::getPhysicalPathTo($dir['parent_id']);
				$oldPath = $path.$dir['name'];
				$newPath = $path.$dir[FileAdmin::$FileSystemItemPath];
				
				//echo $oldPath.' => '.$newPath.'<br />';
				$result = rename($oldPath, $newPath);
				//echo $result ? "Ok" : "Failed";
				self::transformSubFileSystem($dir['id']);
			}
		}
		
		protected function transformFileSystemFiles($files) {
			foreach($files as $file) {
				$path = self::getPhysicalPathTo($file['dir_id']);
				$oldPath = $path.$file['name'].".".self::getFileExtension($file);
				$newPath = self::getPhysicalPathToFile($file);
				
				//echo $oldPath.' => '.$newPath.'<br />';
				rename($oldPath, $newPath);
			}
		}

		private $directoryPathWithTemplate;

		public function directoryPathWithTemplate(callable $template, $dirId, $display = "name", $separator = "/") {
			$oldPath = $this->directoryPathWithTemplate;
			
			$this->directoryPathWithTemplate = $this->getDirectoryPath($dirId, $display, $separator);
			$result = $template();

			$this->directoryPathWithTemplate = $oldPath;
			
			return $result;
		}

		public function getDirectoryPathWithTemplate() {
			return $this->directoryPathWithTemplate;
		}

		private $fileDirectUrlWithTemplate;

		public function fileDirectUrlWithTemplate(callable $template, $fileId) {
			$result = "";

			if ($this->canUserFile($fileId, WEB_R_READ)) {
				$oldPath = $this->fileDirectUrlWithTemplate;

				$file = parent::dao('File')->get($fileId);
				$this->fileDirectUrlWithTemplate = $this->getPhysicalUrlToFile($file);
				$result = $template();

				$this->fileDirectUrlWithTemplate = $oldPath;
			}

			return $result;
		}

		public function getFileDirectUrlWithTemplate() {
			return $this->fileDirectUrlWithTemplate;
		}

		public function fileSwapOrder(callable $template, $id1, $id2) {
			if ($this->canUserFile($id1, WEB_R_WRITE) && $this->canUserFile($id2, WEB_R_WRITE)) {
				$dao = parent::dao('File');
				$file1 = $dao->get($id1);
				$file2 = $dao->get($id2);

				if ($file1["dir_id"] == $file2["dir_id"]) {
					$order = $file1["order"];
					$file1["order"] = $file2["order"];
					$file2["order"] = $order;

					$this->dataAccess()->transaction(function() use ($dao, $file1, $file2) {
						$dao->update($file1);
						$dao->update($file2);
					});

					$template();
				}
			}
		}

		public function directorySwapOrder(callable $template, $id1, $id2) {
			if ($this->canUserDir($id1, WEB_R_WRITE) && $this->canUserDir($id2, WEB_R_WRITE)) {
				$dao = parent::dao('Directory');
				$dir1 = $dao->get($id1);
				$dir2 = $dao->get($id2);

				if ($dir1["parent_id"] == $dir2["parent_id"]) {
					$order = $dir1["order"];
					$dir1["order"] = $dir2["order"];
					$dir2["order"] = $order;

					$this->dataAccess()->transaction(function() use ($dao, $dir1, $dir2) {
						$dao->update($dir1);
						$dao->update($dir2);
					});

					$template();
				}
			}
		}
	}

	function CompareFileImport($a, $b)
	{
		if ($a == $b) {
			return 0;
		}
		return ($a > $b) ? -1 : 1;
	}

?>