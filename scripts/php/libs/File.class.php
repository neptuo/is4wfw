<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  require_once("scripts/php/classes/ResourceBundle.class.php");
  require_once("scripts/php/classes/UrlResolver.class.php");
  
  /**
   * 
   *  FileSystem Class.
   *      
   *  @author     Marek SMM
   *  @timestamp  2011-08-16
   * 
   */  
  class File extends BaseTagLib {
  
    /**
     *
     *  Holds file web app extensions.
     *
     */                   
    public $FileEx;
    
    /**
     *
     *  Holds file id when dynamic rewrite.
     *
     */                        
    private $CurrentId = 0;     

	private $BundleName = 'file';
  	
  	private $BundleLang = 'cs';	
  
    public function __construct() {
		global $webObject;
		
		parent::setTagLibXml("xml/File.xml");
      
		$this->FileEx = array(WEB_TYPE_CSS => "css", WEB_TYPE_JS => "js", WEB_TYPE_JPG => "jpg", WEB_TYPE_GIF => "gif", 
                            WEB_TYPE_PNG => "png", WEB_TYPE_PDF => "pdf", WEB_TYPE_RAR => "rar", WEB_TYPE_ZIP => "zip", 
                            WEB_TYPE_TXT => "txt", WEB_TYPE_XML => "xml", WEB_TYPE_XSL => "xsl", WEB_TYPE_DTD => "dtd",
                            WEB_TYPE_HTML => "html", WEB_TYPE_PHP => "php", WEB_TYPE_SQL => "sql", WEB_TYPE_C => "c",
                            WEB_TYPE_CPP => "cpp", WEB_TYPE_H => "h", WEB_TYPE_JAVA => "java", WEB_TYPE_SWF => "swf",
														WEB_TYPE_MP3 => "mp3", WEB_TYPE_PSD => "psd", WEB_TYPE_DOC => "doc", WEB_TYPE_PPT => "ppt",
														WEB_TYPE_XLS => "xls", WEB_TYPE_MPEG => "mpeg", WEB_TYPE_MOV => "mov",
														WEB_TYPE_BMP => "bmp", WEB_TYPE_AVI => "avi", WEB_TYPE_ICO => "ico");
      
		$this->FileMimeType = array(WEB_TYPE_CSS => "text/css", WEB_TYPE_JS => "application/x-javascript", WEB_TYPE_JPG => "image/jpeg", WEB_TYPE_GIF => "image/gif", 
                            		WEB_TYPE_PNG => "image/png", WEB_TYPE_PDF => "application/pdf", WEB_TYPE_RAR => "application/octet-stream", WEB_TYPE_ZIP => "application/zip", 
                            		WEB_TYPE_TXT => "text/plain", WEB_TYPE_XML => "text/xml", WEB_TYPE_XSL => "text/plain", WEB_TYPE_DTD => "text/plain",
                            		WEB_TYPE_HTML => "text/html", WEB_TYPE_PHP => "application/octet-stream", WEB_TYPE_SQL => "text/plain", WEB_TYPE_C => "text/plain",
                            		WEB_TYPE_CPP => "text/plain", WEB_TYPE_H => "text/plain", WEB_TYPE_JAVA => "text/plain", WEB_TYPE_SWF => "application/x-shockwave-flash",
									WEB_TYPE_MP3 => "audio/mpeg", WEB_TYPE_PSD => "application/octet-stream", WEB_TYPE_DOC => "application/msword", WEB_TYPE_PPT => "application/vnd.ms-powerpoint",
									WEB_TYPE_XLS => "application/vnd.ms-excel", WEB_TYPE_MPEG => "video/mpeg", WEB_TYPE_MOV => "video/quicktime",
									WEB_TYPE_BMP => "image/bmp", WEB_TYPE_AVI => "video/x-msvideo", WEB_TYPE_ICO => "image/x-icon");
      //$_SESSION['dir-id'] = 0;
	  
      	if($webObject->LanguageName != '') {
			$rb = new ResourceBundle();
			if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
				$this->BundleLang = $webObject->LanguageName;
			}
		}
    }
    
    function __destruct() {
    	//unset($_SESSION['dir-id']);
    }
    
    /**
     *
     *  Generates list of directories and files from FS.
     *  C tag.
     *  
     *  @param    dirId   				id of parent directory
     *  @param		useFrames		    use frames in output
     *  @return   list of directories and files from FS.
     *
     */                   
    public function showDirectory($dirId = false, $editable = false, $useFrames = false, $showParent = false, $showTitleInsteadOfName = false) {
      global $dbObject;
      global $loginObject;
      $return = "";
      $origDirId = $dirId;
      $dirId = self::setDirId($dirId);
      
      if($_POST['delete-dir'] == "Delete") {
        $directoryId = $_POST['directory-id'];
        
        //test na prava mazani z adresare
        $permission = $dbObject->fetchAll('SELECT `value` FROM `directory_right` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$directoryId.' AND `directory_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
        if(count($permission) > 0 && $editable != "false") {
        	$subd = $dbObject->fetchAll("SELECT count(`id`) AS `count` FROM `directory` WHERE `parent_id` = ".$directoryId.";");
      	  $subf = $dbObject->fetchAll("SELECT count(`id`) AS `count` FROM `file` WHERE `dir_id` = ".$directoryId.";");
        
    	    if($subd[0]['count'] == 0 && $subf[0]['count'] == 0) {
  	        $path = self::getPhysicalPathTo($directoryId);
	          $dbObject->execute("DELETE FROM `directory` WHERE `id` = ".$directoryId.";");
	          $dbObject->execute("DELETE FROM `directory_right` WHERE `id` = ".$directoryId.";");
        	  // take smazat fyzicky adresar!!
      	    rmdir($_SERVER['DOCUMENT_ROOT'].$path);
    	    } else {
  	        $return .= '<h4 class="error">Directory isn\'t empty!</h4>';
	        }
        } else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
      } elseif($_POST['delete-file'] == "Delete") {
        $fileId = $_POST['file-id'];
        
        //test na prava mazani z adresare
        $permission = $dbObject->fetchAll('SELECT `value` FROM `file_right` LEFT JOIN `group` ON `file_right`.`gid` = `group`.`gid` WHERE `file_right`.`fid` = '.$fileId.' AND `file_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
        if(count($permission) > 0) {
	        $file = $dbObject->fetchAll("SELECT `name`, `dir_id`, `type` FROM `file` WHERE `id` = ".$fileId.";");
  	      if(count($file) == 1) {
    	      $dbObject->execute("DELETE FROM `file` WHERE `id` = ".$fileId.";");
    	      $dbObject->execute("DELETE FROM `file_right` WHERE `id` = ".$fileId.";");
      	    $path = self::getPhysicalPathTo($file[0]['dir_id']);
        	  $filePath = $_SERVER['DOCUMENT_ROOT'].$path.$file[0]['name'].".".$this->FileEx[$file[0]['type']];
          	//echo $filePath;
	          unlink($filePath);
  	      }
  	    } else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
      }
      
      if($useFrames != 'false') {
      	return parent::getFrame("File list :: /".self::getPhysicalPathTo($dirId, true), $return.self::getList($dirId, $editable, $showParent == "false" ? false : true, $showTitleInsteadOfName == "true" ? true : false), "", true);
      } else {
				return $return.self::getList($dirId, true);
			}
    }
    
    /**
     *
     *  Generates list of files.
     *  
     *  @param    dirId     dir id to load from
     *  @param    editable  if true, it shows editing form
     *  @return   list of files     
     *
     */                        
    private function getList($dirId, $editable, $showParent, $showTitleInsteadOfName) {
		global $dbObject;
		global $loginObject;
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = "";
        
      $dirs = $dbObject->fetchAll('SELECT `directory`.`id`, `directory`.`name`, .`directory`.`timestamp` FROM `directory` LEFT JOIN `directory_right` ON `directory`.`id` = `directory_right`.`did` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `parent_id` = '.$dirId.' AND `directory_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `name`;');
      $dir = $dbObject->fetchAll("SELECT `parent_id` FROM `directory` WHERE `id` = ".$dirId.";");
      
      $return .= '' 
      .'<table class="dir-list">'
        .'<tr>'
          .'<th class="th-icon"></th>'
          .'<th class="th-id"><span>'.$rb->get('file.id').':</span></th>'
          .'<th class="th-name">'.$rb->get('file.name').':</th>'
          .'<th class="th-dir-physical-path">'.$rb->get('file.directlink').':</th>'
          .'<th class="th-timestamp">'.$rb->get('file.timestamp').'</th>'
          .'<th class="th-type">'.$rb->get('file.type').'</th>'
          .(($editable != "false") ? '<th class="th-edit">'.$rb->get('file.action').'</th>' : '' )
        .'</tr>'
		.($showParent ? ''
        .'<tr>'
          .'<td class="dir-icon"></td>'
          .'<td class="dir-id"></td>'
          .'<td class="dir-name">'
            .'<form name="dir-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              .'<input type="hidden" name="dir-id" value="'.(($dirId != 0) ? $dir[0]['parent_id'] : $dirId).'" />'
              .'<input type="submit" name="ch-dir" value=".." />'
            .'</form>'
          .'</td>'
          .'<td class="dir-physical-path"></td>'
          .'<td class="dir-timestamp"></td>'
          .'<td class="dir-type"></td>'
          .(($editable != "false") ? '<td class="dir-edit"></td>' : '' )
        .'</tr>'
		: '');
        
      $n = 0;
      // sudy   even
      // lichy  idle
      foreach($dirs as $dir) {
        $return .= ''
        .'<tr class="'.((($n % 2) == 0) ? 'even' : 'idle').'">'
          .'<td class="dir-icon dir"></td>'
          .'<td class="dir-id dir"><span>'.$dir['id'].'</span></td>'
          .'<td class="dir-name">'
            .'<form name="dir-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              .'<input type="hidden" name="dir-id" value="'.$dir['id'].'" />'
              .'<input type="submit" name="ch-dir" value="'.$dir['name'].'" />'
            .'</form>'
          .'</td>'
          .'<td class="dir-physical-path"></td>'
          .'<td class="dir-timestamp">'.date('d.m.Y H:i:s', $dir['timestamp']).'</td>'
          .'<td class="dir-type"></td>'	
          .(($editable != "false") ? ''
          .'<td class="dir-edit">'
            .'<form name="dir-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              .'<input type="hidden" name="directory-id" value="'.$dir['id'].'" />'
              .'<input type="hidden" name="edit-dir" value="Edit" />'
              .'<input type="image" src="~/images/page_edi.png" name="edit-dir" value="Edit" title="Edit Directory" />'
            .'</form> '
            .'<form name="dir-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              .'<input type="hidden" name="directory-id" value="'.$dir['id'].'" />'
              .'<input type="hidden" name="delete-dir" value="Delete" />'
              .'<input class="confirm" type="image" src="~/images/page_del.png" name="delete-dir" value="Delete" title="Delete Directory, id('.$dir['id'].')" />'
            .'</form>'
          .'</td>'
          : '' )
        .'</tr>';
        $n ++;
      }
      
      //$files = $dbObject->fetchAll("SELECT `id`, `name`, `title`, `timestamp`, `type` FROM `file` WHERE `dir_id` = ".$dirId." ORDER BY `name`;");
      $files = $dbObject->fetchAll('SELECT `file`.`id`, `file`.`name`, `file`.`title`, `file`.`timestamp`, `file`.`type` FROM `file` LEFT JOIN `file_right` ON `file`.`id` = `file_right`.`fid` LEFT JOIN `group` ON `file_right`.`gid` = `group`.`gid` WHERE `dir_id` = '.$dirId.' AND `file_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `name`;');
        
      foreach($files as $file) {
        $return .= ''
        .'<tr class="'.((($n % 2) == 0) ? 'even' : 'idle').'">'
          .'<td class="file-icon '.$this->FileEx[$file['type']].'"></td>'
          .'<td class="file-id"><span>'.$file['id'].'</span></td>'
          .'<td class="file-name">'
            .'<a target="_blank" title="'.$file['title'].'" href="~/file.php?rid='.$file['id'].'">'
				.($showTitleInsteadOfName && strlen($file['title']) != 0 ? ''
				.$file['title']
				: ''
				.$file['name']
				)
			.'</a>'
          .'</td>'
          .'<td class="dir-physical-path">'
						.'<a href="'.self::getPhysicalPathTo($dirId, false).$file['name'].".".$this->FileEx[$file['type']].'" target="_blank">open</a>'
					.'</td>'
          .'<td class="file-timestamp">'.date('d.m.Y H:i:s', $file['timestamp']).'</td>'
          .'<td class="file-type">'.$this->FileEx[$file['type']].'</td>'
          .(($editable != "false") ? ''
          .'<td class="file-edit">'
            .'<form name="dir-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              .'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
              .'<input type="hidden" name="edit-file" value="Edit" />'
              .'<input type="image" src="~/images/page_edi.png" name="edit-file" value="Edit" title="Edit File" />'
            .'</form> '
            .'<form name="dir-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              .'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
              .'<input type="hidden" name="delete-file" value="Delete" />'
              .'<input class="confirm" type="image" src="~/images/page_del.png" name="delete-file" value="Delete" title="Delete File, id('.$file['id'].')" />'
            .'</form>'
          .'</td>'
          : '' )
        .'</tr>';
        $n ++;
      }
        
      $return .= ''
      .'</table>';
        
        return $return;
    }
    
    /**
     *
     *  Shows form for add new directory.
     *  C tag.     
     *  
     *  @param    dirId   				parent dir id
     *  @param		useFrames				use frames in output
     *  @return   form for adding directory               
     *
     */                        
    public function showNewDirectoryForm($dirId = false, $useFrames = false) {
      global $dbObject;
      global $loginObject;
      $return = "";
      $dirId = self::setDirId($dirId);
      
      if($_POST['new-directory'] == "Create directory" || $_POST['edit-directory'] == "Edit directory") {
        $directoryParentId = $_POST['directory-parent-id'];
        $directoryName = $_POST['directory-name'];
        $directoryUrl = $_POST['directory-url'];
        $read = $_POST['directory-right-edit-groups-r'];
        $write = $_POST['directory-right-edit-groups-w'];
        $delete = $_POST['directory-right-edit-groups-d'];
        
        //test na prava zapisu do adresare
        $permission = $dbObject->fetchAll('SELECT `value` FROM `directory_right` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$directoryParentId.' AND `directory_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
        if(count($permission) > 0) {
					if(strlen($directoryName) > 0) {
						if($_POST['new-directory'] == "New directory") {
							$sib = $dbObject->fetchAll('SELECT count(`id`) AS `count` FROM `directory` WHERE `name` = "'.$directoryName.'" AND `parent_id` = '.$directoryParentId.';');
						} elseif($_POST['edit-directory'] == "Edit directory") {
							$par = $dbObject->fetchAll('SELECT `name`, `parent_id` FROM `directory` WHERE `id` = '.$directoryParentId.';'); 
							$sib = $dbObject->fetchAll('SELECT count(`id`) AS `count` FROM `directory` WHERE `name` = "'.$directoryName.'" AND `parent_id` = '.$par[0]['parent_id'].';');
						}
  		      
		        if(($_POST['new-directory'] == "Create directory" && $sib[0]['count'] == 0) || ($_POST['edit-directory'] == "Edit directory" && (($par[0]['name'] == $directoryName && $sib[0]['count'] == 1) || ($par[0]['name'] != $directoryName && $sib[0]['count'] == 0)))) {
		        	if($_POST['new-directory'] == "Create directory") {
        		  	$dbObject->execute("INSERT INTO `directory`(`parent_id`, `name`, `url`, `timestamp`) VALUES(".$directoryParentId.", \"".$directoryName."\", \"".$directoryUrl."\", ".time().");");
      	  	  	$directoryId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `directory`;');
    	    	  	$directoryId = $directoryId[0]['id'];
								$path = self::getPhysicalPathTo($directoryParentId).$directoryName;
								mkdir($_SERVER['DOCUMENT_ROOT'].$path);
							} elseif($_POST['edit-directory'] == "Edit directory") {
								$oldName = $dbObject->fetchAll('SELECT `name`, `parent_id` FROM `directory` WHERE `id` = '.$directoryParentId.';');
								$path1 = substr(self::getPhysicalPathTo($oldName[0]['parent_id']).$oldName[0]['name'].'/', 1);
								$path2 = substr(self::getPhysicalPathTo($oldName[0]['parent_id']).$directoryName.'/', 1);
								rename($path1, $path2);
								$dbObject->execute('UPDATE `directory` SET `name` = "'.$directoryName.'", `url` = "'.$directoryUrl.'", `timestamp` = "'.time().'" WHERE `id` = '.$directoryParentId.';');
								$directoryId = $directoryParentId;
							} else {
								return;
							}
							
							if(count($read) != 0) {
								$dbR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `directory_right`.`did` = ".$directoryId." AND `type` = ".WEB_R_READ.";");
	 		          foreach($dbR as $right) {
		              if(!in_array($right, $read)) {
    	           		$dbObject->execute("DELETE FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_READ.";");
      	       		}
        	   		}
         			  foreach($read as $right) {
       		  	    $row = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
     		      	  if(count($row) == 0) {
   		          	  $dbObject->execute("INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES (".$directoryId.", ".$right.", ".WEB_R_READ.");");
	 		            }
		            }
            	} else {
								$rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryParentId.' AND `type` = '.WEB_R_READ.';');
								foreach($rights as $right) {
									$dbObject->execute('INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES ('.$directoryId.', '.$right['gid'].', '.WEB_R_READ.');');
								}
							}
    	 	      if(count($write) != 0) {
    		       	$dbR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `directory_right`.`did` = ".$directoryId." AND `type` = ".WEB_R_WRITE.";");
  	    	   	  foreach($dbR as $right) {
	       		      if(!in_array($right, $write)) {
     	      	  	  $dbObject->execute("DELETE FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_WRITE.";");
   	        		  }
	 	        	  }
  	      	    foreach($write as $right) {
    	  	       	$row = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
    		       	  if(count($row) == 0) {
  	    	   	      $dbObject->execute("INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES (".$directoryId.", ".$right.", ".WEB_R_WRITE.");");
	       		      }
	     	    	  }
		     	    } else {
								$rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryParentId.' AND `type` = '.WEB_R_WRITE.';');
								foreach($rights as $right) {
									$dbObject->execute('INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES ('.$directoryId.', '.$right['gid'].', '.WEB_R_WRITE.');');
								}
							}
  	   	      if(count($delete) != 0) {
	  	 	        $dbR = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `directory_right`.`did` = '.$directoryId.' AND `type` = '.WEB_R_DELETE.';');
 		  	        foreach($dbR as $right) {
    	  	        if(!in_array($right, $delete)) {
      	  	       	$dbObject->execute("DELETE FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_DELETE.";");
        	  	   	}
          	 		} 	
		         	  foreach($delete as $right) {
  		     	      $row = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryId.' AND `type` = '.WEB_R_DELETE.' AND `gid` = '.$right.';');
    		 	        if(count($row) == 0) {
   	  		          $dbObject->execute("INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES (".$directoryId.", ".$right.", ".WEB_R_DELETE.");");
 	      		      }
          		  }
          		} else {
								$rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryParentId.' AND `type` = '.WEB_R_DELETE.';');
								foreach($rights as $right) {
									$dbObject->execute('INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES ('.$directoryId.', '.$right['gid'].', '.WEB_R_DELETE.');');
								}
							}
      		  } else {
    		      $message = '<h4 class="error">Directory with this name already exists!</h4>';
  		        $return .= $message;//parent::getFrame("Error Message", $message, "", true);
  		        if($_POST['edit-directory'] == "Edit directory") {
								$_POST['edit-dir'] = 'Edit';
								$_POST['directory-id'] = $directoryParentId;
							}
		        }
	        } else {
						$return .= '<h4 class="error">Directory name must contain at least one character!</h4>';
					}
        } else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
      }
      
      $dirName = '';
      if($_POST['edit-dir'] == 'Edit') {
      	$direcId = $_POST['directory-id'];
				$permission = $dbObject->fetchAll('SELECT `directory`.`name`, `directory`.`url`, `group`.`value` FROM `directory` LEFT JOIN `directory_right` ON `directory`.`id` = `directory_right`.`did` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$direcId.' AND `directory_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
        if(count($permission) > 0) {
        	$dirId = $direcId;
        	$dirName = $permission[0]['name'];
        	$dirUrl = $permission[0]['url'];
        }
			}
      
      // Ziskat prava ....
			$show = array('read' => true, 'write' => true, 'delete' => false);
			$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_READ.";");
      $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_WRITE.";");
      $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_DELETE.";");
      $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
      $groupSelectR = '<select id="directory-right-edit-groups-r" name="directory-right-edit-groups-r[]" multiple="multiple" size="5">';
      $groupSelectW = '<select id="directory-right-edit-groups-w" name="directory-right-edit-groups-w[]" multiple="multiple" size="5">';
      $groupSelectD = '<select id="directory-right-edit-groups-d" name="directory-right-edit-groups-d[]" multiple="multiple" size="5">';
      foreach($allGroups as $group) {
        $selectedR = false;
        $selectedW = false;
        $selectedD = false;
        foreach($groupsR as $gp) {
          if($gp['gid'] == $group['gid']) {
            $selectedR = true;
            $show['read'] = true;
          }
        }
        foreach($groupsW as $gp) {
          if($gp['gid'] == $group['gid']) {
            $selectedW = true;
            $show['write'] = true;
          }
        }
        foreach($groupsD as $gp) {
          if($gp['gid'] == $group['gid']) {
            $selectedD = true;
            $show['delete'] = true;
          }
        }
        $groupSelectR .= '<option'.(($selectedR) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
        $groupSelectW .= '<option'.(($selectedW) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
        $groupSelectD .= '<option'.(($selectedD) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
      }
      $groupSelectR .= '</select>';
      $groupSelectW .= '</select>';
      $groupSelectD .= '</select>';
      
      $return .= ''
      .'<form name="new-directory" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
        .'<input type="hidden" name="directory-parent-id" value="'.$dirId.'" />'
        .'<div class="directory-name">'
  	      .'<label for="directory-name">Directory name (<span>at least 1 character</span>):</label> '
	        .'<input type="text" id="directory-name" name="directory-name" value="'.$dirName.'" />'
        .'</div>'
        .'<div class="directory-name">'
  	      .'<label for="directory-name">Directory rewrite url (<span>at least 1 char</span>):</label> '
	        .'<input type="text" id="directory-url" name="directory-url" value="'.$dirUrl.'" />'
        .'</div>'
        .'<div class="directory-rights">'
  	      .'<div class="directory-right-r">'
	        	.'<label for="directory-right-edit-groups-r">Read:</label>'
        		.$groupSelectR
        	.'</div>'
      	  .'<div class="directory-right-w">'
    	    	.'<label for="directory-right-edit-groups-w">Write:</label>'
  	      	.$groupSelectW
	        .'</div>'
	        .(($_POST['edit-dir'] == 'Edit' && count($permission) > 0 && !$show['delete']) ? ''
  	      : ''
        	.'<div class="directory-right-d">'
      	  	.'<label for="directory-right-edit-groups-d">Delete:</label>'
    	    	.$groupSelectD
  	      .'</div>'
					)
	        .'<div class="clear"></div>'
        .'</div>'
        .(($_POST['edit-dir'] == 'Edit' && count($permission) > 0) ? ''
        .'<div class="directory-submit">'
        	.'<input type="submit" name="edit-directory" value="Edit directory" /> '
        	.'<input type="submit" name="back-directory" value="Back" />'
        .'</div>'
        : ''
        .'<div class="directory-submit">'
        	.'<input type="submit" name="new-directory" value="Create directory" />'
        .'</div>')
      .'</form>';
      
      if($useFrames != 'false') {
      	return parent::getFrame((($_POST['edit-dir'] == 'Edit') ? "Edit directory" : "New directory"), $return, "", true);
      } else {
      	return $return;
      }
    }
    
    /**
     *
     *  Shows form for upload new file.
     *  C tag.
     *  
     *  @param    dirId   		dir id to put in
     *  @param		useRights		use manaul setting rights or use auto setting
     *  @param		useFrames		use frames in output
     *  @return   form for upload file                    
     *
     */                   
    public function showUploadForm($dirId = false, $useRights = false, $useFrames = false) {
      global $dbObject;
      global $loginObject;
      $return = "";
      $dirId = self::setDirId($dirId);
      
      if(array_key_exists('file-name', $_POST)) {
        $return .= self::processFileUpload();
      }
      
      // Ziskat prava ....
			$show = array('read' => true, 'write' => true, 'delete' => false);
			if($_POST['edit-file'] != 'Edit') {
				$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_READ.";");
  	    $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_WRITE.";");
	      $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_DELETE.";");
      } else {
      	$fileId = $_POST['file-id'];
      	$file = $dbObject->fetchAll('SELECT `id`, `name`, `title` FROM `file` WHERE `id` = '.$fileId.';');
      	$fileName = $file[0]['name'];
      	$fileTitle = $file[0]['title'];
      	$fileId = $file[0]['id'];
				$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_READ.";");
  	    $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_WRITE.";");
	      $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_DELETE.";");
	    }
      $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
      $groupSelectR = '<select id="file-right-edit-groups-r" name="file-right-edit-groups-r[]" multiple="multiple" size="5">';
      $groupSelectW = '<select id="file-right-edit-groups-w" name="file-right-edit-groups-w[]" multiple="multiple" size="5">';
      $groupSelectD = '<select id="file-right-edit-groups-d" name="file-right-edit-groups-d[]" multiple="multiple" size="5">';
      foreach($allGroups as $group) {
        $selectedR = false;
        $selectedW = false;
        $selectedD = false;
        foreach($groupsR as $gp) {
          if($gp['gid'] == $group['gid']) {
            $selectedR = true;
            $show['read'] = true;
          }
        }
        foreach($groupsW as $gp) {
          if($gp['gid'] == $group['gid']) {
            $selectedW = true;
            $show['write'] = true;
          }
        }
        foreach($groupsD as $gp) {
          if($gp['gid'] == $group['gid']) {
            $selectedD = true;
            $show['delete'] = true;
          }
        }
        $groupSelectR .= '<option'.(($selectedR) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
        $groupSelectW .= '<option'.(($selectedW) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
        $groupSelectD .= '<option'.(($selectedD) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
      }
      $groupSelectR .= '</select>';
      $groupSelectW .= '</select>';
      $groupSelectD .= '</select>';
      
      $return .= ''
      .'<form name="new-file" method="post" action="'.$_SERVER['REDIRECT_URL'].'" enctype="multipart/form-data">'
      	.'<!--<a href="http://www.google.cz/" target="ajaxFileUploadIFrame">Google</a>'
      	.'<iframe id="ajaxFileUploadIFrame" name="ajaxFileUploadIFrame" src=""></iframe>-->'
        .'<input type="hidden" name="dir-id" value="'.$dirId.'" />'
        .'<div class="up-file-prop">'
      		.'<div class="up-file-name">'
  	    	  .'<label for="file-name">File name:</label> '
	    	    .'<input type="text" id="file-name" name="file-name" value="'.((strlen($fileName) != 0) ? $fileName : 'file'.rand(1000, 9999).rand(1000, 9999)).'" /> '
    	    .'</div>'
  	      .'<div class="up-file-title">'
	  	      .'<label for="file-title">File title:</label> '
	        	.'<input type="text" id="file-title" name="file-title" value="'.$fileTitle.'" /> '
        	.'</div>'
      	  .'<div class="up-file-rs">'
    	    	.'<label for="file-rs">Select file:</label> '
  	      	.'<input type="file" id="file-rs" name="file-rs" /> '
	        .'</div>'
        .'</div>'
        .(($useRights != 'false') ? ''
        .'<div class="file-rights">'
  	      .'<div class="file-right-r">'
	        	.'<label for="file-right-edit-groups-r">Read:</label>'
        		.$groupSelectR
        	.'</div>'
      	  .'<div class="file-right-w">'
    	    	.'<label for="file-right-edit-groups-w">Write:</label>'
  	      	.$groupSelectW
	        .'</div>'
        	.'<div class="file-right-d">'
      	  	.'<label for="file-right-edit-groups-d">Delete:</label>'
    	    	.$groupSelectD
  	      .'</div>'
	        .'<div class="clear"></div>'
        .'</div>'
        : '')
	      .'<div class="clear"></div>'
        .'<div class="up-file-submit">'
        	.(($_POST['edit-file'] == 'Edit') ? ''
        	.'<input type="hidden" name="file-id" value="'.$fileId.'" />'
        	.'<input type="submit" name="edit-upload-file" value="Upload new version" title="Upload new version" />'
        	: ''
        	.'<input type="submit" name="new-file" value="Upload" title="Upload File" />'
        	)
        .'</div>'
	      .'<div class="clear"></div>'
      .'</form>';
      
      if($useFrames != 'false') {
      	return parent::getFrame((($_POST['edit-file'] == 'Edit') ? 'Edit file' : "New file"), $return, "", true);
      } else {
      	return $return;
      }
    }
    
    private function processFileUpload() {
      global $dbObject;
      global $loginObject;
      $fileName = $_POST['file-name'];
      $dirId = $_POST['dir-id'];
      $fileTitle = $_POST['file-title'];
      $fileId = $_POST['file-id'];
      $read = $_POST['file-right-edit-groups-r'];
      $write = $_POST['file-right-edit-groups-w'];
      $delete = $_POST['file-right-edit-groups-d'];
      $extType = self::getWebFileType($_FILES['file-rs']['name']);
      $oldFile = null;
      
      if(is_uploaded_file($_FILES['file-rs']['tmp_name'])) {
        $original = $_FILES['file-rs']['tmp_name'];
        if(strlen($fileName) == 0) {
          $fileName = "file".rand(1000, 9999).rand(1000, 9999);
        }
        
        if(array_key_exists('file-id', $_POST)) {
					$permission = $dbObject->fetchAll('SELECT `value` FROM `file_right` LEFT JOIN `group` ON `file_right`.`gid` = `group`.`gid` WHERE `file_right`.`fid` = '.$fileId.' AND `file_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
        } else {
        	$permission = $dbObject->fetchAll('SELECT `value` FROM `directory_right` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$dirId.' AND `directory_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
        }
        if(count($permission) > 0) {
					if(strlen($fileName) > 0) {
						if(array_key_exists('file-id', $_POST)) {
        			$files = $dbObject->fetchAll("SELECT `id` FROM `file` WHERE `dir_id` = ".$dirId." AND `name` = \"".$fileName."\" AND `type` = ".$extType." AND `id` != ".$fileId.";");
        		} else {
							$files = $dbObject->fetchAll("SELECT `id` FROM `file` WHERE `dir_id` = ".$dirId." AND `name` = \"".$fileName."\" AND `type` = ".$extType.";");
						}
		        if(count($files) == 0) {
        		  if($extType > 0) {
            		$path = self::getPhysicalPathTo($dirId);
		            $moved = move_uploaded_file($original, $_SERVER['DOCUMENT_ROOT'].$path.$fileName.".".$this->FileEx[$extType]);
    		        
        		    if($moved) {
        		    	if(array_key_exists('file-id', $_POST)) {
        		    		$files = $dbObject->fetchAll("SELECT `id`, `name`, `type`, `dir_id` FROM `file` WHERE `dir_id` = ".$dirId." AND `id` = ".$fileId.";");
        		    		$oldFile = $files[0];
      	  		    	$path = self::getPhysicalPathTo($oldFile['dir_id']);
				        	  $filePath = $_SERVER['DOCUMENT_ROOT'].$path.$oldFile['name'].".".$this->FileEx[$oldFile['type']];
				          	//echo $filePath;
				          	if($fileName != $oldFile['name'] || $extType != $oldFile['type']) {
						          unlink($filePath);
					          }
					          $dbObject->execute('UPDATE `file` SET `name` = "'.$fileName.'", `title` = "'.$fileTitle.'", `timestamp` = '.time().' WHERE `id` = '.$fileId.';');
        		    	} else {
    	        		  $dbObject->execute("INSERT INTO `file`(`dir_id`, `name`, `title`, `type`, `timestamp`) VALUES (".$dirId.", \"".$fileName."\", \"".$fileTitle."\", ".$extType.", ".time().");");
		  	          	$fileId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `file`;');
		  	          	$fileId = $fileId[0]['id'];
	  	          	}
	  	          	
	  	          	
	  	          	// NEFUNGUJE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	  	          	if(count($read) != 0) {
										$dbR = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `file_right`.`fid` = ".$fileId." AND `type` = ".WEB_R_READ.";", true, true);
		 		      	    foreach($dbR as $right) {
			      	        if(!in_array($right, $read)) {
    		  	         		$dbObject->execute("DELETE FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_READ.";");
      			       		}
      	  		   		}
    	     				  foreach($read as $right) {
  	     		  		    $row = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
	     		      		  if(count($row) == 0) {
   		          			  $dbObject->execute("INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES (".$fileId.", ".$right.", ".WEB_R_READ.");");
		 		          	  }
			          	  }
    	        		} else {
										$rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$dirId.' AND `type` = '.WEB_R_READ.';');
										foreach($rights as $right) {
											$dbObject->execute('INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES ('.$fileId.', '.$right['gid'].', '.WEB_R_READ.');');
										}
									}
    		 	  	    if(count($write) != 0) {
    				       	$dbR = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `file_right`.`fid` = ".$fileId." AND `type` = ".WEB_R_WRITE.";");
  	    			   	  foreach($dbR as $right) {
	    	   			      if(!in_array($right, $write)) {
    	 	      		  	  $dbObject->execute("DELETE FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_WRITE.";");
  	 	        			  }
			 	        	  }
  		      	    	foreach($write as $right) {
    		  	    	   	$row = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
    			    	   	  if(count($row) == 0) {
  	    			   	      $dbObject->execute("INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES (".$fileId.", ".$right.", ".WEB_R_WRITE.");");
	       				      }
	     		    		  }
		  	   	    	} else {
										$rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$dirId.' AND `type` = '.WEB_R_WRITE.';');
										foreach($rights as $right) {
											$dbObject->execute('INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES ('.$fileId.', '.$right['gid'].', '.WEB_R_WRITE.');');
										}
									}
	  		   	      if(count($delete) != 0) {
			  	 	        $dbR = $dbObject->fetchAll('SELECT `gid` FROM `file_right` WHERE `file_right`.`fid` = '.$fileId.' AND `type` = '.WEB_R_DELETE.';');
	 			  	        foreach($dbR as $right) {
    		  	        	if(!in_array($right, $delete)) {
      		  	    	   	$dbObject->execute("DELETE FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_DELETE.";");
        		  		   	}
          			 		} 	
		        	 		  foreach($delete as $right) {
  		    	 	    	  $row = $dbObject->fetchAll('SELECT `gid` FROM `file_right` WHERE `fid` = '.$fileId.' AND `type` = '.WEB_R_DELETE.' AND `gid` = '.$right.';');
    			 	        	if(count($row) == 0) {
   	  			          	$dbObject->execute("INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES (".$fileId.", ".$right.", ".WEB_R_DELETE.");");
	 		      		      }
  		        		  }
	    	      		} else {
										$rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$dirId.' AND `type` = '.WEB_R_DELETE.';');
										foreach($rights as $right) {
											$dbObject->execute('INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES ('.$fileId.', '.$right['gid'].', '.WEB_R_DELETE.');');
										}
									}
	  	          }
  	  	      } else {
        		    $return .= '<h4 class="error">Un-supported file type!</h4>';
		          }
    		    } else {
	        		$return .= '<h4 class="error">File with this name already exists!</h4>';
        		}		
        	} else {
						$return .= '<h4 class="error">File name must contain at least one character!</h4>';
					}
        } else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
      } else {
				$return .= '<h4 class="error">No file selected!</h4>';
			}
      
      return $return;
    }
    
    /**
     *  
     *  Setups right dirId.
     *  
     *  @param    dirId   dir id from c tag
     *  @return   dir id               
     *  
     */
    private function setDirId($dirId) {
      if(array_key_exists("dir-id", $_POST)) {
        $_SESSION['dir-id'] = $_POST['dir-id'];
        return $_SESSION['dir-id'];
      } else if(array_key_exists("dir-id", $_SESSION)) {
        return $_SESSION['dir-id'];
      } else {
        $_SESSION['dir-id'] = $dirId | 0;
        return $_SESSION['dir-id'];
      }
    }
    
    /**
     *
     *  Generates physical path to dir in fs.
     *  
     *  @param    dirId   dir id
     *  @return   physical path to dir in fs
     *
     */                   
    public function getPhysicalPathTo($dirId, $notUserFsRoot = false) {
      $path = "";
      
      if($dirId >= 0) {
        while($dirId != 0) {
          $dirInfo = parent::db()->fetchAll("SELECT `name`, `parent_id` FROM `directory` WHERE `id` = ".$dirId.";");
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
    
    /**
     *
     *  Return binary representation of file.
     *  C tag.
     *  
     *  @param    fileId    file id
     *  @return   binary representation of file                    
     *
     */                        
    public function getFile($fileId = false) {
      global $dbObject;
      
      if($fileId == false) {
        if($this->CurrentId != 0) {
          $fileId = $this->CurrentId;
          $this->CurrentId = 0;
        } elseif($_SESSION['file']['current_id'] != '') {
        	$fileId = $_SESSION['file']['current_id'];
        } else {
          // vrat image "file not found".
        }
      }
      
      $file = $dbObject->fetchAll("SELECT `id`, `dir_id`, `name`, `type`, `timestamp` FROM `file` WHERE `id` = ".$fileId.";");
      
      if(count($file) == 1) {
        $filePath = $_SERVER['DOCUMENT_ROOT'].self::getPhysicalPathTo($file[0]['dir_id']).$file[0]['name'].".".$this->FileEx[$file[0]['type']];
        $fileExt = ($file[0]['type'] == WEB_TYPE_JPG || $file[0]['type'] == WEB_TYPE_GIF || $file[0]['type'] == WEB_TYPE_PNG) ? "image/".$this->FileEx[$file[0]['type']] : "document/".$file[0]['type'];
        
        if(array_key_exists("width", $_GET) && array_key_exists("height", $_GET)) {
          $width = $_GET['width'];
          $height = $_GET['height'];
          $thumbPath = 'cache/images/'.$file[0]['dir_id'].'-'.$file[0]['id'].'-'.$file[0]['name'].'_'.$width.'x'.$height.'.'.$this->FileEx[$file[0]['type']];
          
          if(file_exists($thumbPath) && is_readable($thumbPath)) {
            $filePath = $thumbPath;
          } else {
            self::createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
            $filePath = $thumbPath;
          }
        } else if(array_key_exists("width", $_GET)) {
          $width = $_GET['width'];
          list($orWidth, $orHeight, $orType) = getimagesize($filePath);
          $ratio = $width / $orWidth;
          $height = round($ratio * $orHeight);
          
          $thumbPath = 'cache/images/'.$file[0]['dir_id'].'-'.$file[0]['id'].'-'.$file[0]['name'].'_'.$width.'x'.$height.'.'.$this->FileEx[$file[0]['type']];
          
          if(file_exists($thumbPath) && is_readable($thumbPath)) {
            $filePath = $thumbPath;
          } else {
            self::createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
            $filePath = $thumbPath;
          }
        } else if(array_key_exists("height", $_GET)) {
          $height = $_GET['height'];
          list($orWidth, $orHeight, $orType) = getimagesize($filePath);
          $ratio = $height / $orHeight;
          $width = round($ratio * $orWidth);
          
          $thumbPath = 'cache/images/'.$file[0]['dir_id'].'-'.$file[0]['id'].'-'.$file[0]['name'].'_'.$width.'x'.$height.'.'.$this->FileEx[$file[0]['type']];
          
          if(file_exists($thumbPath) && is_readable($thumbPath)) {
            $filePath = $thumbPath;
          } else {
            self::createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
            $filePath = $thumbPath;
          }
        }
        
        header("Expires: ".gmdate("D, d M Y H:i:s", mktime(0, 0, 0, 12, 21, 2011))." GMT");
        header("Cache-Control: max-age=31536000");
        header("Pragma: cache");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s", $updTime)." GMT");
        
        $updTime = filemtime($filePath);
        //echo $updTime.' - '.getenv("HTTP_IF_MODIFIED_SINCE").' ; ';
        if($_SERVER["HTTP_IF_MODIFIED_SINCE"] && $updTime <= strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
          header("HTTP/1.1 304 Not Modified");
          exit;
        }

        
        if(file_exists($filePath) && is_readable($filePath)) {
          $fileSize = filesize($filePath);
          
          header('Content-Type: '.$fileExt);
          header('Content-Length: '.$fileSize);
          header('Content-Disposition: attachment; filename='.$file[0]['name'].".".$this->FileEx[$file[0]['type']]);
          header('Content-Transfer-Encoding: binary');
          $file = @ fopen($filePath, 'rb');
          if ($file) {
            fpassthru($file);
            exit;
          } else {
            // vrat image "file not found".
          }
        } else {
          // vrat image "file not found".
        }
      } else {
        // vrat image "file not found".
      }
    }
    
    /**
     *
     *  Creates thumb from originalPath in thumbPath. Thumb has width and height
     *  as passed values.
     *  
     *  @param    originalPath    path to originalImage
     *  @param    thumbPath       path where to locate new image
     *  @param    width           new image width
     *  @param    height          new image height
     *  @param    type            web image type     
     *  @return   true if success, false otherwise                                   
     *
     */                             
    public function createThumb($originalPath, $thumbPath, $width, $height, $type) {
      list($orWidth, $orHeight, $orType) = getimagesize($originalPath);
      
      switch($type) {
        case WEB_TYPE_GIF: 
          $source = @ imagecreatefromgif($originalPath);
	        if(!$source) {
	          $message = 'Cannot process GIF files. Please use JPEG or PNG.';
	          echo "<h4 class=\"error\">".$message."<h4>";
	          trigger_error($message, E_USER_ERROR);
	        }
          break;
              
        case WEB_TYPE_JPG: $source = imagecreatefromjpeg($originalPath); break;
        case WEB_TYPE_PNG: $source = imagecreatefrompng($originalPath); break;
      }
          
      $thumb = imagecreatetruecolor($width, $height);
      imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $orWidth, $orHeight);
          
      switch($type) {
        case WEB_TYPE_GIF:
	        if(function_exists('imagegif')) {
	          $success = imagegif($thumb, $thumbPath);
		      } else {
	          $success = imagejpeg($thumb, $thumbPath, 50);
		      }
	        break;
	            
	      case WEB_TYPE_JPG:
	        $success = imagejpeg($thumb, $thumbPath, 100);
	        break;
      	case WEB_TYPE_PNG:
	        $success = imagepng($thumb, $thumbPath);
	    }
	        
	    imagedestroy($source);
      imagedestroy($thumb);
        
      return true;
    }
    
    /**
     *
     *  Returns right file type to file.
     *  
     *  @param    fileName    file name
     *  @return   int file extension               
     *
     */                   
    public function getWebFileType($fileName) {
      foreach($this->FileEx as $key => $ext) {
        $ext = ".".$ext;
        if(substr($fileName, strlen($fileName) - strlen($ext)) == $ext) {
          return $key;
        }
      }
      return -1;
    }
    
    /**
     *
     *  Dynamicly rewrite address.
     *  C tag.
     *  
     *  return    if file exists, it returns CurrentDynamicPath     
     *
     */                        
    public function composeUrl() {
      global $webObject;
      global $phpObject;
      global $dbObject;
      $cdp = $webObject->getCurrentDynamicPath();
      
      $id = $phpObject->str_tr($cdp, "-", 1);
      
      $file = $dbObject->fetchAll("SELECT `name` FROM `file` WHERE `id` = ".$id[0].";");
      if(count($file) == 1 && ($cdp == $id[0] || $cdp == $id[0]."-".$file[0]['name'])) {
        $this->CurrentId = $id[0];
        $_SESSION['file']['current_id'] = $id[0];
        return $cdp;
      } else {
        return 'false.false';
      }
    }
    
    /**
     *
     *  Flush all images in passed dirId directory.
     *
     *  @param    pageId    next page id for display full image
     *  @param    dirId     directory id to show
     *
     */                        
    public function galleryFromDirectory($method = false, $pageId = false, $langId = false, $dirId = false, $defaultDirId = false, $showSubDirs = false, $showNames = false, $showTitles = false, $detailWidth = false, $detailHeight = false, $lightbox = false, $lightWidth = false, $lightHeight = false, $lightTitle = false, $lightId = false, $useDirectLink = false, $recursively = false, $orderFilesBy = false, $orderDirsBy = false, $desc = false) {
      global $webObject;
      global $dbObject;
      $return = "";
    
      if($dirId == false) {
        if(array_key_exists("dir-id", $_REQUEST)) {
          $dirId = $_REQUEST['dir-id'];
        } else {
        	if($defaultDirId != false) {
						$dirId = $defaultDirId;
					} else {
    	      $message = "DirId isn't set! [file:gallery]";
  	        echo "<h4 class=\"error\">".$message."</h4>";
	          trigger_error($message, E_USER_ERROR);
          }
        }
      }
      
      if($method === false || ($method != "static" && $method != "dynamic")) {
        $method = "static";
      }
      
      if($langId == false) {
				$langId = $webObject->LanguageId;
			}
			
			if($lightbox == "true") {
				// Include scripts and styles
				$return .= ''
				.'<link type="text/css" rel="stylesheet" href="~/css/jquery-lightbox.css" />'
				.'<script type="text/javascript" src="~/js/jquery/jquery.js"></script>'
				.'<script type="text/javascript" src="~/js/jquery/jquery-lightbox-pack.js"></script>'
				.'<script type="text/javascript">'."\n"
				.'$(function() {'
					.'$("#light-gallery-'.$lightId.' a").lightBox({fixedNavigation:true});'
				.'});'
				.'</script>';
			}
      
      $return .= ''
      .'<div '.(($lightbox == "true") ? 'id="light-gallery-'.$lightId.'"' : "").' class="gallery-cover">'
        .'';
      
      if($showSubDirs == "true" || $recursively == "true") {
      	$order = "name";
      	switch(strtolower($orderDirsBy)) {
					case "id": $order = "id"; break;
					case "url": $order = "url"; break;
					case "timestamp": $order = "timestamp"; break;
				}
        $dirs = $dbObject->fetchAll("SELECT `id`, `name`, `url` FROM `directory` WHERE `parent_id` = ".$dirId." ORDER BY `".$order."`".(($desc == true) ? " DESC" : "").";");
        
        //print_r($_REQUEST);
        
        $tmpDirId = $_REQUEST['dir-id'];
        $tmpDirUrl = $_REQUEST['dir-url'];
        foreach($dirs as $dir) {
        	if($pageId != false) {
						$_REQUEST['dir-id'] = $dir['id'];
						$_REQUEST['dir-url'] = $dir['url'];
						$url = $webObject->composeUrl($pageId);
					}
        
          $return .= ''
            .'<div class="gallery-item gallery-dir">'
              .'<div class="gallery-thumb">'
              .'</div>'
              .'<div class="gallery-name">'.((strlen($url) != 0) ? '<a href="'.$url.'">'.$dir['name'].'</a> ' : $dir['name']).'</div>'
			  .($recursively == "true" ? self::galleryFromDirectory($method, $pageId, $langId, $dir['id'], false, $showSubDirs, $showNames, $showTitles, $detailWidth, $detailHeight, $lightbox == "true" ? "added" : $lightbox, $lightWidth, $lightHeight, $lightTitle, $lightId, $useDirectLink, $recursively, $orderFilesBy, $orderDirsBy, $desc) : "")
            .'</div>';
        }
        $_REQUEST['dir-id'] = $tmpDirId;
        $_REQUEST['dir-url'] = $tmpDirUrl;
      }
      
      $size = '';
      if($detailWidth > 0 && $detailHeight > 0) {
        $size = 'width='.$detailWidth.'&height='.$detailHeight;
      } elseif($detailWidth > 0) {
        $size = 'width='.$detailWidth;
      } elseif($detailHeight > 0) {
        $size = 'height='.$detailHeight;
      } else {
        $size = 'height=100';
      }
      
      $lsize = '';
      if($lightWidth > 0 && $lightHeight > 0) {
        $lsize = 'width='.$lightWidth.'&height='.$lightHeight;
      } elseif($lightWidth > 0) {
        $lsize = 'width='.$lightWidth;
      } elseif($lightHeight > 0) {
        $lsize = 'height='.$lightHeight;
      } else {
        $lsize = '';
      }
      $order = "name";
      switch(strtolower($orderFilesBy)) {
				case "id": $order = "id"; break;
				case "type": $order = "type"; break;
				case "timestamp": $order = "timestamp"; break;
			}
      $images = $dbObject->fetchAll("SELECT `id`, `name`, `title`, `type` FROM `file` WHERE `dir_id` = ".$dirId." AND (`type` = ".WEB_TYPE_JPG." OR `type` = ".WEB_TYPE_GIF." OR `type` = ".WEB_TYPE_PNG.") ORDER BY `".$order."` ".(($desc == true) ? " DESC" : "").";");
      
			foreach($images as $image) {
      	if($lightbox == "true" || $lightbox == "added") {
      		$link = ''
       		.'<a href="'.(($useDirectLink != "true") ? '~/file.php?rid='.$image['id'].'&'.$lsize : self::getPhysicalPathTo($dirId, false).$image['name'].".".$this->FileEx[$image['type']]).'"'.(($lightbox == "true") ? ' rel="lightbox'.(($lightId != false) ? '['.$lightId.']' : '').'"' : '').(($lightTitle == "true") ? ' title="'.$image['title'].'"' : '').'>'
        		.'<img src="'.(($useDirectLink != "true") ? '~/file.php?rid='.$image['id'].'&'.$size : self::getPhysicalPathTo($dirId, false).$image['name'].".".$this->FileEx[$image['type']]).'" alt="'.$image['title'].'" />'
        	.'</a>';
        } else {
        	$link = ''
					.(($pageId != false) ? '<a href="'.$webObject->composeUrl($pageId, $langId).(($method == "dynamic") ? '/'.$image['id'].'-'.$image['name'] : '?file-id='.$image['id']).'">' : '')
        		.'<img src="'.(($useDirectLink != "true") ? '~/file.php?rid='.$image['id'].'&'.$size : self::getPhysicalPathTo($dirId, false).$image['name'].".".$this->FileEx[$image['type']]).'" alt="'.$image['title'].'" />'
        	.(($pageId != false) ? '</a>' : '');
				}
      
        $return .= ''
          .'<div class="gallery-item gallery-image">'
            .'<div class="gallery-thumb">'
            	.$link
            .'</div>'
            .(($showNames == "true") ? '<div class="gallery-name">'.$image['name'].'</div>' : '')
            .(($showTitles == "true") ? '<div class="gallery-title">'.$image['title'].'</div>' : '')
          .'</div>';
      }
      
      $return .= ''
				.'<div class="clear"></div>'
      .'</div>';
      
      return $return;
    }
    
    public function galleryDetail($fileId = false, $showName = false, $showTitle = false) {
      global $dbObject;
      $return = "";
    
      if($fileId == false) {
        if(array_key_exists("file-id", $_REQUEST)) {
          $fileId = $_REQUEST['file-id'];
        } elseif($this->CurrentId != 0) {
          $fileId = $this->CurrentId;
        } else {
          $message = "FileId isn't set! [file:gallery]";
          //echo "<h4 class=\"error\">".$message."</h4>";
          trigger_error($message, E_USER_ERROR);
        }
      }
      
      $image = $dbObject->fetchAll("SELECT `id`, `name`, `title` FROM `file` WHERE `id` = ".$fileId.";");
      
      if(count($image) == 1) {
        $return .= ''
        .'<div class="gallery-detail">'
          .'<div class="gallery-detail-image">'
            .'<img src="~/file.php?rid='.$image[0]['id'].'" alt="'.(strlen($image[0]['title']) != 0 ? $image[0]['title'] : $image[0]['name']).'" />'
          .'</div>'
          .(($showName == "true") ? '<div class="gallery-name">'.$image[0]['name'].'</div>' : '')
          .(($showTitle == "true") ? '<div class="gallery-title">'.$image[0]['title'].'</div>' : '')
        .'</div>';
      }
      
      return $return;
    }
    
    // ----------- PROPERTIES --------------------------- //
    
    public function setFileId($fileId) {
    	if(is_numeric($fileId)) {
				$_SESSION['file']['current_id'] = $fileId;
				return $fileId;
			} else {
				return 'wrong-file-id';
			}
		}
		
		public function getFileId() {
			return $_SESSION['file']['current_id'];
		}
    
    public function setDirectoryId($dirId) {
    	if(is_numeric($dirId)) {
				$_REQUEST['dir-id'] = $dirId;
				return $dirId;
			} else {
				return 'wrong-dir-id';
			}
		}
		
		public function getDirectoryId() {
			return $_REQUEST['dir-id'];
		}
    
    public function setDirectoryIdFromUrl($dirUrl) {
    	global $dbObject;
    	$dirs = $dbObject->fetchAll('SELECT `id` FROM `directory` WHERE `url` = "'.$dirUrl.'";');
    	if(count($dirs) > 0) {
				$_REQUEST['dir-id'] = $dirs[0]['id'];
				$_REQUEST['dir-url'] = $dirUrl;
				return $dirUrl;
			} else {
				return 'wrong-dir-url';
			}
		}
		
		public function getDirectoryUrl() {
			return $_REQUEST['dir-url'];
		}
    
  }

?>
