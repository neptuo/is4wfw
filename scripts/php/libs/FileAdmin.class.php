<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");

require_once("scripts/php/classes/ResourceBundle.class.php");
require_once("scripts/php/classes/dataaccess/Select.class.php");
require_once("scripts/php/classes/RoleHelper.class.php");

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

	public static $FileExtensions = array(
		WEB_TYPE_CSS => "css", WEB_TYPE_JS => "js", WEB_TYPE_JPG => "jpg", WEB_TYPE_GIF => "gif", 
        WEB_TYPE_PNG => "png", WEB_TYPE_PDF => "pdf", WEB_TYPE_RAR => "rar", WEB_TYPE_ZIP => "zip", 
        WEB_TYPE_TXT => "txt", WEB_TYPE_XML => "xml", WEB_TYPE_XSL => "xsl", WEB_TYPE_DTD => "dtd",
		WEB_TYPE_HTML => "html", WEB_TYPE_PHP => "php", WEB_TYPE_SQL => "sql", WEB_TYPE_C => "c",
		WEB_TYPE_CPP => "cpp", WEB_TYPE_H => "h", WEB_TYPE_JAVA => "java", WEB_TYPE_SWF => "swf",
		WEB_TYPE_MP3 => "mp3", WEB_TYPE_PSD => "psd", WEB_TYPE_DOC => "doc", WEB_TYPE_PPT => "ppt",
		WEB_TYPE_XLS => "xls", WEB_TYPE_MPEG => "mpeg", WEB_TYPE_MOV => "mov",
		WEB_TYPE_BMP => "bmp", WEB_TYPE_AVI => "avi", WEB_TYPE_ICO => "ico", WEB_TYPE_HTM => "htm"
	);
	
	public static $FileMimeTypes = array(
		WEB_TYPE_CSS => "text/css", WEB_TYPE_JS => "application/x-javascript", WEB_TYPE_JPG => "image/jpeg", WEB_TYPE_GIF => "image/gif", 
        WEB_TYPE_PNG => "image/png", WEB_TYPE_PDF => "application/pdf", WEB_TYPE_RAR => "application/octet-stream", WEB_TYPE_ZIP => "application/zip", 
        WEB_TYPE_TXT => "text/plain", WEB_TYPE_XML => "text/xml", WEB_TYPE_XSL => "text/plain", WEB_TYPE_DTD => "text/plain",
        WEB_TYPE_HTML => "text/html", WEB_TYPE_HTM => "text/html", WEB_TYPE_PHP => "application/octet-stream", WEB_TYPE_SQL => "text/plain", WEB_TYPE_C => "text/plain",
        WEB_TYPE_CPP => "text/plain", WEB_TYPE_H => "text/plain", WEB_TYPE_JAVA => "text/plain", WEB_TYPE_SWF => "application/x-shockwave-flash",
		WEB_TYPE_MP3 => "audio/mpeg", WEB_TYPE_PSD => "application/octet-stream", WEB_TYPE_DOC => "application/msword", WEB_TYPE_PPT => "application/vnd.ms-powerpoint",
		WEB_TYPE_XLS => "application/vnd.ms-excel", WEB_TYPE_MPEG => "video/mpeg", WEB_TYPE_MOV => "video/quicktime",
		WEB_TYPE_BMP => "image/bmp", WEB_TYPE_AVI => "video/x-msvideo", WEB_TYPE_ICO => "image/x-icon"
	);

    public function __construct() {
        global $webObject;

        parent::setTagLibXml("xml/FileAdmin.xml");
		parent::loadResourceBundle('fileadmin');
    }
	
	protected function canUserDir($objectId, $rightType) {
		return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$DirectoryRightDesc, $objectId, $rightType));
	}
	
	protected function canUserFile($objectId, $rightType) {
		return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$FileRightDesc, $objectId, $rightType));
	}
	
	public function getPhysicalPathTo($dirId, $notUserFsRoot = false) {
		$path = "";
		if($dirId >= 0) {
			while($dirId != 0) {
				parent::db()->getDataAccess()->disableCache();
				$dirInfo = parent::dao('Directory')->select(Select::factory()->where('id', '=', $dirId)->result(), false, array('name', 'parent_id'));
				parent::db()->getDataAccess()->enableCache();
				if(count($dirInfo) == 1) {
					$dirId = $dirInfo[0]['parent_id'];
					$path = $dirInfo[0]['name'].'/'.$path;
				} else {
					$message = "Directory doesn't exists!";
					echo "<h4 class=\"error\">".$message."</h4>";
					trigger_error($message, E_USER_ERROR);
				}
			}
		} else {
			$message = "Directory doesn't exists!";
			echo "<h4 class=\"error\">".$message."</h4>";
			trigger_error($message, E_USER_ERROR);
		}
      
		if(!$notUserFsRoot) {
			$path = UrlResolver::combinePath(UrlResolver::parseScriptRoot($_SERVER['SCRIPT_NAME'], 'file.php'), FS_ROOT.$path);
		}
		return $path;
    }
	
	public function getPhysicalPathToFile($file) {
		$path = self::getPhysicalPathTo($file['dir_id']);
		$filePath = $_SERVER['DOCUMENT_ROOT'].$path.$file['name'].".".self::getFileExtension($file);
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
		return self::getPhysicalPathTo($file['dir_id'], false).$file['name'].".".self::$FileExtensions[$file['type']];
	}
	
	/* ================== ADMIN ======================================================= */
	
	private $useRights;
	
	public function canReadDirectory($data) {
		if($data == array() || $data['id'] == 0) {
			return false;
		}
	
		return self::canUserDir($data['id'], WEB_R_READ);
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
		if(self::canUserFile($fileId, WEB_R_DELETE)) {
			$file = parent::dao('File')->get($fileId);
			if(!parent::dao('File')->delete($file)) {
				unlink(self::getPhysicalPathToFile($file));
				RoleHelper::deleteRights(FileAdmin::$FileRightDesc, $fileId);
				return parent::getSuccess(parent::rb('file.deleted'));
			} else {
				return parent::getError(parent::dao('File')->getErrorMessage());
			}
		} else {
			return parent::getError(parent::rb('permissiondenied'));
		}
	}
	
	protected function deleteDirectory($directoryId, $recursive = true) {
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
			$path = $_SERVER['DOCUMENT_ROOT'].self::getPhysicalPathTo($directoryId);
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
	
	protected function importFileSystem($rootId, $readRights = null, $writeRights = null, $deleteRights = null) {
		if(!self::canUserDir($rootId, WEB_R_WRITE)) {
			return parent::rb('permissiondenied');
		}
	
		$dirs = parent::dao('Directory')->getFromDirectory($rootId);
		$files = parent::dao('File')->getFromDirectory($rootId);
		
		$dirNames = array();
		foreach($dirs as $dir) {
			$dirNames[count($dirNames)] = $dir['name'];
		}
		
		$fileNames = array();
		foreach($files as $file) {
			$fileNames[count($fileNames)] = $file['name'].'.'.self::getFileExtension($file);
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
		
		$path = $_SERVER['DOCUMENT_ROOT'].self::getPhysicalPathTo($rootId);
	
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if(is_file($path.$entry)) {
					if(in_array($entry, $fileNames)) {
						continue;
					}
					
					$info = pathinfo($path.$entry);
					
					if(self::getWebFileType($info['basename']) == -1) {
						continue;
					}
					
					$dataItem = array('name' => $info['filename'], 'type' => self::getWebFileType($info['basename']), 'dir_id' => $rootId, 'timestamp' => time());
					
					if(parent::dao('File')->insert($dataItem) != 0) {
						continue;
					}
					$dataItem['id'] = parent::dao('File')->getLastId();
					
					RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $readRights, WEB_R_READ);
					RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $writeRights, WEB_R_WRITE);
					RoleHelper::setRights(FileAdmin::$FileRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $deleteRights, WEB_R_DELETE);
					
					//echo 'Imported file: '.$path.$entry.'<br />';
				} else {
					if(in_array($entry, $dirNames) || $entry == '.' || $entry == '..') {
						continue;
					}
				
					$dataItem = array('name' => $entry, 'url' => strtolower(parent::convertToUrlValid($entry)), 'parent_id' => $rootId, 'timestamp' => time());
					
					if(parent::dao('Directory')->insert($dataItem) != 0) {
						continue;
					}
					$dataItem['id'] = parent::dao('Directory')->getLastId();
					
					RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $readRights, WEB_R_READ);
					RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $writeRights, WEB_R_WRITE);
					RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $deleteRights, WEB_R_DELETE);
				
					//echo 'Imported folder: '.$path.$entry.'<br />';
					parent::db()->getDataAccess()->disableCache();
					self::importFileSystem($dataItem['id'], $readRights, $writeRights, $deleteRights);
					parent::db()->getDataAccess()->enableCache();
				}
			}
		}
	}
	
	//C-tag
	public function fileBrowser($dirId, $browsable, $useFrames) {
		$return = '';
	
		if($browsable) {
			if(array_key_exists('dir-id', $_POST)) {
				$dirId = $_POST['dir-id'];
			}
		}
		
		if($_POST['delete-file'] == parent::rb('file.delete')) {
			$fileId = $_POST['file-id'];
			$return .= self::deleteFile($fileId);
		}
		
		if($_POST['delete-dir']  == parent::rb('dir.delete')) {
			$directoryId = $_POST['directory-id'];
			$return .= self::deleteDirectory($directoryId, true);
		}

		if($_POST['new-import'] == parent::rb('button.import')) {
			$result = self::importFileSystem($dirId);
			if($result != null) {
				$return .= parent::getError($result);
			} else {
				$return .= parent::getSuccess(parent::rb('message.imported'));
			}
		}
		
		$parentDir = parent::dao('Directory')->get($dirId);
		if($dirId == 0) {
			$parentDir = array('id' => $dirId);
		}
		
		$dirs = parent::dao('Directory')->getFromDirectory($dirId);
		$files = parent::dao('File')->getFromDirectory($dirId);
		$dataModel = array('files' => $files, 'dirs' => $dirs, 'parent' => $parentDir);
		
		if($useFrames) {
			return parent::getFrame(parent::rb('title.browser').' :: /'.self::getPhysicalPathTo($dirId, true), $return.parent::view('fileadmin-list', $dataModel), true);
		} else {
			return $return.parent::view('fileadmin-list', $dataModel);
		}
	}
	
	protected function processFileUpload($dataItem, $fileTmpName, $readRights, $writeRights, $deleteRights) {
		$new = $dataItem['id'] == '';
		
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
		
		$select = Select::factory()->where('dir_id', '=', $dataItem['dir_id'])->conjunct('name', '=', $dataItem['name'])->conjunct('type', '=', $dataItem['type']);
		if(!$new) {
			$select = $select->conjunct('id', '!=', $dataItem['id']);
		}
		
		$existing = parent::dao('File')->select($select->result());
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
				rename(self::getPhysicalPathToFile($file), self::getPhysicalPathToFile($dataItem));
			}
		}
		
		if($fileTmpName != null) {
			//echo self::getPhysicalPathToFile($dataItem);
			$moved = move_uploaded_file($fileTmpName, self::getPhysicalPathToFile($dataItem));
			if($moved) {
				if($new) {
					if(parent::dao('File')->insert($dataItem) != 0) {
						return parent::dao('File')->getErrorMessage();
					}
					$dataItem['id'] = parent::dao('File')->getLastId();
				}
			}
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
			$zip->extractTo($_SERVER['DOCUMENT_ROOT'].self::getPhysicalPathTo($dirId));
			$zip->close();
			
			parent::db()->getDataAccess()->disableCache();
			self::importFileSystem($dirId, $readRights, $writeRights, $deleteRights);
			parent::db()->getDataAccess()->enableCache();
		} else {
			return parent::rb('message.cantextract');
		}
	}
	
	//C-tag
	public function fileUpload($dirId = false, $useRights = false, $useFrames = false) {
		$this->useRights = $useRights;
		
		//print_r($_POST);
		
		if(array_key_exists('dir-id', $_POST)) {
			$dirId = $_POST['dir-id'];
		}
		
		if($_POST['file-save'] == parent::rb('button.save')) {
			$read = $_POST['file-right-r'];
			if($read == array()) {
				$read = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $_POST['dir_id'], WEB_R_READ);
			}
			$write = $_POST['file-right-w'];
			if($write == array()) {
				$write = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $_POST['dir_id'], WEB_R_WRITE);
			}
			$delete = $_POST['file-right-d'];
			if($delete == array()) {
				$delete = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $_POST['dir_id'], WEB_R_DELETE);
			}
				
			if(!$_POST['zip-out']) {
				$fileNames = $_POST['file-name'];
				
				foreach($fileNames as $i => $name) {
					$dataItem = array('id' => $_POST['file-id'], 'name' => $name, 'title' => $_POST['file-title'][$i], 'dir_id' => $_POST['dir-id'], 'type' => self::getWebFileType($_FILES['file-upload']['name'][$i]), 'timestamp' => time());
					
					$result = self::processFileUpload($dataItem, $_FILES['file-upload']['tmp_name'][$i], $read, $write, $delete);
					if($result != null) {
						$_POST['new-file'] = parent::rb('button.newfile');
						$return .= parent::getError($result);
					}
				}
			} else {
				self::zipOutFile($_POST['dir-id'], $_FILES['file-upload']['tmp_name'][0], $read, $write, $delete);
			}
		}
		
		if($_POST['new-file'] == parent::rb('button.newfile') || $_POST['new-zipfile'] == parent::rb('button.newzipfile') || $_POST['edit-file'] == parent::rb('file.edit')) {
			
			$dataItem = array('name' =>  'file'.rand(1000, 9999).rand(1000, 9999), 'dir_id' => $dirId);
			if(array_key_exists('file-id', $_POST)) {
				$fileId = $_POST['file-id'];
				$dataItem = parent::dao('File')->get($fileId);
			}
			$dataItem['zip-out'] = ($_POST['new-zipfile'] == parent::rb('button.newzipfile'));
		
			if($useFrames) {
				return parent::getFrame(parent::rb('title.fileupload'), $return.parent::view('fileadmin-fileupload', $dataItem), true);
			} else {
				return $return.parent::view('fileadmin-fileupload', $dataItem);
			}	
		}
	}
	
	protected function processDirectoryEdit($dataItem, $readRights, $writeRights, $deleteRights) {
		$new = $dataItem['id'] == '';
		
		if($dataItem['url'] == '') {
			$dataItem['url'] = strtolower(parent::convertToUrlValid($dataItem['name']));
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
		
		$select = Select::factory()->where('parent_id', '=', $dataItem['parent_id'])->conjunct('name', '=', $dataItem['name']);
		if(!$new) {
			$select = $select->conjunct('id', '!=', $dataItem['id']);
		}
		
		$existing = parent::dao('Directory')->select($select->result());
		if(count($existing) > 0) {
			return parent::rb('dir.notuniquename').' "'.$dataItem['name'].'"';
		}
		
		if(!$new) {
			$path = $_SERVER['DOCUMENT_ROOT'].self::getPhysicalPathTo($dir['parent_id']);
			rename($path.$dir['name'], $path.$dataItem['name']);
			
			if(parent::dao('Directory')->update($dataItem) != 0) {
				return parent::dao('Directory')->getErrorMessage();
			}
		} else {
			$path = self::getPhysicalPathTo($dataItem['parent_id']).$dataItem['name'];
			mkdir($_SERVER['DOCUMENT_ROOT'].$path);
			
			if(parent::dao('Directory')->insert($dataItem) != 0) {
				return parent::dao('Directory')->getErrorMessage();
			}
			$dataItem['id'] = parent::dao('Directory')->getLastId();
		}
		
		RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $readRights, WEB_R_READ);
		RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $writeRights, WEB_R_WRITE);
		RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, $dataItem['id'], RoleHelper::getCurrentRoles(), $deleteRights, WEB_R_DELETE);
		
		return null;
	}
	
	//C-tag
	public function directoryEditor($useRights = false, $useFrames = false) {
		$this->useRights = $useRights;
		
		if(array_key_exists('dir-id', $_POST)) {
			$dirId = $_POST['dir-id'];
		}
		
		if($_POST['directory-save'] == parent::rb('button.save')) {
			
			$read = $_POST['directory-right-r'];
			if($read == array()) {
				$read = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $dataItem['parent_id'], WEB_R_READ);
			}
			$write = $_POST['directory-right-w'];
			if($write == array()) {
				$write = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $dataItem['parent_id'], WEB_R_WRITE);
			}
			$delete = $_POST['directory-right-d'];
			if($delete == array()) {
				$delete = RoleHelper::getPermissionsOrDefalt(FileAdmin::$DirectoryRightDesc, $dataItem['parent_id'], WEB_R_DELETE);
			}
			
			
			$dataItem = array('id' => $_POST['directory-id'], 'name' => $_POST['dir-name'], 'url' => $_POST['dir-url'], 'parent_id' => $_POST['dir-id'], 'timestamp' => time());
			
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
	
	
	/* ================== WEB ========================================================= */
	
	
	
    /* ================== PROPERTIES ================================================== */

	private $currentId = -1;
	
	public function setCurrentInquiryId($value) {
		parent::setSystemProperty('inquiry_currentid', $value);
		$this->currentId = $value;
	
		return $value;
	}
	
	public function getCurrentInquiryId() {
		if($this->currentId == -1) {
			$this->currentId = parent::getSystemProperty('inquiry_currentid');
		}
		return $this->currentId;
		
	}
}

?>