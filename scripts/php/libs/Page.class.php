<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Class updating web pages.     
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-05-12
   * 
   */  
  class Page extends BaseTagLib {
  
    public function __construct() {
      parent::setTagLibXml("xml/Page.xml");
    }
    
    /**
     *
     *  Generates table with informations about web pages.
     *  C tag.     
     *  
     *  @param  editable  if true, it shows also form editing
     *  @return formed list of web pages               
     *
     */                   
    public function showPages($editable = false) {
      global $dbObject;
      global $loginObject;
      global $webObject;
      $return = '';
      
      $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
      if(count($projects) != 0) {
      	if(array_key_exists('selected-project', $_SESSION)) {
					$projectId = $_SESSION['selected-project'];
					$test = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project`.`id` = '.$projectId.' AND `web_project_right`.`type` = '.WEB_R_WRITE.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
					if(count($test) == 0) {
						$projectId = $projects[0]['id']; 
					}
				} else {
					$projectId = $projects[0]['id'];
				}
			} else {
				if(array_key_exists('selected-project', $_SESSION)) {
					$projectId = $_SESSION['selected-project'];
				} else {
					return parent::getFrame("Page List", '<h4 class="error">No pages to edit!</h4>', "", true);
				}
			}
      
      if($_POST['edit-save'] == "Save" || $_POST['edit-save'] == "Save and Close") {
        $pageId = $_POST['page-id'];
        $parentId = $_POST['parent-id'];
        $languageId = $_POST['language'];
        $name = $_POST['edit-name'];
        $escapeChars = array("ě" => "e", "é" => "e", "ř" => "r", "ť" => "t", "ý" => "y", "ú" => "u", "ů" => "u", "í" => "i", "ó" => "o", "á" => "a", "š" => "s", "ď" => "d", "ž" => "z", "č" => "c", "ň" => "n", " " => "-");
        $href = strtr(strtolower($_POST['edit-href']), $escapeChars);
        $inTitle = ($_POST['edit-in-title'] == "on") ? 1 : 0;
        $visible = ($_POST['edit-visible'] == "on") ? 1 : 0;
        $menu = ($_POST['edit-menu'] == "on") ? 1 : 0;
        $head = str_replace('&amp;web:page', '&web:page', $_POST['edit-head']);
        $content = str_replace('&amp;web:page', '&web:page', $_POST['edit-content']);
        $tlStart = str_replace('&amp;web:page', '&web:page', $_POST['edit-tl-start']);
        $tlEnd = str_replace('&amp;web:page', '&web:page', $_POST['edit-tl-end']);
        $head = str_replace('&#126', '~', $head);
        $content = str_replace('&#126', '~', $content);
        $tlStart = str_replace('&#126', '~', $tlStart);
        $tlEnd = str_replace('&#126', '~', $tlEnd);
        $type = $_POST['type'];
        $keywords = $_POST['edit-keywords'];
        $clearUrlCache = $_POST['edit-clearurlcache'];
        $errors = array();
        
        $pageRightR = $_POST['right-edit-groups-r'];
        $pageRightW = $_POST['right-edit-groups-w'];
        $pageRightD = $_POST['right-edit-groups-d'];
        
        if(strlen($name) < 2) {
          $errors[] = 'Page name must have at least 2 chars!';
        }
        
	      if($type != "page-edit") {
	        $tmpPages = $dbObject->fetchAll("SELECT `id` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` WHERE `info`.`href` = \"".$href."\" AND `page`.`parent_id` = ".$parentId." AND `info`.`language_id` = ".$languageId." AND `page`.`wp` = ".$projectId.";");
	        if(count($tmpPages) != 0) {
	          $errors[] = 'Page with this href already exists in this branch!';
	        }
	      }
        
        if(count($errors) == 0) {
          if($type == "page-edit") {
            $dbObject->execute("UPDATE `content` SET `tag_lib_start` = \"".$tlStart."\", `tag_lib_end` = \"".$tlEnd."\", `head` = \"".$head."\", `content` = \"".$content."\" WHERE `page_id` = ".$pageId." AND `language_id` = ".$languageId.";");
            $dbObject->execute("UPDATE `info` SET `name` = \"".$name."\", `href` = \"".$href."\", `in_title` = \"".$inTitle."\", `in_menu` = ".$menu.", `is_visible` = ".$visible.", `keywords` = \"".$keywords."\", `timestamp` = ".time()." WHERE `page_id` = ".$pageId." AND `language_id` = ".$languageId.";");
      
     	    	if(count($pageRightR) != 0) {      
    	        $dbR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `page_right`.`pid` = ".$pageId." AND `type` = ".WEB_R_READ.";");
  	          foreach($dbR as $right) {
	              if(!in_array($right, $pageRightR)) {
                	$dbObject->execute("DELETE FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_READ.";");
              	}
            	}
          	  foreach($pageRightR as $right) {
        	      $row = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
      	        if(count($row) == 0) {
    	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_READ.")");
  	            }
	            }
            }
     	    	if(count($pageRightW) != 0) {
    	        $dbR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `page_right`.`pid` = ".$pageId." AND `type` = ".WEB_R_WRITE.";");
  	          foreach($dbR as $right) {
	              if(!in_array($right, $pageRightW)) {
                	$dbObject->execute("DELETE FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_WRITE.";");
              	}
            	}
          	  foreach($pageRightW as $right) {
        	      $row = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
      	        if(count($row) == 0) {
    	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_WRITE.")");
  	            }
	            }
            }
     	    	if(count($pageRightD) != 0) {
    	        $dbR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `page_right`.`pid` = ".$pageId." AND `type` = ".WEB_R_DELETE.";");
  	          foreach($dbR as $right) {
	              if(!in_array($right, $pageRightD)) {
                	$dbObject->execute("DELETE FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_DELETE.";");
              	}
            	}
          	  foreach($pageRightD as $right) {
        	      $row = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_DELETE." AND `gid` = ".$right.";");
      	        if(count($row) == 0) {
    	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_DELETE.")");
  	            }
	            }
            }
            
            //$return .= parent::getFrame('Success Message', '<h4 class="success">Page successfully updated!</h4>', '', true);
          } else if($type == "add-new-page") {
            $sql_return = $dbObject->fetchAll("SELECT MAX(`id`) AS `id` FROM `page`");
            
            $pageId = $sql_return[0]['id'] + 1;
            $_POST['page-id'] = $pageId;
            $languageId = $_POST['language'];
            
            $dbObject->execute("INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES(".$pageId.", ".$parentId.", ".$projectId.");");
            $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(".$pageId.", \"".$tlStart."\", \"".$tlEnd."\", \"".$head."\", \"".$content."\", ".$languageId.");");
            $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES(".$pageId.", ".$languageId.", \"".$name."\", \"".$href."\", ".$inTitle.", ".$menu.", ".$pageId.", ".$visible.", \"".$keywords."\", ".time().");");
            
     	    	if(count($pageRightR) != 0) {
	            foreach($pageRightR as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_READ.")");
    	        }
    	      }
     	    	if(count($pageRightW) != 0) {
	            foreach($pageRightW as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_WRITE.")");
    	        }
    	      }
     	    	if(count($pageRightD) != 0) {
	            foreach($pageRightD as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_DELETE.")");
    	        }
    	      }
            
            $return .= parent::getFrame("Success Message", '<h4 class="success">New page added!</h4>', "", true);
          } else if($type == "page-add-lang-ver") {
            
            $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(".$pageId.", \"".$tlStart."\", \"".$tlEnd."\", \"".$head."\", \"".$content."\", ".$languageId.");");
            $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES(".$pageId.", ".$languageId.", \"".$name."\", \"".$href."\", ".$inTitle.", ".$menu.", ".$pageId.", ".$visible.", \"".$keywords."\", ".time().");");
            
            $return .= parent::getFrame("Success Message" ,'<h4 class="success">Language version added!</h4>', "", true);
          } else if($type == "page-add-sub") {
            $sql_return = $dbObject->fetchAll("SELECT MAX(`id`) AS `id` FROM `page`");
            
            $pageId = $sql_return[0]['id'] + 1;
            $_POST['page-id'] = $pageId;
            
            $dbObject->execute("INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES(".$pageId.", ".$parentId.", ".$projectId.");");
            $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(".$pageId.", \"".$tlStart."\", \"".$tlEnd."\", \"".$head."\", \"".$content."\", ".$languageId.");");
            $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES(".$pageId.", ".$languageId.", \"".$name."\", \"".$href."\", ".$inTitle.", ".$menu.", ".$pageId.", ".$visible.", \"".$keywords."\", ".time().");");
      
     	    	if(count($pageRightR) != 0) {      
    	        foreach($pageRightR as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_READ.")");
	            }
            }
     	    	if(count($pageRightW) != 0) {
    	        foreach($pageRightW as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_WRITE.")");
	            }
            }
     	    	if(count($pageRightD) != 0) {
            	foreach($pageRightD as $right) {
              	$dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_DELETE.")");
            	}
            }
            
            $return .= parent::getFrame("Success Message", '<h4 class="success">Sub page added!</h4>', "", true);
          }
          
          if($clearUrlCache) {
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."-%\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"".$pageId."-%\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` = \"".$pageId."\" AND `language_id` = ".$languageId.";");
					}
        
          if($_POST['edit-save'] == "Save") {
            $_POST['page-edit'] = "Edit";
            $_POST['page-lang-id'] = $_POST['language'];
          }
        } else {
          //$errorList = '<ul class="error-list">';
          foreach($errors as $error) {
            $errorList .= '<h4 class="error">'.$error.'</h4>';
          }
          //$errorList .= '</ul>';
          $return .= parent::getFrame("Error Message", $errorList, "", true);
          
          $errorOccurs = "true";
          
          if($_POST['type'] == 'add-new-page') {
            $_POST['add-new-page'] = "Add new page";
          } else if($_POST['type'] == 'page-add-sub') {
            $_POST['page-add-sub'] = "Add sub page";
          } else if($_POST['type'] == 'page-add-lang-ver') {
            $_POST['page-add-lang-ver'] = "Add Language version";
          } else if($_POST['type'] == 'page-edit') {
            $_POST['page-edit'] = "Edit";
          }
        }
      }
      
      if($_POST['delete'] == "Delete") {
        $pageId = $_POST['page-id'];
        $languageId = $_POST['page-lang-id'];
          
        /*$rights = $dbObject->fetchAll("SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = ".$pageId." AND `page_right`.`type` = ".WEB_R_DELETE.";");
        $ok = true;
        if(count($rights) > 0) {
          $ok = false;
          foreach($rights as $right) {
            foreach($loginObject->getGroups() as $u_gp) {
              if($right['name'] == $u_gp['name']) {
                $ok = true;
              }
            }
          }
        }*/
        
        $rights = $dbObject->fetchAll('SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = '.$pageId.' AND `page_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
        
				$ok = true;
				if(count($rights) == 0) {
					$ok = false;
				}
        
        if($ok) {
          if($languageId != "") {
            $dbObject->execute("DELETE FROM `info` WHERE `page_id` = ".$pageId." AND `language_id` = ".$languageId.";");
            $dbObject->execute("DELETE FROM `content` WHERE `page_id` = ".$pageId." AND `language_id` = ".$languageId.";");
          
            if(count($dbObject->fetchAll("SELECT `name` FROM `info` WHERE `page_id` = ".$pageId.";")) == 0) {
              $dbObject->execute("DELETE FROM `page` WHERE `id` = ".$pageId.";");
              $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = ".$pageId.";");
              $dbObject->execute("DELETE FROM `page_file_inc` WHERE `page_id` = ".$pageId.";");
            }
        
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."-%\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"".$pageId."-%\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` = \"".$pageId."\" AND `language_id` = ".$languageId.";");
            
            $return .= parent::getFrame("Success Message", '<h4 class="success">Laguage version deleted!</h4>', "", true);
          } else {
            $dbObject->execute("DELETE FROM `info` WHERE `page_id` = ".$pageId.";");
            $dbObject->execute("DELETE FROM `content` WHERE `page_id` = ".$pageId.";");
            $dbObject->execute("DELETE FROM `page` WHERE `id` = ".$pageId.";");
            $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = ".$pageId.";");
            $dbObject->execute("DELETE FROM `page_file_inc` WHERE `page_id` = ".$pageId.";");
        
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."-%\";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"".$pageId."-%\";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."\";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` = \"".$pageId."\";");
            
            $return .= parent::getFrame("Success Message", '<h4 class="success">Page deleted!</h4>', "success", true);
          }
        } else {
          $return .= parent::getFrame('Error Message', '<h4 class="error">Permission denied</h4><div>You can\'t delete this page.</div>', "", true);
        }
      }
      
      if($_POST['move-up'] == "Up") {
        $pageId = $_POST['page-id'];
        $pagePos = 0;
        
        $pages = $dbObject->fetchAll("SELECT `page`.`id`, `info`.`page_pos` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `parent_id` = (SELECT `parent_id` FROM `page` WHERE `id` = ".$pageId.") ORDER BY `page_pos`;");
        $prevSibling = -1;
        $prevSibPos = 0;
        $i = 0;
        if(count($pages) > 1) {
          while($prevSibling == -1 || $i == count($pages)) {
            if($pages[$i]['id'] == $pageId && $i > 0) {
              $prevSibling = $pages[$i - 1]['id'];
              $prevSibPos = $pages[$i - 1]['page_pos'];
              $pagePos = $pages[$i]['page_pos'];
            }
            $i ++;
          }
        }
        
        if($prevSibling != -1) {
          $dbObject->execute("UPDATE `info` SET `page_pos` = ".$prevSibPos." WHERE `page_id` = ".$pageId.";");
          $dbObject->execute("UPDATE `info` SET `page_pos` = ".$pagePos." WHERE `page_id` = ".$prevSibling.";");
        } else {
          $return .= parent::getFrame('Error Message', '<h4 class="error">Position can\'t be updated!</h4>', '', true);
        }
      } elseif($_POST['move-down'] == "Down") {
        $pageId = $_POST['page-id'];
        $pagePos = 0;
        
        $pages = $dbObject->fetchAll("SELECT DISTINCT `page`.`id`, `info`.`page_pos` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `parent_id` = (SELECT `parent_id` FROM `page` WHERE `id` = ".$pageId.") ORDER BY `page_pos`;");
        $prevSibling = -1;
        $prevSibPos = 0;
        $i = 0;
        if(count($pages > 1)) {
          while($prevSibling == -1 || $i == count($pages)) {
            if($pages[$i]['id'] == $pageId && $i < count($pages)) {
              $prevSibling = $pages[$i + 1]['id'];
              $prevSibPos = $pages[$i + 1]['page_pos'];
              $pagePos = $pages[$i]['page_pos'];
            }
            $i ++;
          }
        }
        
        if($prevSibling != -1) {
          $dbObject->execute("UPDATE `info` SET `page_pos` = ".$prevSibPos." WHERE `page_id` = ".$pageId.";");
          $dbObject->execute("UPDATE `info` SET `page_pos` = ".$pagePos." WHERE `page_id` = ".$prevSibling.";");
        } else {
          $return .= parent::getFrame('Error Message', '<h4 class="error">Position can\'t be updated!</h4>', '', true);
        }
      } elseif($_POST['move-branch'] == "Move" || $_POST['copy-branch'] == "Copy") {
      	$pageId = $_POST['page-id'];
      	// test na prava!!!!!!!!!!!!!!!!!!!
      	
				$returnMove = '';
				
				$projects = $dbObject->fetchAll('SELECT `web_project`.`id`, `web_project`.`name` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `id`;');
				
				$strProjects = '';
				foreach($projects as $project) {
					$strProjects .= '<option class="webproject" value="wp'.$project['id'].'">'.$project['name'].'</option>';
					$pages = $dbObject->fetchAll('SELECT DISTINCT `page`.`id`, `info`.`name` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `page_right` ON `page`.`id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `page_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `page`.`wp` = '.$project['id'].' ORDER BY `info`.`page_id`;');
					$i = 0;
					foreach($pages as $page) {
						$strProjects .= '<option value="'.$page['id'].'">- '.(($page['id'] < 10) ? '0'.$page['id'] : $page['id'] ).' - '.$page['name'].'</option>';
						$i ++;
					}
				}
				
				$returnMove .= ''
				.'<div class="move-copy-branch">'
					.'<form name="move-copy-branch" method="post" action="">'
						.'<label for="select-parent">Select parent page of branch:</label> '
						.'<select class="select-webproject" name="select-parent" id="select-parent">'
							.$strProjects
						.'</select> '
						.'<input type="hidden" name="page-id" value="'.$pageId.'" />'
						.(($_POST['move-branch'] == "Move") ? ''
						.'<input type="submit" name="move-branch-to" value="Move to" />'
						: ''
						.'<input type="submit" name="copy-branch-to" value="Copy to" />'
						)
					.'</form>'
				.'</div>';
				
				$return .= parent::getFrame((($_POST['move-branch'] == "Move") ? 'Move branch' : 'Copy branch'), $returnMove, '', true);
			} elseif($_POST['move-branch-to'] == "Move to") {
				$selectParent = $_POST['select-parent'];
				$pageId = $_POST['page-id'];
      	// test na prava zapisu stranky !!!!!!!!!!!!!!!!!!!
      	$ok = true;
      	$pages = $dbObject->fetchAll('SELECT `page`.`wp`, `page_right`.`gid` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `page_right` ON `page`.`id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `page_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `page`.`id` = '.$pageId.';');
      	if(count($pages) != 0) {
					if(substr($selectParent, 0, 2) == "wp") {
						$projectID = substr($selectParent, 2, strlen($selectParent));
						$parentId = 0;
					} else {
						$parentId = $selectParent;
						$projectID = $dbObject->fetchAll('SELECT `wp` FROM `page` WHERE `id` = '.$parentId.';');
						if(count($projectID) != 0) {
							$projectID = $projectID[0]['wp'];
						} else {
							$ok = false;
						}
					}
					if($ok) {
						// test zapisu do projektu
						$projects = $dbObject->fetchAll('SELECT `web_project_right`.`gid` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `web_project_right`.`wp` = '.$projectID.';');
						if(count($projects) != 0) {
							if($pages[0]['wp'] == $projectID) {
								$dbObject->execute('UPDATE `page` SET `parent_id` = '.$parentId.' WHERE `id` = '.$pageId.';');
							} else {
								$dbObject->execute('UPDATE `page` SET `parent_id` = '.$parentId.', `wp` = '.$projectID.' WHERE `id` = '.$pageId.';');
								// Zavolat fci pro rekurzivni prespsani projektu u podstranek.
								self::rewriteProjectIdRecursivly($pageId, $projectID);
							}
						} else {
							$return .= parent::getFrame('Error Message', '<h4 class="error">Permission Denied!</h4>', '', true);
						}
					} else {
						$return .= parent::getFrame('Error Message', '<h4 class="error">Some error ocurs!</h4>', '', true);
					}
				} else {
					$return .= parent::getFrame('Error Message', '<h4 class="error">Permission Denied!</h4>', '', true);
				}
			} elseif($_POST['copy-branch-to'] == "Copy to") {
				// Code for copying pages!! ;)
			
				$selectParent = $_POST['select-parent'];
				$pageId = $_POST['page-id'];
      	// test na prava zapisu stranky !!!!!!!!!!!!!!!!!!!
      	$ok = true;
      	$pages = $dbObject->fetchAll('SELECT `page`.`wp`, `info`.`href`, `page_right`.`gid` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `page_right` ON `page`.`id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `page_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `page`.`id` = '.$pageId.';');
      	$pagesUrl = '';
      	if(count($pages) != 0) {
      		$first = true;
      		foreach($pages as $page) {
      			if($first) {
      				$pagesUrl .= '"'.$page['href'].'"';
      				$first = false;
      			} else {
							$pagesUrl .= ', "'.$page['href'].'"';
						}
      		}
					if(substr($selectParent, 0, 2) == "wp") {
						$projectID = substr($selectParent, 2, strlen($selectParent));
						$parentId = 0;
					} else {
						$parentId = $selectParent;
						$projectID = $dbObject->fetchAll('SELECT `wp` FROM `page` WHERE `id` = '.$parentId.';');
						if(count($projectID) != 0) {
							$projectID = $projectID[0]['wp'];
						} else {
							$ok = false;
						}
					}
					
					if($ok) {
						// test zapisu do projektu
						$projects = $dbObject->fetchAll('SELECT `web_project_right`.`gid` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `web_project_right`.`wp` = '.$projectID.';');
						if(count($projects) != 0) {
							if($pages[0]['wp'] == $projectID) {
								// Testovat parentId stranka, testovat url v dane parent vetvi, kopirovat i vazby na TF
								$urls = $dbObject->fetchAll('SELECT `href` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id`  WHERE `page`.`parent_id` = '.$parentId.' AND `info`.`href` IN ('.$pagesUrl.') AND `page`.`wp` = '.$projectID.';');
								if(count($urls) == 0) {
									// neni treba menit url, je jedinecna v dane sekci
									// rekurzivne zkopirovat vsechny stranky atd.
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
									$lastId = 0;
									$newId = 0;
									foreach($pages as $page) {
										if($lastId != $page['id']) {
											$newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
											$newId = $newId[0]['id'] + 1;
											$dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES ('.$newId.', '.$parentId.', '.$projectId.');');
											$rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = '.$page['id'].';');
											foreach($rights as $right) {
												$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$newId.', '.$right['gid'].', '.$right['type'].');');
											}
											self::copyPagesRecursivly($page['id'], $newId, $projectID, true);
										}
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$page['href'].'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= parent::getFrame('Copied', '<h4 class="success">Pages have been copied!</h4>', '', true);
								} else {
									// zmenit url na nahodnou a vypsat ji.
									// rekurzivne zkopirovat vsechny stranky atd.
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
									$lastId = 0;
									$newId = 0;
									$randUrl = 'random-url-'.rand(100, 1000).rand(100, 1000);
									foreach($pages as $page) {
										if($lastId != $page['id']) {
											$newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
											$newId = $newId[0]['id'] + 1;
											$dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES ('.$newId.', '.$parentId.', '.$projectId.');');
											$rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = '.$page['id'].';');
											foreach($rights as $right) {
												$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$newId.', '.$right['gid'].', '.$right['type'].');');
											}
											self::copyPagesRecursivly($page['id'], $newId, $projectID, true);
										}
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$randUrl.'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= parent::getFrame('Copied', '<h4 class="success">Pages have been copied!</h4><h4 class="warning">Url has been changed to "'.$randUrl.'"</h4>', '', true);
								}
							} else {
								// Testovat url v dane parent vetvi, nekopirovat vazby na TF
								$urls = $dbObject->fetchAll('SELECT `href` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id`  WHERE `page`.`parent_id` = '.$parentId.' AND `info`.`href` IN ('.$pagesUrl.') AND `page`.`wp` = '.$projectID.';');
								if(count($urls) == 0) {
									// neni treba menit url, je jedinecna v dane sekci
									// rekurzivne zkopirovat vsechny stranky atd.
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
									$lastId = 0;
									$newId = 0;
									foreach($pages as $page) {
										if($lastId != $page['id']) {
											$newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
											$newId = $newId[0]['id'] + 1;
											$dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES ('.$newId.', '.$parentId.', '.$projectID.');');
											$rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = '.$page['id'].';');
											foreach($rights as $right) {
												$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$newId.', '.$right['gid'].', '.$right['type'].');');
											}
											self::copyPagesRecursivly($page['id'], $newId, $projectID, true);
										}
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$page['href'].'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= parent::getFrame('Copied', '<h4 class="success">Pages have been copied!</h4>', '', true);
								} else {
									// zmenit url na nahodnou a vypsat ji.
									// rekurzivne zkopirovat vsechny stranky atd.
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
									$lastId = 0;
									$newId = 0;
									$randUrl = 'random-url-'.rand(100, 1000).rand(100, 1000);
									foreach($pages as $page) {
										if($lastId != $page['id']) {
											$newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
											$newId = $newId[0]['id'] + 1;
											$dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES ('.$newId.', '.$parentId.', '.$projectID.');');
											$rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = '.$page['id'].';');
											foreach($rights as $right) {
												$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$newId.', '.$right['gid'].', '.$right['type'].');');
											}
											self::copyPagesRecursivly($page['id'], $newId, $projectID, true);
										}
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$randUrl.'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= parent::getFrame('Copied', '<h4 class="success">Pages have been copied!</h4><h4 class="warning">Url has been changed to "'.$randUrl.'"</h4>', '', true);
								}
							}
						} else {
							$return .= parent::getFrame('Error Message', '<h4 class="error">Permission Denied!</h4>', '', true);
						}
					} else {
						$return .= parent::getFrame('Error Message', '<h4 class="error">Some error ocurs!</h4>', '', true);
					}
				} else {
					$return .= parent::getFrame('Error Message', '<h4 class="error">Permission Denied!</h4>', '', true);
				}
			}
      
      if($_POST['page-edit'] == "Edit" || $_POST['add-new-page'] == "Add new page" || $_POST['page-add-sub'] == "Add sub page" || $_POST['page-add-lang-ver'] == "Add Language version" || $errorOccurs == "true") {
        $usedLangs = array();
        $pageId = $_POST['page-id'] | 0;
        $parentId = $_POST['parent-id'];
        $langId = $_POST['page-lang-id'];
        $langsCount = true;
        
        $rights = $dbObject->fetchAll('SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = '.$pageId.' AND `page_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
        
				$ok = true;
				if(count($rights) == 0) {
					$ok = false;
				}
        /*if(count($rights) > 0) {
          $ok = false;
          foreach($rights as $right) {
            foreach($loginObject->getGroups() as $u_gp) {
              if($right['name'] == $u_gp['name']) {
                $ok = true;
              }
            }
          }
        }*/
        
        if($ok) {
          $right_pid = $pageId;
          if($_POST['page-edit'] == "Edit") {
            $type = "page-edit";
          } else if($_POST['add-new-page'] == "Add new page") {
            $type = "add-new-page";
            $right_pid = $parentId;
            $frameTitle = 'Add new page';
          } else if($_POST['page-add-sub'] == "Add sub page") {
            $type = "page-add-sub";
            $frameTitle = 'Add sub page';
          } else if($_POST['page-add-lang-ver'] == "Add Language version") {
            $type = "page-add-lang-ver";
            $parentId = $pageId;
            $usedLangs = $dbObject->fetchAll("SELECT `language_id` FROM `info` WHERE `page_id` = ".$pageId.";");
            $frameTitle = 'Add language version';
          } else {
            $type = "undefined";
          }
          if($_POST['page-edit'] == "Edit") {
            $sql_return = $dbObject->fetchAll("SELECT `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content`, `info`.`name`, `info`.`href`, `info`.`in_title`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords` FROM `content` LEFT JOIN `info` ON `content`.`page_id` = `info`.`page_id` AND `info`.`language_id` = `content`.`language_id` WHERE `info`.`page_id` = ".$pageId." AND `info`.`language_id` = ".$langId.";");
            $frameTitle = 'Edit page :: '.$sql_return[0]['name'];
          } else {
            $sql_return = array();
            $sql_return[0]['in_title'] = 1;
            $sql_return[0]['is_visible'] = 1;
          }
          
          if($errorOccurs == "true") {
            $sql_return[0]['name'] = $name;
            $sql_return[0]['href'] = $href;
            $sql_return[0]['in_title'] = $inTitle;
            $sql_return[0]['in_menu'] = $inMenu;
            $sql_return[0]['is_visible'] = $isVisible;
            $sql_return[0]['head'] = $head;
            $sql_return[0]['content'] = $content;
            $sql_return[0]['tag_lib_start'] = $tlStart;
            $sql_return[0]['tag_lib_end'] = $tlEnd;
            $langId = $languageId;
          }
          
          if($type == 'add-new-page') {
  	        $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$projectId." AND `type` = ".WEB_R_READ.";");
	          $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$projectId." AND `type` = ".WEB_R_WRITE.";");
          	$groupsD = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$projectId." AND `type` = ".WEB_R_DELETE.";");
					} else {
    	      $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$right_pid." AND `type` = ".WEB_R_READ.";");
  	        $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$right_pid." AND `type` = ".WEB_R_WRITE.";");
	          $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$right_pid." AND `type` = ".WEB_R_DELETE.";");
					}
          
          $show = array('read' => true, 'write' => true, 'delete' => false);
          $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
          $groupSelectR = '<select name="right-edit-groups-r[]" multiple="multiple" size="5">';
          $groupSelectW = '<select name="right-edit-groups-w[]" multiple="multiple" size="5">';
          $groupSelectD = '<select name="right-edit-groups-d[]" multiple="multiple" size="5">';
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
          
          $return .= '';
          if(($type != "undefined" && (count($sql_return) == 1) || $type != "Edit")) {
            $sql_return[0]['tag_lib_start'] = str_replace("&", "&amp;", $sql_return[0]['tag_lib_start']);
            $sql_return[0]['tag_lib_end'] = str_replace("&", "&amp;", $sql_return[0]['tag_lib_end']);
            $sql_return[0]['head'] = str_replace("&", "&amp;", $sql_return[0]['head']);
            $sql_return[0]['content'] = str_replace("&", "&amp;", $sql_return[0]['content']);
            $sql_return[0]['head'] = str_replace(">", "&gt;", $sql_return[0]['head']);
            $sql_return[0]['head'] = str_replace("<", "&lt;", $sql_return[0]['head']);
            $sql_return[0]['content'] = str_replace(">", "&gt;", $sql_return[0]['content']);
            $sql_return[0]['content'] = str_replace("<", "&lt;", $sql_return[0]['content']);
            
            $returnTmp .= ''
                      .'<form name="edit" method="post" action="">'
                      .'<div class="edit edit-page-info">'
                        .'<div class="edit edit-prop">'
                          .'<div class="edit edit-name">'
                            .'<lable for="edit-name">Name:</label> '
                            .'<input type="text" name="edit-name" value="'.$sql_return[0]['name'].'" />'
                          .'</div>'
                          .'<div class="edit edit-href">'
                            .'<lable for="edit-href">Href:</label> '
                            .'<input type="text" name="edit-href" value="'.$sql_return[0]['href'].'" />'
                          .'</div>'
                          .'<div class="edit edit-in-title">'
                            .'<lable for="edit-in-title">in title:</label> '
                            .'<input type="checkbox" name="edit-in-title"'.(($sql_return[0]['in_title'] == 1) ? 'checked="checked"' : '').' />'
                          .'</div>'
                          .'<div class="edit edit-menu">'
                            .'<lable for="edit-menu">in menu:</label> '
                            .'<input type="checkbox" name="edit-menu"'.(($sql_return[0]['in_menu'] == 1) ? 'checked="checked"' : '').' />'
                          .'</div>'
                          .'<div class="edit edit-visible">'
                            .'<lable for="edit-visible">is visible:</label> '
                            .'<input type="checkbox" name="edit-visible"'.(($sql_return[0]['is_visible'] == 1) ? 'checked="checked"' : '').' />'
                          .'</div>'
                          .'<div class="edit edit-clear-cache">'
                            .'<lable for="edit-clearurlcache">clear urlcache:</label> '
                            .'<input type="checkbox" name="edit-clearurlcache" />'
                          .'</div>';
            if($type == "add-new-page" || $type == "page-add-lang-ver") {
              $returnTmp .= 
                          '<div class="edit edit-language">'
                          .'<label for="language">Laguage: </label>'
                          .'<select name="language">';
              $parentPage = $dbObject->fetchAll('SELECT `parent_id` FROM `page` WHERE `id` = '.$pageId.';');
              if($type == "page-add-lang-ver" && $parentPage[0]['parent_id']) {
								$langs = $dbObject->fetchAll("SELECT `language`.`id`, `language`.`language` FROM `language` LEFT JOIN `info` ON `language`.`id` = `info`.`language_id` WHERE `info`.`page_id` = ".$parentPage[0]['parent_id']." ORDER BY `language`.`language`;");
							} else {
              	$langs = $dbObject->fetchAll("SELECT `language`.`id`, `language`.`language` FROM `language` ORDER BY `language`.`language`;");
              	
							} 
							$iOk = 0; 
              foreach($langs as $lang) {
                $ok = true;
                foreach($usedLangs as $usedLang) {
                  if(in_array($lang['id'], $usedLang)) {
                    $ok = false;
                  }
                }
                if($ok) {
                  $returnTmp .= '<option value="'.$lang['id'].'">'.$lang['language'].'</option>';
                  $iOk ++;
                }
              }
              
              if($iOk == 0) {
								$langsCount = false;
							}
              $returnTmp .= '</select></div>';
            }
                $returnTmp .= ''
                        .'</div>'
                        .'<div class="edit edit-rights">'
                        	.(($show['read']) ? ''
                          .'<div class="edit edit-right-read">'
                            .'<label for="right-edit-groups-r">Read</label>'
                            .$groupSelectR
                          .'</div>'
                          : '')
                          .(($show['write']) ? ''
                          .'<div class="edit edit-right-write">'
                            .'<label for="right-edit-groups-w">Write</label>'
                            .$groupSelectW
                          .'</div>'
                          : '')
                          .(($show['delete']) ? ''
                          .'<div class="edit edit-right-delete">'
                            .'<label for="right-edit-groups-d">Delete</label>'
                            .$groupSelectD
                          .'</div>'
                          : '')
                          .'<div class="clear"></div>'
                        .'</div>'
                        .'<div class="clear"></div>'
                        .'<div class="edit edit-keywords">'
                          .'<lable for="edit-keywords">Key words:</label> '
                          .'<input type="text" name="edit-keywords" value="'.$sql_return[0]['keywords'].'" />'
                        .'</div>'
                        .'<div class="clear"></div>'
                      .'</div>'
                      .'<div class="clear"></div>'
                      .'<div class="edit edit-tag-lib">'
                          .'<div class="edit edit-tl-start">'
                            .'<label for="edit-tl-start">Tag lib start:</label>'
                            .'<div class="editor-cover">'
                            	.'<div class="textarea-cover">'
                            		.'<textarea name="edit-tl-start" class="editor-textarea editor-closed" wrap="off" rows="4">'.str_replace('~', '&#126', $sql_return[0]['tag_lib_start']).'</textarea>'
                            	.'</div>'
                            	.'<div class="clear"></div>'
                            .'</div>'
                          .'</div>'
                          .'<div class="edit edit-tl-end">'
                            .'<label for="edit-tl-end">Tag lib end:</label>'
                            .'<div class="editor-cover">'
                            	.'<div class="textarea-cover">'
                            		.'<textarea name="edit-tl-end" class="editor-textarea editor-closed" wrap="off" rows="4">'.str_replace('~', '&#126', $sql_return[0]['tag_lib_end']).'</textarea>'
                            	.'</div>'
                            	.'<div class="clear"></div>'
                            .'</div>'
                          .'</div>'
                      .'</div>'
                      .'<div class="edit edit-content">'
                          .'<div class="edit edit-head">'
                            .'<label for="edit-head">Head:</label>'
                            .'<div class="editor-cover">'
                            	.'<div class="textarea-cover">'
                            		.'<textarea name="edit-head" class="editor-textarea editor-closed" wrap="off" rows="4">'.str_replace('~', '&#126', $sql_return[0]['head']).'</textarea>'
                            	.'</div>'
                            	.'<div class="clear"></div>'
                            .'</div>'
                          .'</div>'
                          .'<div class="edit edit-content">'
                            .'<label for="edit-content">Content:</label>'
                            .'<div class="editor-cover">'
                            	.'<div class="textarea-cover">'
                            		.'<textarea name="edit-content" class="editor-textarea editor-tiny" wrap="off" rows="15">'.str_replace('~', '&#126', $sql_return[0]['content']).'</textarea>'
                            	.'</div>'
                            	.'<div class="clear"></div>'
                            .'</div>'
                          .'</div>'
                      .'</div>'
                      .'<div class="edit edit-submit">'
                        .'<input type="hidden" name="parent-id" value="'.$parentId.'" />'
                        .'<input type="hidden" name="page-id" value="'.$pageId.'" />';
            if($type != "add-new-page" && $type != "page-add-lang-ver") {
              $returnTmp .= '<input type="hidden" name="language" value="'.$langId.'" />';
            }
            $returnTmp .= '<input type="hidden" name="type" value="'.$type.'" />'
                        .'<input type="submit" name="edit-save" value="Save" /> '
                        .'<input type="submit" name="edit-save" value="Save and Close" /> '
                        .'<input type="submit" name="edit-close" value="Close" /> '
                      .'</div>'
                    .' </form>';
          } else {
            $returnTmp .= '<h4 class="error">No page selected!</h4>';
          }
          //$returnTmp .= '</div>';
          
          if($langsCount) {
          	$return .= parent::getFrame($frameTitle, $returnTmp, "");
          } else {
						$return .= parent::getFrame($frameTitle, '<h4 class="error">You can\'t add more language versions at this moment! Please, first, add language version to parent page or if this is root page, create more language versions in web application!</h4>', "");
					}
        } else {
          $return .= parent::getFrame('Error Message', '<h4 class="error">Permission denied</h4><div>You can\'t write this page.</div>', "", true);
        }
      } 
			
			if($_POST['remove-file'] == "Remove") {
        $fileId = $_POST['file-id'];
        $pageId = $_POST['page-id'];
        $langId = $_POST['page-lang-id'];
        
        $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = ".$fileId." AND `page_id` = ".$pageId." AND `language_id` = ".$langId.";");
        
        $_POST['added-files'] = "Added files";
      } elseif($_POST['add-files'] == "Add selected") {
        //print_r($_POST);
        $pageId = $_POST['page-id'];
        $langId = $_POST['page-lang-id'];
        $files = $_POST['files'];
        
        foreach($files as $file => $val) {
          if($val = "on") {
            $dbObject->execute("INSERT INTO `page_file_inc`(`file_id`, `page_id`, `language_id`) VALUES (".$file.", ".$pageId.", ".$langId.");");
          }
        }
        //$return .= parent::getFrame("Success Message", '<h4 class="success">Files successfully inserted!</h4>', "", true);
        
        $_POST['added-files'] = "Added files";
      }
			
			if($_POST['added-files'] == "Added files") {
        $pageId = $_POST['page-id'];
        $langId = $_POST['page-lang-id'];
        $filesEx = array(WEB_TYPE_CSS => "Css", WEB_TYPE_JS => "Js");
        
        $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` LEFT JOIN `page_file_inc` ON `page_file`.`id` = `page_file_inc`.`file_id` WHERE `page_file_inc`.`page_id` = ".$pageId." AND `page_file_inc`.`language_id` = ".$langId.";");
        
        if(count($files) != 0) {
        	$returnTmp .= ''
                .'<table class="page-file-list">'
                  .'<tr class="file-tr">'
                    .'<th colspan="4" class="file-head-th">Added Files</th>'
                  .'</tr>';
        	foreach($files as $file) {
          $returnTmp .= '<tr class="file-tr">'
                      .'<td class="file-name">'
                          .$file['name']
                      .'</td>'
                      .'<td class="file-content">'
                        .'<div class="file-content-in"><div class="foo">'.substr($file['content'], 0, 130).'</div></div>'
                      .'</td>'
                      .'<td class="file-type">'
                        .$filesEx[$file['type']]
                      .'</td>'
                      .'<td>'
                        .(($editable) ? ''
                        .'<form name="process-file" method="post" action="">'
                          .'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                          .'<input type="hidden" name="page-id" value="'.$pageId.'" />'
                          .'<input type="hidden" name="page-lang-id" value="'.$langId.'" />'
                          .'<input type="hidden" name="remove-file" value="Remove" />'
                          .'<input type="image" src="'.WEB_ROOT.'images/page_del.png" name="remove-file" value="Remove" title="Remove file" />'
                        .'</form>'
                        : '')
                      .'</td>'
                    .'</tr>';
      	  }
    	    $returnTmp .= '</table>';
  	      //$return1 = parent::getFrame('Added files', $returnTmp, '');
	        $return1 = $returnTmp;
        } else {
					$return1 = '<h4 class="error">No files added!</h4>';
				}
                  
        
        $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` LEFT JOIN `page_file_inc` ON `page_file`.`id` = `page_file_inc`.`file_id` WHERE `id` NOT IN (SELECT `file_id` FROM `page_file_inc` WHERE `page_id` = ".$pageId." AND `language_id` = ".$langId.") AND `wp` = ".$_SESSION['selected-project']." ORDER BY `id`;");
        if(count($files) != 0) {
  	      $returnTmp = ''
                .'<form name="files-to-add" method="post" action="">'
                .'<table class="page-file-list">'
                  .'<tr class="file-tr">'
                    .'<th colspan="4" class="file-head-th">Files to Add</th>'
                  .'</tr>';
	        $i = 0;
        	foreach($files as $file) {
      	    $returnTmp .= '<tr class="file-tr">'
                      .'<td class="file-name">'
                          .$file['name']
                      .'</td>'
                      .'<td class="file-content">'
                        .'<div class="file-content-in"><div class="foo">'.substr($file['content'], 0, 130).'</div></div>'
                      .'</td>'
                      .'<td class="file-type">'
                        .$filesEx[$file['type']]
                      .'</td>'
                      .'<td>'
                        .'<input type="checkbox" name="files['.$file['id'].']" />'
                      .'</td>'
                    .'</tr>';
    	      $i ++;
  	      }
	        $returnTmp .= '</table>'
                  .'<input type="hidden" name="page-id" value="'.$pageId.'" />'
                  .'<input type="hidden" name="page-lang-id" value="'.$langId.'" />'
                  .'<input type="submit" name="add-files" value="Add selected" />'
                  .'</form>';
        	//$return2 = parent::getFrame('Files to add', $returnTmp, '');
        	$return2 = $returnTmp;
        } else {
					$return2 = '<h4 class="error">No files to add!</h4>';
				}
        $return .= parent::getFrame('Text Files', $return1.$return2, '');
      }
      
      if($_POST['select-lang'] == "Select") {
        $_SESSION['language'] = $_POST['language'];
        $langQuery = " `language`.`id` = ".$_SESSION['language'];
      }
      
      if(isset($_SESSION['language'])) {
        $langQuery = " `language`.`id` = ".$_SESSION['language'];
      } else {
        $_SESSION['language'] = 1;
        $langQuery = " `language`.`id` = ".$_SESSION['language'];
      }
      
      $langs = $dbObject->fetchAll("SELECT `id`, `language` FROM `language` ORDER BY `language`;");
      
      $returnTmp = ''
                .'<div class="page-list">';
                
      $returnTmp .= '<div class="pages-list-in">';
      $returnTmp .= self::generatePageList(0, $editable, 0, $projectId);
      $returnTmp .= '</div></div>';
      $return .= parent::getFrame('Page List', $returnTmp, '');
      
      if($_SESSION['selected-project'] != null) {
      	$returnTmp = ''
				.'<div class="add-page">'
	        .'<form name="add-page" method="post" action="">'
  	      	.'<input type="hidden" name="parent-id" value="0" />'
    	  		.'<input type="submit" name="add-new-page" value="Add new page" />'
    		  .'</form>'
  	    .'</div>';
	      $return .= parent::getFrame('New Page', $returnTmp, '');
      }
      
      return $return;
    }
    
    /**
     *
     *  Generates page list.
     *  
     *  @param    parentId  parent page
     *  @param    editable  if true, it shows forms for editing
     *  @param    inn       number of inner recursion
     *  @param		projectId	web project id     
     *  @return   page list          
     *
     */                        
    private function generatePageList($parentId, $editable, $inn, $projectId) {
      global $dbObject;
      global $webObject;
      
      $sql_return = $dbObject->fetchAll("SELECT `page`.`parent_id`, `page`.`id` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`parent_id` = ".$parentId." AND `page`.`wp` = ".$projectId." GROUP BY `page`.`id` ORDER BY `info`.`page_pos`;");
      if(count($sql_return) == 0 && $parentId == 0) {
				return '<h4 class="error">No pages to show!</h4>';
			}
      if(count($sql_return) > 0) $return .= '<ul class="inn-'.$inn.'">';
      $count = 0;
      foreach($sql_return as $tmp) {
        if(count($dbObject->fetchAll("SELECT `id` FROM `page` WHERE `parent_id` = ".$tmp['id']." AND `wp` = ".$projectId.";")) == 0) {
          $parent = false;
        } else {
          $parent = true;
        }
        if($count == (count($sql_return) - 1)) {
          $last = " last";
        } else {
          $last = "";
        }
        $pg_info = $dbObject->fetchAll("SELECT `name`, `language`, `language`.`id` AS `lang_id` FROM `info` LEFT JOIN `language` ON `info`.`language_id` = `language`.`id` WHERE `info`.`page_id` = ".$tmp['id'].";");
        if(count($pg_info) > 0) {
          $count ++;
          $innText = '<span class="page-id-col" title="Page Id">('.$tmp['id'].')</span> <span class="page-name" title="Page Name">'.$pg_info[0]['name'].'</span> : '.'<span class="page-languages">( ';
          foreach($pg_info as $inf) {
          	$parentLang = $dbObject->fetchAll('SELECT `page_id` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` WHERE `language_id` = '.$inf['lang_id'].' AND `page`.`parent_id` = '.$tmp['id'].';');
          	if(count($parentLang) > 0) {
							$thisParent = true;
						} else {
							$thisParent = false;
						}
						
						//echo 'PageId: '.$tmp['id'].', Parent: '.$parent.', ThisParent: '.$thisParent.'<br />';
						
          	$innText .= '' 
						.'<div class="page-language-version"> { '
							.'<span class="page-language">'
								.'<a target="_blank" href="'.$webObject->composeUrl($tmp['id'], $inf['lang_id']).'">'.((strlen($inf['language']) != 0) ? $inf['language'] : "-").'</a>'
							.'</span>'
							.'<form name="page1" method="post" action="">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="page-edit" value="Edit" /> '
                .'<input type="image" title="Edit" src="'.WEB_ROOT.'images/page_edi.png" name="page-edit" value="Edit" /> '
              .'</form>'
							.'<form name="page2" method="post" action="">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="page-add-sub" value="Add sub page" /> '
                .'<input type="image" title="Add sub page" src="'.WEB_ROOT.'images/page_add.png" name="page-add-sub" value="Add sub page" /> '
              .'</form>'
							.'<form name="page3" method="post" action="">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="added-files" value="Added files" /> '
                .'<input type="image" title="Included files" src="'.WEB_ROOT.'images/file_bws.png" name="added-files" value="Added files" /> '
              .'</form>'
              .((!$parent || !$thisParent) ? ''
							.'<form name="page4" method="post" action="">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="delete" value="Delete" /> '
                .'<input class="confirm" type="image" title="Delete language version" src="'.WEB_ROOT.'images/lang_del.png" name="delete" value="Delete" />'
              .'</form>'
              : '')
						.'</div> } ';
          }
          $innText .= ''
          .'[ '
					.'<form name="page-move1" method="post" action="">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" />'
          	.'<input type="hidden" name="move-branch" value="Move" />'
            .'<input type="image" src="'.WEB_ROOT.'images/page_mov.png" title="Move Branch" name="move-branch" value="Move" />'
          .'</form> '
					.'<form name="page-move2" method="post" action="">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" />'
          	.'<input type="hidden" name="copy-branch" value="Copy" />'
            .'<input type="image" src="'.WEB_ROOT.'images/page_cop.png" title="Copy Branch" name="copy-branch" value="Copy" />'
          .'</form> '
					.'<form name="page-move3" method="post" action="">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="move-up" value="Up" /> '
            .'<input type="image" src="'.WEB_ROOT.'images/arro_up.png" title="Move Page Up" name="move-up" value="Up" />'
          .'</form>'
					.'<form name="page-move4" method="post" action="">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="move-down" value="Down" /> '
            .'<input type="image" src="'.WEB_ROOT.'images/arro_do.png" title="Move Page Down" name="move-down" value="Down" />'
          .'</form>'
          .'] '
					.'<form name="page-add-lang1" method="post" action="">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="page-add-lang-ver" value="Add Language version" /> '
            .'<input type="image" title="Add language version" src="'.WEB_ROOT.'images/lang_add.png" name="page-add-lang-ver" value="Add Language version" /> '
          .'</form>'
          .((count($dbObject->fetchAll("SELECT `id` FROM `page` WHERE `parent_id` = ".$tmp['id'].";")) == 0) ? ''
					.'<form name="page-add-lang2" method="post" action="">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="delete" value="Delete" /> '
            .'<input class="confirm" type="image" title="Delete page" src="'.WEB_ROOT.'images/page_del.png" name="delete" value="Delete" />'
          .'</form>'
          : '')
          .' )</span>';
          $return .= ''
					.'<li class="page page-item-'.$count.' inn-'.$inn.(($parent) ? ' parent' : ' single').$last.'">'
          	.(($editable) ? '<div><div><span class="page page-id">'.$innText.'</span></div></div>' : '')
          	.self::generatePageList($tmp['id'], $editable, $inn + 1, $projectId)
          .'</li>';
        }
      }
      if(count($sql_return) > 0) $return .= '</ul>';
      
      return $return;
    }
    
    /**
     *
     *	Recursivly rewrites project id every page under pageId.
     *	
     *	@param	pageId			root page id
     *	@param	projectId		web project id
     *	@return	none     
     *
     */		 		 		     
    private function rewriteProjectIdRecursivly($pageId, $projectId) {
    	global $dbObject;
    	
			$pages = $dbObject->fetchAll('SELECT `id` FROM `page` WHERE `parent_id` = '.$pageId.';');
			foreach($pages as $page) {
				$dbObject->execute('UPDATE `page` SET `wp` = '.$projectId.' WHERE `id` = '.$page['id'].';');
				self::rewriteProjectIdRecursivly($page['id'], $projectId);
			}
		}
		
		// Jeste doresit kopirovat pageFileInc
		private function copyPagesRecursivly($parentId, $newParentId, $projectId, $pageFileInc = false) {
			global $dbObject;
			
			if($pageFileInc) {
				$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`parent_id` = '.$parentId.' ORDER BY `page`.`id`;');
				$lastId = 0;
				$newId = 0;
				foreach($pages as $page) {
					if($lastId != $page['id']) {
						$newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
						$newId = $newId[0]['id'] + 1;
						$dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES ('.$newId.', '.$newParentId.', '.$projectId.');');
						$rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = '.$page['id'].';');
						foreach($rights as $right) {
							$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$newId.', '.$right['gid'].', '.$right['type'].');');
						}
						self::copyPagesRecursivly($page['id'], $newId, $projectId, $pageFileInc);
					}
					$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$page['href'].'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().');');
					$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
					
					$lastId = $page['id'];
				}
			} else {
			
			}
		}
    
    /**
     *
     *  Generates table with informations about page files.
     *  C tag.     
     *  
     *  @param  editable  if true, it shows also form editing
     *  @return formed list of page files
     *
     */                        
    public function showPageFiles($editable = false) {
      global $dbObject;
      global $loginObject;
      $editable = (strtolower($editable) == "true") ? true : false;
      $return = "";
      $filesEx = array(WEB_TYPE_CSS => "Css", WEB_TYPE_JS => "Js");
      
      $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
      if(count($projects) != 0) {
      	if(array_key_exists('selected-project', $_SESSION)) {
					$projectId = $_SESSION['selected-project'];
					/*$test = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project`.`id` = '.$projectId.' AND `web_project_right`.`type` = '.WEB_R_WRITE.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
					if(count($test) == 0) {
						$projectId = $_SESSION['selected-project'];
					} else {
						$projectId = $projects[0]['id'];
					}*/
				} else {
					$projectId = $projects[0]['id'];
				}
			} else {
				if(array_key_exists('selected-project', $_SESSION)) {
					$projectId = $_SESSION['selected-project'];
				} else {
					return parent::getFrame("Text file list", '<h4 class="error">No files to edit!</h4>', "", true);
				}
			}
      
      if($_POST['add-file'] == "New file") {
        $fileTypesOpt = "";
        foreach($filesEx as $key => $ext) {
          $fileTypesOpt .= '<option value="'.$key.'">'.$ext.'</option>';
        }  
        
        $browsers['All'] = 1;
        $browsers['IE6'] = 0;
        $browsers['IE7'] = 0;
        $browsers['IE8'] = 0;
        $browsers['Firefox'] = 0;
        $browsers['Opera'] = 0;
        $browsers['Safari'] = 0;
          
        $return .= self::getFileUpdateForm(-1, '', '', $browsers, $fileTypesOpt);
      }
      
      if($_POST['save'] == "Save" || $_POST['save'] == "Save and Close") {
        if(array_key_exists("file-id", $_POST)) {
          $fileId = $_POST['file-id'];
        }
        $fileName = $_POST['file-name'];
        $fileContent = str_replace('&#126', '~', $_POST['file-content']);
        $fileType = $_POST['file-type'];
        $browser['all'] = (($_POST['browser-all'] == "on") ? 1 : 0);
        $browser['msie6'] = (($_POST['browser-ie6'] == "on") ? 1 : 0);
        $browser['msie7'] = (($_POST['browser-ie7'] == "on") ? 1 : 0);
        $browser['msie8'] = (($_POST['browser-ie8'] == "on") ? 1 : 0);
        $browser['firefox'] = (($_POST['browser-firefox'] == "on") ? 1 : 0);
        $browser['safari'] = (($_POST['browser-safari'] == "on") ? 1 : 0);
        $browser['opera'] = (($_POST['browser-opera'] == "on") ? 1 : 0);
        
        $newFile = false;
        
        if(isset($fileId)) {
          $dbObject->execute("UPDATE `page_file` SET `name` = \"".$fileName."\", `content` = \"".$fileContent."\", `for_all` = ".$browser['all'].", `for_msie6` = ".$browser['msie6'].", `for_msie7` = ".$browser['msie7'].", `for_msie8` = ".$browser['msie8'].", `for_firefox` = ".$browser['firefox'].", `for_opera` = ".$browser['opera'].", `for_safari` = ".$browser['safari'].", `type` = \"".$fileType."\" WHERE `id` = ".$fileId.";");
        } else {
          $dbObject->execute("INSERT INTO `page_file`(`name`, `content`, `for_all`, `for_msie6`, `for_msie7`, `for_msie8`, `for_firefox`, `for_opera`, `for_safari`, `type`, `wp`) VALUES (\"".$fileName."\", \"".$fileContent."\", ".$browser['all'].", ".$browser['msie6'].", ".$browser['msie7'].", ".$browser['msie8'].", ".$browser['firefox'].", ".$browser['opera'].", ".$browser['safari'].", ".$fileType.", ".$projectId.");");
          $newFile = true;
        }
        if($_POST['save'] == "Save") {
          $_POST['edit-file'] = "Edit";
          if($newFile) {
	          $fid = $dbObject->fetchAll("SELECT MAX(`id`) AS `id` FROM `page_file`;");
	          $_POST['file-id'] = $fid[0]['id'];
	        }
        }
      }
       
      if($_POST['edit-file'] == "Edit") {
        $fileId = $_POST['file-id'];
        
        $file = $dbObject->fetchAll("SELECT `name`, `content`, `for_all`, `for_msie6`, `for_msie7`, `for_msie8`, `for_firefox`, `for_opera`, `for_safari`, `type` FROM `page_file` WHERE `id` = ".$fileId.";");
        if(count($file) == 1) {      
          $fileTypesOpt = "";
          foreach($filesEx as $key => $ext) {
            $fileTypesOpt .= '<option '.(($key == $file[0]['type']) ? 'selected="selected"' : '').'value="'.$key.'">'.$ext.'</option>';
          }
          $browsers['All'] = $file[0]['for_all'];
          $browsers['IE6'] = $file[0]['for_msie6'];
          $browsers['IE7'] = $file[0]['for_msie7'];
          $browsers['IE8'] = $file[0]['for_msie8'];
          $browsers['Firefox'] = $file[0]['for_firefox'];
          $browsers['Opera'] = $file[0]['for_opera'];
          $browsers['Safari'] = $file[0]['for_safari'];
          
          $return .= self::getFileUpdateForm($fileId, $file[0]['name'], $file[0]['content'], $browsers, $fileTypesOpt);
        } else {
          $return .= parent::getFrame('Error message', '<h4 class="error">No file selected!</h4>', '', true);
        }
      }
      
      if($_POST['delete-file'] == "Delete") {
        $fileId = $_POST['file-id'];
        $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = ".$fileId.";");
        $dbObject->execute("DELETE FROM `page_file` WHERE `id` = ".$fileId.";");
      }
      $n = 1;
      $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` WHERE `wp` = ".$projectId." ORDER BY `id`;");
      if(count($files) != 0) {
	      $returnTmp .= ''
  	    .'<table class="page-file-list">'
    	  	.'<tr class="file-tr">'
      			.'<th class="">Id</th>'
      			.'<th class="">Name</th>'
      			.'<th class="">Content</th>'
	      		.'<th class="">Type</th>'
  	    		.'<th class="">Edit</th>'
					.'</tr>';     
      	foreach($files as $file) {
        	$returnTmp .= '' 
					.'<tr class="file-tr '.(($n % 2) ? 'idle' : 'even').'">'
        		.'<td class="file-id">'
							.$file['id']
        		.'</td>'
            .'<td class="file-name">'
            	.$file['name']
            .'</td>'
            .'<td class="file-content">'
            	.'<div class="file-content-in"><div class="foo">'.substr($file['content'], 0, 130).'</div></div>'
            .'</td>'
            .'<td class="file-type">'
            	.$filesEx[$file['type']]
            .'</td>'
            .'<td>'
            	.(($editable) ? ''
              .'<form name="process-file1" method="post" action="">'
              	.'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                .'<input type="hidden" name="edit-file" value="Edit" />'
                .'<input type="image" src="'.WEB_ROOT.'images/page_edi.png" name="edit-file" value="Edit" title="Edit file" /> '
              .'</form>'
              .'<form name="process-file2" method="post" action="">'
              	.'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                .'<input type="hidden" name="delete-file" value="Delete" />'
                .'<input class="confirm" type="image" src="'.WEB_ROOT.'images/page_del.png" name="delete-file" value="Delete" title="Delete file" />'
              .'</form>'
              : '')
            .'</td>'
          .'</tr>';
        	$n ++;
      	}
      	$returnTmp .= '</table>';
      	$return .= parent::getFrame('Text files', $returnTmp, '');
      } else {
				$return .= parent::getFrame('Text files', '<h4 class="error">No files to edit!</h4>', '');
			}
      
      $returnTmp = ''
      .'<form name="add-file" method="post" action="">'
      	.'<input type="submit" name="add-file" value="New file" title="Create new file" />'
      .'</form>';
      $return .= parent::getFrame('New Text File', $returnTmp, '');
      return $return;
    }
    
    /**
     *
     *  Generates form for updating text files.
     *
     */                        
    private function getFileUpdateForm($fileId, $fileName, $fileContent, $browsers, $fileTypes) {
    	$htmlBrowsers = '';
    	foreach($browsers as $browser => $value) {
				$htmlBrowsers .= ''
				.'<div class="text-file-browser">'
					.'<label for="browser-'.strtolower($browser).'">'.$browser.'</label> '
					.'<input type="checkbox" name="browser-'.strtolower($browser).'"'.(($value == 1) ? ' checked="checked"' : '').' />'
				.'</div>';
			}
    
      $returnTmp = ''
                .'<form name="edit-file" method="post" action="">'
                  .'<div class="edit-file-name">'
                    .'<div class="text-file-prop">'
                      .'<div class="text-file-name">'
                        .'<label for="file-name">Name:</label> '
                        .'<input type="text" name="file-name" value="'.$fileName.'" /> '
                      .'</div>'
                      .'<div class="text-file-type">'
                        .'<label for="file-type">Type:</label> '
                        .'<select name="file-type"> '
                          .$fileTypes
                        .'</select> '
                      .'</div>'
                      .'<div class="text-file-browsers">'
                      	.$htmlBrowsers
                      .'</div>'
                      .'<div class="clear"></div>'
                    .'</div>'
                    .'<div class="text-file-content">'
                      .'<label for="file-content">Content:</label> '
                      .'<div class="editor-cover">'
                      	.'<div class="textarea-cover">'
                      		.'<textarea name="file-content" class="editor-textarea" rows="15" wrap="off">'.str_replace('~', '&#126', $fileContent).'</textarea> '
                      	.'</div>'
                      	.'<div class="clear"></div>'
                      .'</div>'
                    .'</div>'
                    .'<div class="text-file-submit">'
                      .(($fileId != -1) ? '<input type="hidden" name="file-id" value="'.$fileId.'" />' : '')
                      .'<input type="submit" name="save" value="Save" title="Save changes" /> '
                      .'<input type="submit" name="save" value="Save and Close" title="Save changes and Close file" /> '
                      .'<input type="submit" name="close" value="Close" title="Close file" /> '
                    .'</div>'
                  .'</div>'
                .'</form>';
      return parent::getFrame('Edit file'.((strlen($fileName) != 0) ? ' :: '.$fileName : ''), $returnTmp, '');
    }
    
    /**
     *
     *	Clears url cache.
     *	C tag.     
     *	
     *	@return		form		      
     *
     */		 		 		 		     
    public function clearUrlCache() {
			global $dbObject;
			$return = $msg = '';
			
			if($_POST['clear-url-cache'] == "Do \'Clear Url Cache\'") {
				$dbObject->execute("TRUNCATE TABLE `urlcache`");
				$msg = '<h4 class="success">Url cache cleared!</h4>';
			}
			
			$returnForm = ''
			.((strlen($msg) > 0) ? $msg : '' )
			.'<div class="clear-url-cache">'
				.'<form name="clear-url-cache" method="post" action="">'
					.'<input type="submit" name="clear-url-cache" value="Do \'Clear Url Cache\'" />'
				.'</form>'
			.'</div>';
			
			return $return.parent::getFrame("Clear Url Cache", $returnForm, "", true);
		}
		
		/**
		 *
		 *	Setups keywords of whole web app.
		 *	C tag.
		 *	
		 *	return form		 		 		 		 		 
		 *
		 */		 		 		 		
		public function updateKeywords() {
			$return = $msg = '';
			
			if($_POST['save-keywords'] == "Save") {
				file_put_contents("keywords.txt", $_POST['keywords']);
				$msg = '<h4 class="success">Keywords saved!</h4>';
			}
			
			$keywords = file_get_contents("keywords.txt");
			$returnForm = ''
			.((strlen($msg) > 0) ? $msg : '' )
			.'<div class="update-keywords">'
				.'<form class="update-keywords" name="update-keywords" method="post" action="">'
					.'<label for="keywords">Set keywords of whole web app:</label> '
					.'<input class="keywords-input" type="text" name="keywords" value="'.$keywords.'" /> '
					.'<input type="submit" name="save-keywords" value="Save" >'
				.'</form>'
			.'</div>';
			
			return parent::getFrame("Manage keywords", $returnForm, "", true);
		}
		
		/**
		 *
		 *	Show languages.
		 *	C tag.		 
		 *	
		 *	@param		editable		if true, user can delete lang versions
		 *	@return		form		 		 		 
		 *
		 */		 		 		 		
		public function showLanguages($editable) {
			global $dbObject;
			$return = $returnForm = $returnNewForm = $msg = $msgNew = '';
			
			if($editable && $_POST['delete-language'] == 'Delete language') {
				$langId = $_POST['language-id'];
				
				if(count($dbObject->fetchAll('SELECT `page_id` FROM `info` WHERE `language_id` = '.$langId.';')) == 0) {
					$dbObject->execute("DELETE FROM `language` WHERE `id` = ".$langId.";");
					$msg = '<h4 class="success">Language deleted!</h4>';
				}
			}
			
			if($editable && $_POST['add-new-language'] == "Add") {
				$name = $_POST['langauge-name'];
				
				if(count($dbObject->fetchAll('SELECT `id` FROM `language` WHERE `language` = "'.$name.'";')) == 0) {
					$max = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `language`;');
					$max = $max[0]['id'] + 1;
					$dbObject->execute('INSERT INTO `language` (`id`, `language`) VALUES ('.$max.', "'.$name.'");');
					$msgNew = '<h4 class="success">Language added!</h4>';
				} else {
					$msgNew = '<h4 class="error">Language with this name already exists!</h4>';
				}
			}
			
			$langs = $dbObject->fetchAll('SELECT `id`, `language` FROM `language` ORDER BY `id`;');
			$returnForm = ''
			.((strlen($msg) > 0) ? $msg : '' )
			.'<table class="languages-edit">'
				.'<tr>'
					.'<th>Id</th>'
					.'<th>Name</th>'
					.'<th>Edit</th>'
				.'</tr>';
			$i = 1;
			foreach($langs as $lang) {
				$returnForm .= ''
				.'<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">'
					.'<td class="langs-id">'.$lang['id'].'</td>'
					.'<td class="langs-language">'.$lang['language'].'</td>'
					.(($editable && (count($dbObject->fetchAll('SELECT `page_id` FROM `info` WHERE `language_id` = '.$lang['id'].';')) == 0)) ? ''
					.'<td class="langs-delete">'
						.'<form name="delete-lang" method="post" action="">'
							.'<input type="hidden" name="language-id" value="'.$lang['id'].'" />'
							.'<input type="hidden" name="delete-language" value="Delete language" />'
							.'<input class="confirm" type="image" src="'.WEB_ROOT.'images/page_del.png" name="delete-language" value="Delete language" title="Delete language" />'
						.'</form>'
					.'</td>'
					: '<td></td>' )
				.'</tr>';
				$i ++;
			}
			$returnForm .= '</table>';
			
			if($editable) {
				$returnNewForm = ''
				.((strlen($msgNew) > 0) ? $msgNew : '' )
				.'<div class="add-new-language">'
					.'<form name="add-new-language" method="post" action="">'
						.'<label for="langauge-name">Type new language name:</label> '
						.'<input type="text" name="langauge-name" /> '
						.'<input type="submit" name="add-new-language" value="Add" />'
					.'</form>'
				.'</div>';
			}
			
			return $return.parent::getFrame('Languages', $returnForm, '', true).parent::getFrame('Add Language', $returnNewForm, '', true);
		}
		
		/**
		 *
		 *	Show list of templates
		 *	C tag
		 *	
		 *	@param	detailPageId				page id with edit template form
		 *	@param	useFrames						use frames in output
		 *	@param	showError						show errors in output
		 *	@return	list of templates
		 *
		 */		 		 		 		 		
		public function showTemplates($detailPageId = false, $useFrames = false, $showError = false) {
			global $webObject;
			global $loginObject;
			global $dbObject;
			$return = '';
			$actionUrl = '';
			if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
			
			if($_POST['template-delete'] == "Delete") {
				$templateId = $_POST['template-id'];
				
				$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
				if(count($rights) > 0 && $templateId > 0) {
					$dbObject->execute('DELETE FROM `template_right` WHERE `tid` = '.$templateId.';');
					$dbObject->execute('DELETE FROM `template` WHERE `id` = '.$templateId.';');
					if($showError != 'false') {
						$return .= '<h4 class="success">Template deleted!</h4>';
					}
				} else {
					if($showError != 'false') {
						$return .= '<h4 class="error">Permission Denied!</h4>';
					}
				}
			}
			
			// Vyber templatu do kterych smim zapisovat
			$templates = $dbObject->fetchAll('SELECT `template`.`id`, `template`.`content` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `template`.`id`;');
			
			if(count($templates) > 0) {
				$return .= ''
				.'<table class="template-list">'
					.'<tr>'
						.'<th class="template-id">Id:</th>'
						.'<th class="template-content">Content:</th>'
						.'<th class="template-edit">Edit:</th>'
					.'</tr>';
				$i = 1;
				foreach($templates as $template) {
					//$template['content'] = str_replace('&amp;web:page', '&web:page', $template['content']);
	      	$template['content'] = str_replace('~', '&#126', $template['content']);
        	$template['content'] = str_replace("&", "&amp;", $template['content']);
      	  $template['content'] = str_replace(">", "&gt;", $template['content']);
    	    $template['content'] = str_replace("<", "&lt;", $template['content']);
					$return .= ''
					.'<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">'
						.'<td class="template-id">'.$template['id'].'</td>'
						.'<td class="template-content">'
							.'<div class="file-content-in"><div class="foo">'.substr($template['content'], 0, 130).'</div></div>'
						.'</td>'
						.'<td class="template-edit">'
							.'<form name="template-edit1" method="post" action="'.$actionUrl.'">'
								.'<input type="hidden" name="template-id" value="'.$template['id'].'" />'
								.'<input type="hidden" name="template-edit" value="Edit" />'
								.'<input type="image" src="~/images/page_edi.png" name="template-edit" value="Edit" title="Edit template" />'
							.'</form> '
							.'<form name="template-edit2" method="post" action="">'
								.'<input type="hidden" name="template-id" value="'.$template['id'].'" />'
								.'<input type="hidden" name="template-delete" value="Delete" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="template-delete" value="Delete" title="Delete template" />'
							.'</form>'
						.'</td>'
					.'</tr>';
					$i ++;
				}
				$return .= ''
				.'</table>';
			} else {
				if($showError != 'false') {
					$return .= '<h4 class="error">No templates to show!</h4>';
				}
			}
			
			$newTemplate = $dbObject->fetchAll('SELECT `value` FROM `template_right` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = 0 AND `template_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
			if(count($newTemplate) > 0) {
				$return .= ''
				.'<hr />'
				.'<form name="template-new" method="post" action="'.$actionUrl.'">'
					.'<input type="submit" name="template-new" value="New Template" />'
				.'</form>';
			}
			
			if($useFrames != "false") {
				return parent::getFrame('Temlates list', $return, '');
			} else {
				return $return;
			}
		}
		
		/**
		 *
		 *	Shows edit template form
		 *	
		 *	@param	submitPageId		page id submit form to
		 *	@param	useFrames				use frames in output
		 *	@param	showError				show error in output		 		 		 		 
		 *	@return	html form form editing template
		 *
		 */		 		 		 		 		
		public function showEditTemplateForm($submitPageId = false, $useFrames = false, $showError = false) {
			global $webObject;
			global $loginObject;
			global $dbObject;
			$return = '';
			$actionUrl = '';
			if($submitPageId != false) {
				$actionUrl = $webObject->composeUrl($submitPageId);
			}
			
			if($_POST['template-submit'] == "Save") {
				$templateId = $_POST['template-id'];
				$templateContent = $_POST['template-content'];
				$templateR = $_POST['template-right-edit-groups-r'];
				$templateW = $_POST['template-right-edit-groups-w'];
				$templateD = $_POST['template-right-edit-groups-d'];
				
				// test na prava
				$tempId = (($_POST['template-id'] != '') ? $_POST['template-id'] : 0);
				$rights = $dbObject->fetchAll('SELECT `value` FROM `template_right` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = '.$tempId.' AND `template_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
				if(count($rights) > 0) {
	        $template = $dbObject->fetchAll('SELECT `id` FROM `template` WHERE `id` = '.$templateId.';');
  	      if(count($template) == 0) {
						$dbObject->execute('INSERT INTO `template`(`content`) VALUES ("'.$templateContent.'");');
						$last = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `template`;');
						$templateId = $last[0]['id'];
						$_POST['template-id'] = $templateId;
					} else {
						$dbObject->execute('UPDATE `template` SET `content` = "'.$templateContent.'" WHERE `id` = '.$templateId.';');
					}
			
     	    if(count($templateR) != 0) {		
						$dbR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `template_right`.`tid` = ".$templateId." AND `type` = ".WEB_R_READ.";");
		        foreach($dbR as $right) {
			        if(!in_array($right['gid'], $templateR)) {
  							$dbObject->execute("DELETE FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_READ.";");
      	    	}
        		}
	        	foreach($templateR as $right) {
  	    	 		$row = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
    		 	    if(count($row) == 0) {
   		  	    	$dbObject->execute("INSERT INTO `template_right`(`tid`, `gid`, `type`) VALUES (".$templateId.", ".$right.", ".WEB_R_READ.");");
 		      	  }
		        }
	      	}
     	    if(count($templateW) != 0) {
  	      	$dbR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `template_right`.`tid` = ".$templateId." AND `type` = ".WEB_R_WRITE.";");
    	  	  foreach($dbR as $right) {
      		 	  if(!in_array($right['gid'], $templateW)) {
    	 	  	    $dbObject->execute("DELETE FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_WRITE.";");
  	 	      	}
		 	      }
  	      	foreach($templateW as $right) {
    	  	   	$row = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
      		    if(count($row) == 0) {
    	    	    $dbObject->execute("INSERT INTO `template_right`(`tid`, `gid`, `type`) VALUES (".$templateId.", ".$right.", ".WEB_R_WRITE.");");
  	     	  	}
		     	  }
	     	  }
     	    if(count($templateD) != 0) {
	  	 	    $dbR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `template_right`.`tid` = ".$templateId." AND `type` = ".WEB_R_DELETE.";");
 		  	    foreach($dbR as $right) {
    	  	    if(!in_array($right['gid'], $templateD)) {
      	  	   	$dbObject->execute("DELETE FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_DELETE.";");
        	 		}
	        	}
	  	      foreach($templateD as $right) {
  	  	   	  $row = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_DELETE." AND `gid` = ".$right.";");
    	 		    if(count($row) == 0) {
   	  	  	    $dbObject->execute("INSERT INTO `template_right`(`tid`, `gid`, `type`) VALUES (".$templateId.", ".$right.", ".WEB_R_DELETE.")");
 	      	  	}
	        	}
	        }
	        
	        if($showError != 'false') {
						$return .= '<h4 class="success">Template added!</h4>';
					}
				} else {
					if($showError != 'false') {
						$return .= '<h4 class="error">Permission Denied!</h4>';
					}
				}
			}
			
			// Pokud je v postu template-id, vyber template, testuj prava, pokud, testuj prava pro template-id 0
			$templateId = ((array_key_exists('template-id', $_POST)) ? $_POST['template-id'] : 0);
			$template = $dbObject->fetchAll('SELECT `content` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
			if(count($template) > 0 || $templateId == 0) {
				$show = array('read' => true, 'write' => true, 'delete' => false);
				$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_READ.";");
        $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_WRITE.";");
        $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = ".$templateId." AND `type` = ".WEB_R_DELETE.";");
        $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
        $groupSelectR = '<select id="template-right-edit-groups-r" name="template-right-edit-groups-r[]" multiple="multiple" size="5">';
        $groupSelectW = '<select id="template-right-edit-groups-w" name="template-right-edit-groups-w[]" multiple="multiple" size="5">';
        $groupSelectD = '<select id="template-right-edit-groups-d" name="template-right-edit-groups-d[]" multiple="multiple" size="5">';
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
			
				$template = $template[0];
				$template['content'] = str_replace('~', '&#126', $template['content']);
        $template['content'] = str_replace("&", "&amp;", $template['content']);
      	$template['content'] = str_replace(">", "&gt;", $template['content']);
    	  $template['content'] = str_replace("<", "&lt;", $template['content']);
				$return .= ''
				.'<form name="template-edit-detail" method="post" action="'.$actionUrl.'">'
					.'<div class="template-rights">'
						.(($show['read']) ? ''
						.'<div class="template-right-r">'
							.'<label for="template-right-edit-groups-r">Read:</label>'
							.$groupSelectR
						.'</div>'
						: '')
						.(($show['write']) ? ''
						.'<div class="template-right-w">'
							.'<label for="template-right-edit-groups-w">Write:</label>'
							.$groupSelectW
						.'</div>'
						: '')
						.(($show['delete']) ? ''
						.'<div class="template-right-d">'
							.'<label for="template-right-edit-groups-d">Delete:</label>'
							.$groupSelectD
						.'</div>'
						: '')
						.'<div class="clear"></div>'
					.'</div>'
					.'<div class="clear"></div>'
					.'<div class="template-edit-content">'
						.'<label for="template-edit-detail-content">Content:</label>'
						.'<div class="editor-cover">'
							.'<div class="textarea-cover">'
								.'<textarea id="template-edit-detail-content" class="editor-textarea" name="template-content" rows="15" wrap="off">'.$template['content'].'</textarea>'
							.'</div>'
						.'</div>'
					.'</div>'
					.'<div class="template-submit">'
						.'<input type="hidden" name="template-id" value="'.$templateId.'" />'
						.'<input type="submit" name="template-submit" value="Save" />'
					.'</div>'
				.'</form>';
			} else {
				if($showError != 'false') {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
			}
		
			if($useFrames != "false") {
				return parent::getFrame('Temlate edit', $return, '');
			} else {
				return $return;
			}
		}
		
  }

?>
