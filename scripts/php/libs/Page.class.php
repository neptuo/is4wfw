<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");

  require_once("scripts/php/classes/ResourceBundle.class.php");
  
  /**
   * 
   *  Class updating web pages.     
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-12-05
   * 
   */  
  class Page extends BaseTagLib {
  
  	private $BundleName = 'page';
  	
  	private $BundleLang = 'en';
  
    public function __construct() {
      parent::setTagLibXml("xml/Page.xml");
      
      if($webObject->LanguageName != '') {
				$rb = new ResourceBundle();
				if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
					$BundleLang = $webObject->LanguageName;
				}
			}
    }
    
    public function showEditPage() {
			global $dbObject;
      global $loginObject;
      global $webObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
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
					// dont care.
				}
			}
      
      if($_POST['edit-save'] == $rb->get('page.action.save') || $_POST['edit-save'] == $rb->get('page.action.saveandclose')) {
        $pageId = $_POST['page-id'];
        $parentId = $_POST['parent-id'];
        $languageId = $_POST['language'];
        $name = $_POST['edit-name'];
        $escapeChars = array("ě" => "e", "é" => "e", "ř" => "r", "ť" => "t", "ý" => "y", "ú" => "u", "ů" => "u", "í" => "i", "ó" => "o", "á" => "a", "š" => "s", "ď" => "d", "ž" => "z", "č" => "c", "ň" => "n", "." => "-");
        $href = strtr($_POST['edit-href'], $escapeChars);
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
        // Surely???
        $head = str_replace('"', '\"', $head);
        $content = str_replace('"', '\"', $content);
        $tlStart = str_replace('"', '\"', $tlStart);
        $tlEnd = str_replace('"', '\"', $tlEnd);
        // --
        $type = $_POST['type'];
        $keywords = $_POST['edit-keywords'];
        $clearUrlCache = $_POST['edit-clearurlcache'];
        $cacheTime = $_POST['edit-cachetime'];
        $errors = array();
        
        $forSaveNewPageId = 0;
        
        $pageRightR = $_POST['right-edit-groups-r'];
        $pageRightW = $_POST['right-edit-groups-w'];
        $pageRightD = $_POST['right-edit-groups-d'];
        
        if(strlen($name) < 2) {
          $errors[] = $rb->get('page.error.nametooshort');
        }
        
	      if($type != "page-edit") {
	        $tmpPages = $dbObject->fetchAll("SELECT `id` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` WHERE `info`.`href` = \"".$href."\" AND `page`.`parent_id` = ".$parentId." AND `info`.`language_id` = ".$languageId." AND `page`.`wp` = ".$projectId.";");
	        if(count($tmpPages) != 0) {
	          $errors[] = $rb->get('page.error.urlused');
	        }
	      }
        if(count($errors) == 0) {
          if($type == "page-edit") {
            $dbObject->execute("UPDATE `content` SET `tag_lib_start` = \"".$tlStart."\", `tag_lib_end` = \"".$tlEnd."\", `head` = \"".$head."\", `content` = \"".$content."\" WHERE `page_id` = ".$pageId." AND `language_id` = ".$languageId.";", true);
            $dbObject->execute("UPDATE `info` SET `name` = \"".$name."\", `href` = \"".$href."\", `in_title` = \"".$inTitle."\", `in_menu` = ".$menu.", `is_visible` = ".$visible.", `keywords` = \"".$keywords."\", `timestamp` = ".time().", `cachetime` = ".$cacheTime." WHERE `page_id` = ".$pageId." AND `language_id` = ".$languageId.";");
      
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
            $forSaveNewPageId = $pageId;
            $_POST['page-id'] = $pageId;
            $languageId = $_POST['language'];
            
            $dbObject->execute("INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES(".$pageId.", ".$parentId.", ".$projectId.");");
            $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(".$pageId.", \"".$tlStart."\", \"".$tlEnd."\", \"".$head."\", \"".$content."\", ".$languageId.");");
            $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES(".$pageId.", ".$languageId.", \"".$name."\", \"".$href."\", ".$inTitle.", ".$menu.", ".$pageId.", ".$visible.", \"".$keywords."\", ".time().", ".$cacheTime.");");
            
     	    	if(count($pageRightR) != 0) {
	            foreach($pageRightR as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_READ.")");
    	        }
    	      } else {
							$rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = '.$parentId.' AND `type` = '.WEB_R_READ.';');
							foreach($rights as $right) {
								$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$pageId.', '.$right['gid'].', '.WEB_R_READ.');');
							}
						}
     	    	if(count($pageRightW) != 0) {
	            foreach($pageRightW as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_WRITE.")");
    	        }
    	      } else {
							$rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = '.$parentId.' AND `type` = '.WEB_R_WRITE.';');
							foreach($rights as $right) {
								$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$pageId.', '.$right['gid'].', '.WEB_R_WRITE.');');
							}
						}
     	    	if(count($pageRightD) != 0) {
	            foreach($pageRightD as $right) {
  	            $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (".$pageId.", ".$right.", ".WEB_R_DELETE.")");
    	        }
    	      } else {
							$rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = '.$parentId.' AND `type` = '.WEB_R_DELETE.';');
							foreach($rights as $right) {
								$dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES ('.$pageId.', '.$right['gid'].', '.WEB_R_DELETE.');');
							}
						}
            
            $return .= '<h4 class="success">'.$rb->get('page.success.added').'</h4>';
          } else if($type == "page-add-lang-ver") {
            
            $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(".$pageId.", \"".$tlStart."\", \"".$tlEnd."\", \"".$head."\", \"".$content."\", ".$languageId.");");
            $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES(".$pageId.", ".$languageId.", \"".$name."\", \"".$href."\", ".$inTitle.", ".$menu.", ".$pageId.", ".$visible.", \"".$keywords."\", ".time().", ".$cacheTime.");");
            
           $return .= '<h4 class="success">'.$rb->get('page.success.langadded').'</h4>';
          } else if($type == "page-add-sub") {
            $sql_return = $dbObject->fetchAll("SELECT MAX(`id`) AS `id` FROM `page`");
            
            $pageId = $sql_return[0]['id'] + 1;
            $_POST['page-id'] = $pageId;
            
            $dbObject->execute("INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES(".$pageId.", ".$parentId.", ".$projectId.");");
            $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(".$pageId.", \"".$tlStart."\", \"".$tlEnd."\", \"".$head."\", \"".$content."\", ".$languageId.");");
            $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES(".$pageId.", ".$languageId.", \"".$name."\", \"".$href."\", ".$inTitle.", ".$menu.", ".$pageId.", ".$visible.", \"".$keywords."\", ".time().", ".$cacheTime.");");
      
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
            
            $return .= '<h4 class="success">'.$rb->get('page.success.added').'</h4>';
          }
          
          if($clearUrlCache) {
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."-%\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"".$pageId."-%\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` LIKE \"%-".$pageId."\" AND `language_id` = ".$languageId.";");
						$dbObject->execute("DELETE FROM `urlcache` WHERE `page-ids` = \"".$pageId."\" AND `language_id` = ".$languageId.";");
					}
        
          if($_POST['edit-save'] == $rb->get('page.action.save')) {
          	if($_POST['type'] == 'add-new-page') {
          		$_POST['page-id'] = $forSaveNewPageId;
          	}
            $_POST['page-edit'] = $rb->get('page.action.edit');
            $_POST['page-lang-id'] = $_POST['language'];
          }
        } else {
          //$errorList = '<ul class="error-list">';
          foreach($errors as $error) {
            $errorList .= '<h4 class="error">'.$error.'</h4>';
          }
          //$errorList .= '</ul>';
          $return .= parent::getFrame($rb->get('page.error.listlabel'), $errorList, "", true);
          
          $errorOccurs = "true";
          
          if($_POST['type'] == 'add-new-page') {
            $_POST['add-new-page'] = $rb->get('page.action.addpage');
          } else if($_POST['type'] == 'page-add-sub') {
            $_POST['page-add-sub'] = $rb->get('page.action.addsubpage');
          } else if($_POST['type'] == 'page-add-lang-ver') {
            $_POST['page-add-lang-ver'] = $rb->get('page.action.addlang');;
          } else if($_POST['type'] == 'page-edit') {
            $_POST['page-edit'] = "Edit";
          }
        }
      }
      
      if($_POST['page-edit'] == $rb->get('page.action.edit') || $_POST['add-new-page'] == $rb->get('page.action.addpage') || $_POST['page-add-sub'] == $rb->get('page.action.addsubpage') || $_POST['page-add-lang-ver'] == $rb->get('page.action.addlang') || $errorOccurs == "true") {
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
        
        if($ok) {
          $right_pid = $pageId;
          if($_POST['page-edit'] == $rb->get('page.action.edit')) {
            $type = "page-edit";
          } else if($_POST['add-new-page'] == $rb->get('page.action.addpage')) {
            $type = "add-new-page";
            $right_pid = $parentId;
            $frameTitle = $rb->get('page.title.addpage');
          } else if($_POST['page-add-sub'] == $rb->get('page.action.addsubpage')) {
            $type = "page-add-sub";
            $frameTitle = $rb->get('page.title.addsubpage');
          } else if($_POST['page-add-lang-ver'] == $rb->get('page.action.addlang')) {
            $type = "page-add-lang-ver";
            $parentId = $pageId;
            $usedLangs = $dbObject->fetchAll("SELECT `language_id` FROM `info` WHERE `page_id` = ".$pageId.";");
            $frameTitle = $rb->get('page.title.addlang');
          } else {
            $type = "undefined";
          }
          if($_POST['page-edit'] == $rb->get('page.action.edit')) {
            $sql_return = $dbObject->fetchAll("SELECT `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content`, `info`.`name`, `info`.`href`, `info`.`in_title`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime` FROM `content` LEFT JOIN `info` ON `content`.`page_id` = `info`.`page_id` AND `info`.`language_id` = `content`.`language_id` WHERE `info`.`page_id` = ".$pageId." AND `info`.`language_id` = ".$langId.";");
            $frameTitle = $rb->get('page.action.editation').' :: '.$sql_return[0]['name'].' ( '.$pageId.' )';
          } else {
            $sql_return = array();
            $sql_return[0]['in_title'] = 1;
            $sql_return[0]['is_visible'] = 1;
            $sql_return[0]['cachetime'] = -1;
          }
          
          if($errorOccurs == "true") {
  	    	  $head = str_replace('\"', '"', $head);
    	  	  $content = str_replace('\"', '"', $content);
  	      	$tlStart = str_replace('\"', '"', $tlStart);
		        $tlEnd = str_replace('\"', '"', $tlEnd);
		        
            $sql_return[0]['name'] = $name;
            $sql_return[0]['href'] = $href;
            $sql_return[0]['in_title'] = $inTitle;
            $sql_return[0]['in_menu'] = $inMenu;
            $sql_return[0]['is_visible'] = $isVisible;
            $sql_return[0]['head'] = $head;
            $sql_return[0]['content'] = $content;
            $sql_return[0]['tag_lib_start'] = $tlStart;
            $sql_return[0]['tag_lib_end'] = $tlEnd;
            $sql_return[0]['cachetime'] = $cacheTime;
            $langId = $languageId;
          }
          
          if($type == $rb->get('page.action.addpage')) {
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
          $groupSelectR = '<select id="right-edit-groups-r" name="right-edit-groups-r[]" multiple="multiple" size="5">';
          $groupSelectW = '<select id="right-edit-groups-w" name="right-edit-groups-w[]" multiple="multiple" size="5">';
          $groupSelectD = '<select id="right-edit-groups-d" name="right-edit-groups-d[]" multiple="multiple" size="5">';
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
                      .'<form name="page-edit-detail" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                      .'<div class="edit edit-page-info">'
                        .'<div class="edit edit-prop">'
                          .'<div class="edit edit-name">'
                            .'<label for="edit-name">'.$rb->get('page.field.namelabel').':</label> '
                            .'<input type="text" name="edit-name" id="edit-name" value="'.$sql_return[0]['name'].'" />'
                          .'</div>'
                          .'<div class="edit edit-href">'
                            .'<label for="edit-href">'.$rb->get('page.field.urllabel').':</label> '
                            .'<input type="text" name="edit-href" id="edit-href" value="'.$sql_return[0]['href'].'" />'
                          .'</div>'
                          .'<div class="edit edit-in-title">'
                            .'<label for="edit-in-title">'.$rb->get('page.field.intitlelabel').':</label> '
                            .'<input type="checkbox" name="edit-in-title" id="edit-in-title"'.(($sql_return[0]['in_title'] == 1) ? 'checked="checked"' : '').' />'
                          .'</div>'
                          .'<div class="edit edit-menu">'
                            .'<label for="edit-menu">'.$rb->get('page.field.inmenulabel').':</label> '
                            .'<input type="checkbox" name="edit-menu" id="edit-menu"'.(($sql_return[0]['in_menu'] == 1) ? 'checked="checked"' : '').' />'
                          .'</div>'
                          .'<div class="edit edit-visible">'
                            .'<label for="edit-visible">'.$rb->get('page.field.isvisiblelabel').':</label> '
                            .'<input type="checkbox" name="edit-visible" id="edit-visible"'.(($sql_return[0]['is_visible'] == 1) ? 'checked="checked"' : '').' />'
                          .'</div>'
                          .'<div class="edit edit-clear-cache">'
                            .'<label for="edit-clearurlcache">'.$rb->get('page.field.clearcachelabel').':</label> '
                            .'<input type="checkbox" name="edit-clearurlcache" id="edit-clearurlcache" />'
                          .'</div>'
                          .'<div class="edit edit-cache-time">'
                            .'<label for="edit-cachetime">'.$rb->get('page.field.cachetimelabel').':</label>'
                            .'<select id="edit-cachetime" name="edit-cachetime">'
                            	.'<option value="-1"'.(($sql_return[0]['cachetime'] == -1) ? 'selected="selected"' : '').'>Don\'t use</option>'
                            	.'<option value="60"'.(($sql_return[0]['cachetime'] == 60) ? 'selected="selected"' : '').'>1 minute</option>'
                            	.'<option value="3600"'.(($sql_return[0]['cachetime'] == 3600) ? 'selected="selected"' : '').'>1 hour</option>'
                            	.'<option value="86400"'.(($sql_return[0]['cachetime'] == 86400) ? 'selected="selected"' : '').'>1 day</option>'
                            	.'<option value="0"'.(($sql_return[0]['cachetime'] == 0) ? 'selected="selected"' : '').'>Unlimited</option>'
                            .'</select>'
                          .'</div>';
            if($type == 'add-new-page' || $type == 'page-add-lang-ver') {
              $returnTmp .= 
                          '<div class="edit edit-language">'
                          .'<label for="select-language">'.$rb->get('page.field.languagelabel').': </label>'
                          .'<select id="select-language" name="language">';
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
            
            include_once('System.class.php');
    
			    	$name = 'Page.editors';
    				$system = new System();
		  	  	$propertyEditors = $system->getPropertyValue($name);
		  	  	$editAreaContentRows = $system->getPropertyValue('Page.editAreaContentRows');
		  	  	$editAreaHeadRows = $system->getPropertyValue('Page.editAreaHeadRows');
		  	  	$editAreaTLStartRows = $system->getPropertyValue('Page.editAreaTLStartRows');
		  	  	$editAreaTLendRows = $system->getPropertyValue('Page.editAreaTLEndRows');
		  	  	
            $returnTmp .= ''
                        .'</div>'
                        .'<div class="edit edit-rights">'
                        	.(($show['read']) ? ''
                          .'<div class="edit edit-right-read">'
                            .'<label for="right-edit-groups-r">'.$rb->get('page.field.rreadlabel').':</label>'
                            .$groupSelectR
                          .'</div>'
                          : '')
                          .(($show['write']) ? ''
                          .'<div class="edit edit-right-write">'
                            .'<label for="right-edit-groups-w">'.$rb->get('page.field.rwritelabel').':</label>'
                            .$groupSelectW
                          .'</div>'
                          : '')
                          .(($show['delete']) ? ''
                          .'<div class="edit edit-right-delete">'
                            .'<label for="right-edit-groups-d">'.$rb->get('page.field.rdeletelabel').':</label>'
                            .$groupSelectD
                          .'</div>'
                          : '')
                          .'<div class="clear"></div>'
                        .'</div>'
                        .'<div class="clear"></div>'
                        .'<div class="edit edit-keywords">'
                          .'<label for="edit-keywords">'.$rb->get('page.field.keywordslabel').':</label> '
                          .'<input id="edit-keywords" type="text" name="edit-keywords" value="'.$sql_return[0]['keywords'].'" />'
                        .'</div>'
                        .'<div class="clear"></div>'
                      .'</div>'
                      .'<div class="clear"></div>';
                      
            if($propertyEditors == 'edit_area') {
							$returnTmp .= ''
							.'<div id="editors" class="editors edit-area-editors">'
							  .'<div id="editors-tab" class="editors-tab"></div>'
								.'<div id="cover-page-edit-tag-lib-start">'
									.'<label for="page-edit-tag-lib-start">'.$rb->get('page.field.tlstartlabel').':</label>'
								  .'<textarea id="page-edit-tag-lib-start" class="edit-area html" name="edit-tl-start" rows="'.($editAreaTLStartRows > 0 ? $editAreaTLStartRows : 20).'">'.str_replace('~', '&#126', $sql_return[0]['tag_lib_start']).'</textarea>'
								.'</div>'
								.'<div id="cover-page-edit-tag-lib-end">'
									.'<label for="page-edit-tag-lib-end">'.$rb->get('page.field.tlendlabel').':</label>'
									.'<textarea id="page-edit-tag-lib-end" class="edit-area html" name="edit-tl-end" rows="'.($editAreaTLEndRows > 0 ? $editAreaTLEndRows : 20).'">'.str_replace('~', '&#126', $sql_return[0]['tag_lib_end']).'</textarea>'
								.'</div>'
								.'<div id="cover-page-edit-head">'
									.'<label for="page-edit-head">'.$rb->get('page.field.headlabel').':</label>'
									.'<textarea id="page-edit-head" class="edit-area html" name="edit-head" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 20).'">'.str_replace('~', '&#126', $sql_return[0]['head']).'</textarea>'
								.'</div>'
								.'<div id="cover-page-edit-content">'
									.'<label for="page-edit-content">'.$rb->get('page.field.contentlabel').':</label>'
									.'<textarea id="page-edit-content" class="edit-area html" name="edit-content" rows="'.($editAreaContentRows > 0 ? $editAreaContentRows : 20).'">'.str_replace('~', '&#126', $sql_return[0]['content']).'</textarea>'
								.'</div>'
							.'</div>';
						} else {  
	            $returnTmp .= ''
                      .'<div class="edit edit-tag-lib">'
                          .'<div class="edit edit-tl-start">'
                            .'<label for="edit-tl-start">'.$rb->get('page.field.tlstartlabel').':</label>'
                            .'<div class="editor-cover">'
                            	.'<div class="textarea-cover">'
                            		.'<textarea name="edit-tl-start" class="editor-textarea editor-closed" wrap="off" rows="4">'.str_replace('~', '&#126', $sql_return[0]['tag_lib_start']).'</textarea>'
                            	.'</div>'
                            	.'<div class="clear"></div>'
                            .'</div>'
                          .'</div>'
                          .'<div class="edit edit-tl-end">'
                            .'<label for="edit-tl-end">'.$rb->get('page.field.tlendlabel').':</label>'
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
                            .'<label for="edit-head">'.$rb->get('page.field.headlabel').':</label>'
                            .'<div class="editor-cover">'
                            	.'<div class="textarea-cover">'
                            		.'<textarea name="edit-head" class="editor-textarea editor-closed" wrap="off" rows="4">'.str_replace('~', '&#126', $sql_return[0]['head']).'</textarea>'
                            	.'</div>'
                            	.'<div class="clear"></div>'
                            .'</div>'
                          .'</div>'
                          .'<div class="edit edit-content">'
                            .'<label for="edit-content">'.$rb->get('page.field.contentlabel').':</label>'
                            .'<div class="editor-cover">'
                            	.'<div class="textarea-cover">'
                            		.'<textarea name="edit-content" class="editor-textarea editor-tiny" wrap="off" rows="15">'.str_replace('~', '&#126', $sql_return[0]['content']).'</textarea>'
                            	.'</div>'
                            	.'<div class="clear"></div>'
                            .'</div>'
                          .'</div>'
                      .'</div>';
            }
                      
            $returnTmp .= ''
                      .'<div class="edit edit-submit">'
                        .'<input type="hidden" name="parent-id" value="'.$parentId.'" />'
                        .'<input type="hidden" name="page-id" value="'.$pageId.'" />';
            if($type != "add-new-page" && $type != "page-add-lang-ver") {
              $returnTmp .= '<input type="hidden" name="language" value="'.$langId.'" />';
            }
            $returnTmp .= '<input type="hidden" name="type" value="'.$type.'" />'
                        .'<input type="submit" name="edit-save" value="'.$rb->get('page.action.save').'" /> '
                        .'<input type="submit" name="edit-save" value="'.$rb->get('page.action.saveandclose').'" /> '
                        .'<input type="submit" name="edit-close" value="'.$rb->get('page.action.close').'" /> '
                      .'</div>'
                    .' </form>';
          } else {
            $returnTmp .= '<h4 class="warning">'.$rb->get('page.warning.nopage').'</h4>';
          }
          //$returnTmp .= '</div>';
          
          if($langsCount) {
          	$return .= parent::getFrame($frameTitle, $returnTmp, "page-editpage");
          } else {
						//$return .= parent::getFrame($frameTitle, '<h4 class="error">You can\'t add more language versions at this moment! Please, first, add language version to parent page or if this is root page, create more language versions in web application!</h4>', "");
						$return .= parent::getFrame($frameTitle, '<h4 class="error">'.$rb-get('page.error.alllangversionsused').'</h4>', "");
					}
        } else {
          $return .= parent::getFrame($frameTitle, '<h4 class="error">'.$rb->get('page.error.permissiondenied').'</h4>', "", true);
        }
      }
      
      return $return;
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
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      $return = '';
      
      $webObject->PageLog .= $_SERVER['REQUEST_URI'];
      
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
					return parent::getFrame($rb->get('pagelist.title'), '<h4 class="warning">'.$rb->get('pagelist.warning.nopages').'</h4>', "page-pagelist", true);
				}
			}
			
			// save block ------------------------ 
      
      if($_POST['delete'] == $rb->get('pagelist.action.delete')) {
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
            
            $return .= '<h4 class="success">'.$rb->get('pagelist.success.deleted').'!</h4>';
          }
        } else {
          $return .= '<h4 class="error">'.$rb->get('page.error.permissiondenied').'</h4>';
        }
      }
      
      if($_POST['move-up'] == $rb->get('pagelist.action.up')) {
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
      } elseif($_POST['move-down'] == $rb->get('pagelist.action.down')) {
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
          $return .= '<h4 class="error">'.$rb->get('pagelist.error.changeposition').'</h4>';
        }
      } elseif($_POST['move-branch'] == $rb->get('pagelist.action.move') || $_POST['copy-branch'] == $rb->get('pagelist.action.copy')) {
      	$pageId = $_POST['page-id'];
      	// test na prava!!!!!!!!!!!!!!!!!!!
      	
				$returnMove = '';
				
				$projects = $dbObject->fetchAll('SELECT DISTINCT `web_project`.`id`, `web_project`.`name` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `id`;');
				
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
					.'<form name="move-copy-branch" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<label for="select-parent">'.$rb->get('pagelist.field.movecopyparent').':</label> '
						.'<select class="select-webproject" name="select-parent" id="select-parent">'
							.$strProjects
						.'</select> '
						.'<input type="hidden" name="page-id" value="'.$pageId.'" />'
						.(($_POST['move-branch'] == $rb->get('pagelist.action.move')) ? ''
						.'<input type="submit" name="move-branch-to" value="'.$rb->get('pagelist.action.moveto').'" />'
						: ''
						.'<input type="submit" name="copy-branch-to" value="'.$rb->get('pagelist.action.copyto').'" />'
						)
					.'</form>'
				.'</div>';
				
				$return .= parent::getFrame((($_POST['move-branch'] == $rb->get('pagelist.action.move')) ? $rb->get('pagelist.action.moveto') : $rb->get('pagelist.action.copyto')), $returnMove, '', true);
			} elseif($_POST['move-branch-to'] == $rb->get('pagelist.action.moveto')) {
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
							$return .= '<h4 class="error">'.$rb->get('page.error.permissiondenied').'</h4>';
						}
					} else {
						$return .= '<h4 class="error">'.$rb->get('pagelist.error.someerror').'!</h4>';
					}
				} else {
					$return .= '<h4 class="error">'.$rb->get('page.error.permissiondenied').'</h4>';
				}
			} elseif($_POST['copy-branch-to'] == $rb->get('pagelist.action.copyto')) {
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
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
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
										$page['tag_lib_start'] = addslashes($page['tag_lib_start']);
										$page['tag_lib_end'] = addslashes($page['tag_lib_end']);
										$page['head'] = addslashes($page['head']);
										$page['content'] = addslashes($page['content']);
										$page['name'] = addslashes($page['name']);
										$page['href'] = addslashes($page['href']);
										$page['keywords'] = addslashes($page['keywords']);
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$page['href'].'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().', '.$page['cachetime'].');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= '<h4 class="success">'.$prb->get('pagelist.success.copied').'</h4>';
								} else {
									// zmenit url na nahodnou a vypsat ji.
									// rekurzivne zkopirovat vsechny stranky atd.
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
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
										$page['tag_lib_start'] = addslashes($page['tag_lib_start']);
										$page['tag_lib_end'] = addslashes($page['tag_lib_end']);
										$page['head'] = addslashes($page['head']);
										$page['content'] = addslashes($page['content']);
										$page['name'] = addslashes($page['name']);
										$page['href'] = addslashes($page['href']);
										$page['keywords'] = addslashes($page['keywords']);
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$randUrl.'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().', '.$page['cachetime'].');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= '<h4 class="success">'.$prb->get('pagelist.success.copied').'</h4><h4 class="warning">'.$rb->get('pagelist.warning.urlchanged').' "'.$randUrl.'"</h4>';
								}
							} else {
								// Testovat url v dane parent vetvi, nekopirovat vazby na TF
								$urls = $dbObject->fetchAll('SELECT `href` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id`  WHERE `page`.`parent_id` = '.$parentId.' AND `info`.`href` IN ('.$pagesUrl.') AND `page`.`wp` = '.$projectID.';');
								if(count($urls) == 0) {
									// neni treba menit url, je jedinecna v dane sekci
									// rekurzivne zkopirovat vsechny stranky atd.
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
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
										$page['tag_lib_start'] = addslashes($page['tag_lib_start']);
										$page['tag_lib_end'] = addslashes($page['tag_lib_end']);
										$page['head'] = addslashes($page['head']);
										$page['content'] = addslashes($page['content']);
										$page['name'] = addslashes($page['name']);
										$page['href'] = addslashes($page['href']);
										$page['keywords'] = addslashes($page['keywords']);
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$page['href'].'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().', '.$page['cachetime'].');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= '<h4 class="success">'.$prb->get('pagelist.success.copied').'</h4>';
								} else {
									// zmenit url na nahodnou a vypsat ji.
									// rekurzivne zkopirovat vsechny stranky atd.
									$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = '.$pageId.' ORDER BY `page`.`id`;');
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
										$page['tag_lib_start'] = addslashes($page['tag_lib_start']);
										$page['tag_lib_end'] = addslashes($page['tag_lib_end']);
										$page['head'] = addslashes($page['head']);
										$page['content'] = addslashes($page['content']);
										$page['name'] = addslashes($page['name']);
										$page['href'] = addslashes($page['href']);
										$page['keywords'] = addslashes($page['keywords']);
										$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$randUrl.'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().', '.$page['cachetime'].');');
										$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
										$lastId = $page['id'];
									}
									$return .= '<h4 class="success">'.$prb->get('pagelist.success.copied').'</h4><h4 class="warning">'.$rb->get('pagelist.warning.urlchanged').' "'.$randUrl.'"</h4>';
								}
							}
						} else {
							$return .= '<h4 class="error">'.$rb->get('page.error.permissiondenied').'</h4>';
						}
					} else {
						$return .= '<h4 class="error">'.$rb->get('pagelist.error.someerror').'!</h4>';
					}
				} else {
					$return .= '<h4 class="error">'.$rb->get('page.error.permissiondenied').'</h4>';
				}
			}
      
      // edit block -------------- 
			
			$returnTmp = '';
			
			if($_POST['remove-files'] == $rb->get('pagelist.action.removeselected')) {
        $pageId = $_POST['page-id'];
        $langId = $_POST['page-lang-id'];
        $files = $_POST['files'];
        
        foreach($files as $file => $val) {
          if($val = "on") {
            //$dbObject->execute("INSERT INTO `page_file_inc`(`file_id`, `page_id`, `language_id`) VALUES (".$file.", ".$pageId.", ".$langId.");");
            $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = ".$file." AND `page_id` = ".$pageId." AND `language_id` = ".$langId.";");
          }
        }
        
        /*$fileId = $_POST['file-id'];
        $pageId = $_POST['page-id'];
        $langId = $_POST['page-lang-id'];
        
        $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = ".$fileId." AND `page_id` = ".$pageId." AND `language_id` = ".$langId.";");
        */
        //$returnTmp = '<h4 class="success">'.$rb->get('pagelist.success.removed').'</h4>';
        $_POST['added-files'] = $rb->get('pagelist.action.addedfiles');
      } elseif($_POST['add-files'] == $rb->get('pagelist.action.addselected')) {
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
        
        //$returnTmp = '<h4 class="success">'.$rb->get('pagelist.success.added').'</h4>';
        $_POST['added-files'] = $rb->get('pagelist.action.addedfiles');
      }
			
			if($_POST['added-files'] == $rb->get('pagelist.action.addedfiles')) {
        $pageId = $_POST['page-id'];
        $langId = $_POST['page-lang-id'];
        $filesEx = array(WEB_TYPE_CSS => "Css", WEB_TYPE_JS => "Js");
        
        $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` LEFT JOIN `page_file_inc` ON `page_file`.`id` = `page_file_inc`.`file_id` WHERE `page_file_inc`.`page_id` = ".$pageId." AND `page_file_inc`.`language_id` = ".$langId.";");
        
        if(count($files) != 0) {
        	$returnTmp .= ''
        				.'<form name="files-to-remove" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                .'<table class="page-file-list">'
                  .'<tr class="file-tr">'
                    .'<th colspan="4" class="file-head-th">'.$rb->get('pagelist.field.addedfiles').':</th>'
                  .'</tr>';
          $i = 1;
        	foreach($files as $file) {
          $returnTmp .= '<tr class="file-tr '.((($i % 2) == 0) ? 'even' : 'idle').'">'
                      .'<td class="file-name">'
                          .'<label for="remove-text-files-files-'.$file['id'].'">'.$file['name'].'</label>'
                      .'</td>'
                      .'<td class="file-content">'
                        .'<label for="remove-text-files-files-'.$file['id'].'">'
													.'<div class="file-content-in"><div class="foo">'.substr($file['content'], 0, 130).'</div></div>'
												.'</label>'
                      .'</td>'
                      .'<td class="file-type">'
                      	.'<label for="remove-text-files-files-'.$file['id'].'">'
                        	.$filesEx[$file['type']]
                        .'</label>'
                      .'</td>'
                      /*.'<td>'
                        .(($editable) ? ''
                        .'<form name="process-file" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                          .'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                          .'<input type="hidden" name="page-id" value="'.$pageId.'" />'
                          .'<input type="hidden" name="page-lang-id" value="'.$langId.'" />'
                          .'<input type="hidden" name="remove-file" value="Remove" />'
                          .'<input type="image" src="'.WEB_ROOT.'images/page_del.png" name="remove-file" value="Remove" title="Remove file" />'
                        .'</form>'
                        : '')
                      .'</td>'*/
                      .'<td>'
                        .'<input id="remove-text-files-files-'.$file['id'].'" type="checkbox" name="files['.$file['id'].']" />'
                      .'</td>'
                    .'</tr>';
              $i ++;
      	  }
    	    $returnTmp .= '</table>'
	        				.'<div class="add-rem-text-files-submit">'
    	              .'<input type="hidden" name="page-id" value="'.$pageId.'" />'
  	                .'<input type="hidden" name="page-lang-id" value="'.$langId.'" />'
	                  .'<input type="submit" name="remove-files" value="'.$rb->get('pagelist.action.removeselected').'" />'
                  .'</div>'
									.'</form><div class="break"></div>';
  	      //$return1 = parent::getFrame('Added files', $returnTmp, '');
	        $return1 = $returnTmp;
        } else {
					$return1 = '<h4 class="warning">'.$rb->get('pagelist.warning.nofilesadded').'</h4>';
				}
                  
        
        $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` LEFT JOIN `page_file_inc` ON `page_file`.`id` = `page_file_inc`.`file_id` WHERE `id` NOT IN (SELECT `file_id` FROM `page_file_inc` WHERE `page_id` = ".$pageId." AND `language_id` = ".$langId.") AND `wp` = ".$_SESSION['selected-project']." ORDER BY `id`;");
        if(count($files) != 0) {
  	      $returnTmp = ''
                .'<form name="files-to-add" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                .'<table class="page-file-list">'
                  .'<tr class="file-tr">'
                    .'<th colspan="4" class="file-head-th">'.$rb->get('pagelist.field.filestoadd').'</th>'
                  .'</tr>';
	        $i = 1;
        	foreach($files as $file) {
      	    $returnTmp .= '<tr class="file-tr '.((($i % 2) == 0) ? 'even' : 'idle').'">'
                      .'<td class="file-name">'
                          .'<label for="add-text-files-files-'.$file['id'].'">'.$file['name'].'</label>'
                      .'</td>'
                      .'<td class="file-content">'
                        .'<div class="file-content-in"><div class="foo">'.'<label for="add-text-files-files-'.$file['id'].'">'.substr($file['content'], 0, 130).'</label>'.'</div></div>'
                      .'</td>'
                      .'<td class="file-type">'
                        .'<label for="add-text-files-files-'.$file['id'].'">'.$filesEx[$file['type']].'</label>'
                      .'</td>'
                      .'<td>'
                        .'<input id="add-text-files-files-'.$file['id'].'" type="checkbox" name="files['.$file['id'].']" />'
                      .'</td>'
                    .'</tr>';
    	      $i ++;
  	      }
	        $returnTmp .= '</table>'
	        				.'<div class="add-rem-text-files-submit">'
    	              .'<input type="hidden" name="page-id" value="'.$pageId.'" />'
  	                .'<input type="hidden" name="page-lang-id" value="'.$langId.'" />'
	                  .'<input type="submit" name="add-files" value="'.$rb->get('pagelist.action.addselected').'" />'
                  .'</div>'
                  .'</form>';
        	//$return2 = parent::getFrame('Files to add', $returnTmp, '');
        	$return2 = $returnTmp;
        } else {
					$return2 = '<h4 class="warning">'.$rb->get('pagelist.warning.nofilestoadd').'</h4>';
				}
        $return .= parent::getFrame($rb->get('pagelist.textfilestitle'), $return1.$return2, 'page-textfiles');
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
      //$return .= parent::getFrame($rb->get('pagelist.title'), $returnTmp, 'page-pagelist');
      
      if($_SESSION['selected-project'] != null) {
      	$returnTmp = ''
				.'<div class="add-page">'
					.'<ul>'
						.'<li>'
							.$rb->get('page.newpagecaption')
	    			    .'<form name="add-page" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
			  	      	.'<input type="hidden" name="parent-id" value="0" />'
			  	      	.'<input type="hidden" name="add-new-page" value="'.$rb->get('page.action.addpage').'" />'
    	  					.'<input type="image" src="~/images/page_add.png" name="add-new-page" value="'.$rb->get('page.action.addpage').'" />'
		  	  		  .'</form>'
    			  	.'</li>'
    		  	.'</ul>'
  	    .'</div>'
				.'<hr />'
				.$returnTmp;
	      //$return .= parent::getFrame($rb->get('pagelist.newtitle'), $returnTmp, 'page-newlist');
      }
      $return .= parent::getFrame($rb->get('pagelist.title'), $returnTmp, 'page-pagelist');
      
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
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
      $sql_return = $dbObject->fetchAll("SELECT `page`.`parent_id`, `page`.`id` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`parent_id` = ".$parentId." AND `page`.`wp` = ".$projectId." GROUP BY `page`.`id` ORDER BY `info`.`page_pos`;");
      if(count($sql_return) == 0 && $parentId == 0) {
				return '<h4 class="warning">'.$rb->get('pagelist.warning.nopages').'</h4>';
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
							.'<form name="page1" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="form-page1">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="page-edit" value="'.$rb->get('pagelist.field.edit').'" /> '
                .'<input type="image" title="'.$rb->get('pagelist.field.edit').'" src="'.WEB_ROOT.'images/page_edi.png" name="page-edit" value="'.$rb->get('page.action.edit').'" /> '
              .'</form>'
							.'<form name="page2" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="form-page2">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="page-add-sub" value="'.$rb->get('page.action.addsubpage').'" /> '
                .'<input type="image" title="'.$rb->get('page.action.addsubpage').'" src="'.WEB_ROOT.'images/page_add.png" name="page-add-sub" value="'.$rb->get('page.action.addsubpage').'" /> '
              .'</form>'
							.'<form name="page3" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="form-page3">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="added-files" value="'.$rb->get('pagelist.field.addedfiles').'" /> '
                .'<input type="image" title="'.$rb->get('pagelist.field.addedfiles').'" src="'.WEB_ROOT.'images/file_bws.png" name="added-files" value="'.$rb->get('pagelist.action.addedfiles').'" /> '
              .'</form>'
              .((!$parent || !$thisParent) ? ''
							.'<form name="page4" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="form-page4">'
                .'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="parent-id" value="'.$tmp['id'].'" /> '
                .'<input type="hidden" name="page-lang-id" value="'.$inf['lang_id'].'" /> '
                .'<input type="hidden" name="delete" value="'.$rb->get('pagelist.action.delete').'" /> '
                .'<input class="confirm" type="image" title="'.$rb->get('pagelist.field.delete2').', id('.$tmp['id'].')" src="'.WEB_ROOT.'images/lang_del.png" name="delete" value="'.$rb->get('pagelist.action.delete').'" />'
              .'</form>'
              : '')
						.'</div> } ';
          }
          $innText .= ''
          .'[ '
					.'<form name="page-move1" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="page-move1">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" />'
          	.'<input type="hidden" name="move-branch" value="'.$rb->get('pagelist.action.move').'" />'
            .'<input type="image" src="'.WEB_ROOT.'images/page_mov.png" title="'.$rb->get('pagelist.field.move').'" name="move-branch" value="'.$rb->get('pagelist.action.move').'" />'
          .'</form> '
					.'<form name="page-move2" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="page-move2">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" />'
          	.'<input type="hidden" name="copy-branch" value="'.$rb->get('pagelist.action.copy').'" />'
            .'<input type="image" src="'.WEB_ROOT.'images/page_cop.png" title="'.$rb->get('pagelist.field.copy').'" name="copy-branch" value="'.$rb->get('pagelist.action.copy').'" />'
          .'</form> '
					.'<form name="page-move3" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="page-move3">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="move-up" value="'.$rb->get('pagelist.action.up').'" /> '
            .'<input type="image" src="'.WEB_ROOT.'images/arro_up.png" title="'.$rb->get('pagelist.field.up').'" name="move-up" value="'.$rb->get('pagelist.action.up').'" />'
          .'</form>'
					.'<form name="page-move4" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="page-move4">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="move-down" value="'.$rb->get('pagelist.action.down').'" /> '
            .'<input type="image" src="'.WEB_ROOT.'images/arro_do.png" title="'.$rb->get('pagelist.field.down').'" name="move-down" value="'.$rb->get('pagelist.action.down').'" />'
          .'</form>'
          .'] '
					.'<form name="page-add-lang1" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="page-add-lang1">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="page-add-lang-ver" value="'.$rb->get('page.action.addlang').'" /> '
            .'<input type="image" title="'.$rb->get('pagelist.field.addlang').'" src="'.WEB_ROOT.'images/lang_add.png" name="page-add-lang-ver" value="'.$rb->get('page.action.addlang').'" /> '
          .'</form>'
          .((count($dbObject->fetchAll("SELECT `id` FROM `page` WHERE `parent_id` = ".$tmp['id'].";")) == 0) ? ''
					.'<form name="page-add-lang2" method="post" action="'.$_SERVER['REDIRECT_URL'].'" class="page-add-lang2">'
          	.'<input type="hidden" name="page-id" value="'.$tmp['id'].'" /> '
          	.'<input type="hidden" name="delete" value="'.$rb->get('pagelist.action.delete').'" /> '
            .'<input class="confirm" type="image" title="'.$rb->get('pagelist.field.delete').', id('.$tmp['id'].')" src="'.WEB_ROOT.'images/page_del.png" name="delete" value="'.$rb->get('pagelist.action.delete').'" />'
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
				$pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`parent_id` = '.$parentId.' ORDER BY `page`.`id`;');
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
					$page['tag_lib_start'] = addslashes($page['tag_lib_start']);
					$page['tag_lib_end'] = addslashes($page['tag_lib_end']);
					$page['head'] = addslashes($page['head']);
					$page['content'] = addslashes($page['content']);
					$page['name'] = addslashes($page['name']);
					$page['href'] = addslashes($page['href']);
					$page['keywords'] = addslashes($page['keywords']);
					$dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['name'].'", '.$page['in_title'].', "'.$page['href'].'", '.$page['in_menu'].', '.$newId.', '.$page['is_visible'].', "'.$page['keywords'].'", '.time().', '.$page['cachetime'].');');
					$dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES ('.$newId.', '.$page['language_id'].', "'.$page['tag_lib_start'].'", "'.$page['tag_lib_end'].'", "'.$page['head'].'", "'.$page['content'].'");');
					
					$lastId = $page['id'];
				}
			} else {
			
			}
		}
		
		public function showEditPageFile() {
      global $dbObject;
      global $loginObject;
      $return = "";
      $filesEx = array(WEB_TYPE_CSS => "Css", WEB_TYPE_JS => "Js");
      
      $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
      if(count($projects) != 0) {
      	if(array_key_exists('selected-project', $_SESSION)) {
					$projectId = $_SESSION['selected-project'];
				} else {
					$projectId = $projects[0]['id'];
				}
			} else {
				if(array_key_exists('selected-project', $_SESSION)) {
					$projectId = $_SESSION['selected-project'];
				} else {
					// dont care.
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
      
      return $return;
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
      
      // text file form block ---------------------
      
      if($_POST['delete-file'] == "Delete") {
        $fileId = $_POST['file-id'];
        $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = ".$fileId.";");
        $dbObject->execute("DELETE FROM `wp_wysiwyg_file` WHERE `tf_id` = ".$fileId.";");
        $dbObject->execute("DELETE FROM `page_file` WHERE `id` = ".$fileId.";");
      }
      $n = 1;
      $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` WHERE `wp` = ".$projectId." ORDER BY `id`;");
      if(count($files) != 0) {
	      $returnTmp .= ''
  	    .'<table class="page-file-list data-table">'
  	    	.'<thead>'
    	  	.'<tr class="file-tr">'
      			.'<th class="">Id</th>'
      			.'<th class="">Name</th>'
      			.'<th class="">Content</th>'
	      		.'<th class="">Type</th>'
  	    		.'<th class="">Edit</th>'
					.'</tr>'
					.'</thead>'
					.'<tbody>';     
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
              .'<form name="process-file1" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              	.'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                .'<input type="hidden" name="edit-file" value="Edit" />'
                .'<input type="image" src="'.WEB_ROOT.'images/page_edi.png" name="edit-file" value="Edit" title="Edit file" /> '
              .'</form>'
              .'<form name="process-file2" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
              	.'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                .'<input type="hidden" name="delete-file" value="Delete" />'
                .'<input class="confirm" type="image" src="'.WEB_ROOT.'images/page_del.png" name="delete-file" value="Delete" title="Delete file, id('.$file['id'].')" />'
              .'</form>'
              : '')
            .'</td>'
          .'</tr>';
        	$n ++;
      	}
      	$returnTmp .= '</tbody></table>';
      	//$return .= parent::getFrame('Text files', $returnTmp, '');
      } else {
				//$return .= parent::getFrame('Text files', '<h4 class="error">No files to edit!</h4>', '');
				$returnTmp .= '<h4 class="error">No files to edit!</h4>';
			}
      
      $returnTmp .= ''
      .'<hr />'
      .'<form name="add-file" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
      	.'<input type="submit" name="add-file" value="New file" title="Create new file" />'
      .'</form>';
      $return .= parent::getFrame('Text Files', $returnTmp, '');
      return $return;
    }
    
    /**
     *
     *  Generates form for updating text files.
     *
     */                        
    private function getFileUpdateForm($fileId, $fileName, $fileContent, $browsers, $fileTypes) {
    	include_once('System.class.php');
    	$htmlBrowsers = '';
    	foreach($browsers as $browser => $value) {
				$htmlBrowsers .= ''
				.'<div class="text-file-browser">'
					.'<label for="browser-'.strtolower($browser).'">'.$browser.'</label> '
					.'<input type="checkbox" name="browser-'.strtolower($browser).'"'.(($value == 1) ? ' checked="checked"' : '').' />'
				.'</div>';
			}
    
      $returnTmp = ''
                .'<form name="edit-file" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
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
                    .'<div class="text-file-content">';
			
      $name = 'Page.editors';
    	$system = new System();
			$propertyEditors = $system->getPropertyValue($name);
		  $editAreaTextFileRows = $system->getPropertyValue('Page.editAreaTextFileRows');
		  
		  if($propertyEditors == "edit_area") {
		  	$returnTmp .= ''
					.'<div id="editors" class="editors edit-area-editors">'
						.'<div id="cover-page-file-content">'
							.'<label for="file-content">File content:</label>'
							.'<textarea id="file-content" class="edit-area html" name="file-content" rows="'.($editAreaTextFileRows > 0 ? $editAreaTextFileRows : 30).'" wrap="off">'.str_replace('~', '&#126', $fileContent).'</textarea>'
						.'</div>'
					.'</div>';
			} else {
      	$returnTmp .= ''
                      .'<label for="file-content">Content:</label> '
                      .'<div class="editor-cover">'
                      	.'<div class="textarea-cover">'
                      		.'<textarea name="file-content" class="editor-textarea" rows="15" wrap="off">'.str_replace('~', '&#126', $fileContent).'</textarea> '
                      	.'</div>'
                      	.'<div class="clear"></div>'
                      .'</div>';
      } 
      $returnTmp .= ''
                    .'</div>'
                    .'<div class="text-file-submit">'
                      .(($fileId != -1) ? '<input type="hidden" name="file-id" value="'.$fileId.'" />' : '')
                      .'<input type="submit" name="save" value="Save" title="Save changes" /> '
                      .'<input type="submit" name="save" value="Save and Close" title="Save changes and Close file" /> '
                      .'<input type="submit" name="close" value="Close" title="Close file" /> '
                    .'</div>'
                  .'</div>'
                .'</form>';
      return parent::getFrame('Edit file'.((strlen($fileName) != 0) ? ' :: '.$fileName.' ( '.$fileId.' )' : ''), $returnTmp, '');
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
				.'<form name="clear-url-cache" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<input type="submit" name="clear-url-cache" value="Do \'Clear Url Cache\'" />'
				.'</form>'
			.'</div>';
			
			return $return.parent::getFrame("Clear Url Cache", $returnForm, "", true);
		}
    
    /**
     *
     *	Edit & clear url cache.
     *	C tag.     
     *	
     *	@return		form		      
     *
     */		 		 		 		     
    public function manageUrlCache() {
			global $dbObject;
			$return = $msg = '';
			$projectId = 0;
			$pageId = '';
			$partOfUrl = '';
			$urlCache = array();
			$urlCacheReturn = '';
			$sent = false;
			
			if($_POST['clear-url-cache'] == "Do 'Clear Url Cache'") {
				$dbObject->execute("TRUNCATE TABLE `urlcache`");
				$msg = '<h4 class="success">Url cache cleared!</h4>';
			}
			
			if($_POST['delete-from-url-cache'] == 'Delete selected') {
				$delete = $_POST['url-cache-delete-checkbox'];
				foreach($delete as $del) {
					$dbObject->execute('DELETE FROM `urlcache` WHERE `id` = '.$del.';');
				}
				$msg = '<h4 class="success">Selected items have been deleted!</h4>';
				$_POST['show-url-cache'] = 'Show url cache';
				$sent = true;
			}
			
			if($_POST['show-url-cache'] == 'Show url cache') {
				$pageId = $_POST['page-id-url-cache'];
				$projectId = $_POST['project-id-url-cache'];
				$partOfUrl = $_POST['part-of-url-url-cache'];
				if(strlen($pageId) != 0 && strlen($partOfUrl) != 0 && $projectId != 0) {
					$urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`project_url`, `urlcache`.`page-ids`, `language`.`language`, `language`.`id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`http`, `web_project`.`https`, `web_project`.`url` as `wp_url`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`wp` = `web_project`.`id` WHERE (`urlcache`.`page-ids` LIKE "'.$pageId.'-%" OR `urlcache`.`page-ids` LIKE "%-'.$pageId.'-%" OR `urlcache`.`page-ids` LIKE "%-'.$pageId.'") AND (`urlcache`.`url` LIKE "%'.$partOfUrl.'%") AND `web_project`.`id` = '.$projectId.' ORDER BY `urlcache`.`id`;');
				} elseif(strlen($pageId) != 0 && strlen($partOfUrl) != 0) {
					$urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`project_url`, `urlcache`.`page-ids`, `language`.`language`, `language`.`id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`http`, `web_project`.`https`, `web_project`.`url` as `wp_url`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`wp` = `web_project`.`id` WHERE (`urlcache`.`page-ids` LIKE "'.$pageId.'-%" OR `urlcache`.`page-ids` LIKE "%-'.$pageId.'-%" OR `urlcache`.`page-ids` LIKE "%-'.$pageId.'") AND (`urlcache`.`url` LIKE "%'.$partOfUrl.'%") ORDER BY `urlcache`.`id`;');
				} elseif(strlen($partOfUrl) != 0 && $projectId != 0) {
					$urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`project_url`, `urlcache`.`page-ids`, `language`.`language`, `language`.`id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`http`, `web_project`.`https`, `web_project`.`url` as `wp_url`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`wp` = `web_project`.`id` WHERE (`urlcache`.`url` LIKE "%'.$partOfUrl.'%") AND `web_project`.`id` = '.$projectId.' ORDER BY `urlcache`.`id`;');
				} elseif(strlen($partOfUrl) != 0) {
					$urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`project_url`, `urlcache`.`page-ids`, `language`.`language`, `language`.`id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`http`, `web_project`.`https`, `web_project`.`url` as `wp_url`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`wp` = `web_project`.`id` WHERE (`urlcache`.`url` LIKE "%'.$partOfUrl.'%") ORDER BY `urlcache`.`id`;');
				} elseif(strlen($pageId) != 0) {
					$urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`project_url`, `urlcache`.`page-ids`, `language`.`language`, `language`.`id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`http`, `web_project`.`https`, `web_project`.`url` as `wp_url`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`wp` = `web_project`.`id` WHERE (`urlcache`.`page-ids` LIKE "'.$pageId.'-%" OR `urlcache`.`page-ids` LIKE "%-'.$pageId.'-%" OR `urlcache`.`page-ids` LIKE "%-'.$pageId.'") ORDER BY `urlcache`.`id`;');
				} elseif($projectId != 0) {
					$urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`project_url`, `urlcache`.`page-ids`, `language`.`language`, `language`.`id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`http`, `web_project`.`https`, `web_project`.`url` as `wp_url`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`wp` = `web_project`.`id` WHERE `web_project`.`id` = '.$projectId.' ORDER BY `urlcache`.`id`;');
				} else {
					$urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`project_url`, `urlcache`.`page-ids`, `language`.`language`, `language`.`id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`http`, `web_project`.`https`, `web_project`.`url` as `wp_url`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`wp` = `web_project`.`id` ORDER BY `urlcache`.`id`;');
				}
				
				if(count($urlCache) > 0) {
					$urlCacheReturn .= ''
					.'<table class="url-cache-table data-table">'
						.'<thead>'
						.'<tr>'
							.'<th class="url-cache-id">Id:</th>'
							.'<th class="url-cache-link">Link:</th>'
							.'<th class="url-cache-name">Name:</th>'
							.'<th class="url-cache-wp-url">Web project url:</th>'
							.'<th class="url-cache-slash"></th>'
							.'<th class="url-cache-url">Url:</th>'
							.'<th class="url-cache-page">Page Ids:</th>'
							.'<th class="url-cache-lang">Lang:</th>'
							.'<th class="url-cache-cachetime">Cachetime:</th>'
							.'<th class="url-cache-lastcache">Lastcache:</th>'
							.'<th class="url-cache-edit">Edit:</th>'
						.'</tr>'
						.'</thead>'
						.'<tbody>';
					
					$i = 1;
					foreach($urlCache as $url) {
						$cacheTime = '';
						if($url['cachetime'] == -1) {
							$cacheTime = 'Don\'t use';
						} elseif($url['cachetime'] == 60) {
							$cacheTime = '1 minute';
						} elseif($url['cachetime'] == 3600) {
							$cacheTime = '1 hour';
						} elseif($url['cachetime'] == 86400) {
							$cacheTime = '1 day';
						} elseif($url['cachetime'] == 0) {
							$cacheTime = 'Unlimited';
						}
						
						$urlCacheReturn .= ''
						.'<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">'
							.'<td class="url-cache-id"><label for="url-cache-delete-checkbox-'.$url['id'].'">'.$url['id'].'</label></td>'
							.'<td class="url-cache-id"><a target="_blank" href="http://'.$url['project_url'].'/'.$url['url'].'">view</a></td>'
							.'<td class="url-cache-name"><label for="url-cache-delete-checkbox-'.$url['id'].'">'.$url['name'].'</label></td>'
							.'<td class="url-cache-wp-url"><label for="url-cache-delete-checkbox-'.$url['id'].'">'.$url['project_url'].'</label></td>'
							.'<td class="url-cache-slash"><label for="url-cache-delete-checkbox-'.$url['id'].'">/</label></td>'
							.'<td class="url-cache-url"><label for="url-cache-delete-checkbox-'.$url['id'].'">'.$url['url'].'</label></td>'
							.'<td class="url-cache-page"><label for="url-cache-delete-checkbox-'.$url['id'].'" langid="'.$url['lang-id'].'">'.$url['page-ids'].'</label></td>'
							.'<td class="url-cache-lang"><label for="url-cache-delete-checkbox-'.$url['id'].'">'.$url['language'].'</label></td>'
							.'<td class="url-cache-cachetime"><label for="url-cache-delete-checkbox-'.$url['id'].'">'.$cacheTime.'</label></td>'
							.'<td class="url-cache-lastcache"><label for="url-cache-delete-checkbox-'.$url['id'].'">'.(($url['lastcache'] > 0) ? date('H:i:s d.m.Y', $url['lastcache']) : '-').'</label></td>'
							.'<td class="url-cache-edit">'
								.'<input id="url-cache-delete-checkbox-'.$url['id'].'" type="checkbox" name="url-cache-delete-checkbox[]" value="'.$url['id'].'" />'
							.'</td>'
						.'</tr>';
						$i ++;
					}
						
					$urlCacheReturn .= '</tbody></table>';
				}
				$sent = true;
			}
			
			$projectsDb = $dbObject->fetchAll('SELECT `id`, `name` FROM `web_project` ORDER BY `name`;');
			$projects = '';
			foreach($projectsDb as $prj) {
				$projects .= '<option value="'.$prj['id'].'"'.(($projectId == $prj['id']) ? 'selected="selected"' : '').'>'.$prj['name'].'</option>';
			}
			
			$returnForm = ''
			.((strlen($msg) > 0) ? $msg : '' )
			.'<div id="clear-url-cache" class="clear-url-cache">'
				.'<form name="clear-url-cache" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<div class="part-of-url">'
						.'<label for="part-of-url-url-cache">Part of url:</label> '
						.'<input id="part-of-url-url-cache" type="text" name="part-of-url-url-cache" value="'.$partOfUrl.'" />'
					.'</div>'
					.'<div class="project-id">'
						.'<label for="project-id-url-cache">Project:</label> '
						.'<select id="project-id-url-cache" type="text" name="project-id-url-cache">'
							.'<option value="0">All</option>'
							.$projects
						.'</select>'
					.'</div>'
					.'<div class="page-id">'
						.'<label for="page-id-url-cache">Page id:</label> '
						.'<input id="page-id-url-cache" type="text" name="page-id-url-cache" value="'.$pageId.'" />'
					.'</div>'
					.'<div class="clear"></div>'
					.'<div id="clear-url-cache-submit" class="submit">'
						.'<input type="submit" name="show-url-cache" value="Show url cache" />'
						.((strlen($urlCacheReturn) > 0) ? ''
						.'<input class="confirm" type="submit" name="delete-from-url-cache" value="Delete selected" title="Delete selected items from urlcache" />'
						: '')
						.'<input class="confirm" type="submit" name="clear-url-cache" value="Do \'Clear Url Cache\'" title="Clear whole url cache" />'
						.(($sent) ? ''
						.'<input type="submit" name="cancel-url-cache" value="Cancel" />'
						: '')
					.'</div>'
					.'<div class="results">'
						.$urlCacheReturn
					.'</div>'
				.'</form>'
			.'</div>';
			
			return $return.parent::getFrame("Manage Url Cache", $returnForm, "", true);
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
				.'<form class="update-keywords" name="update-keywords" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
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
						.'<form name="delete-lang" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
							.'<input type="hidden" name="language-id" value="'.$lang['id'].'" />'
							.'<input type="hidden" name="delete-language" value="Delete language" />'
							.'<input class="confirm" type="image" src="'.WEB_ROOT.'images/page_del.png" name="delete-language" value="Delete language" title="Delete language, id('.$lang['id'].')" />'
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
					.'<form name="add-new-language" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
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
				.'<table class="template-list data-table">'
					.'<thead>'
					.'<tr>'
						.'<th class="template-id">Id:</th>'
						.'<th class="template-content">Content:</th>'
						.'<th class="template-edit">Edit:</th>'
					.'</tr>'
					.'</thead>'
					.'<tbody>';
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
							.'<form name="template-edit2" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
								.'<input type="hidden" name="template-id" value="'.$template['id'].'" />'
								.'<input type="hidden" name="template-delete" value="Delete" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="template-delete" value="Delete" title="Delete template, id('.$template['id'].')" />'
							.'</form>'
						.'</td>'
					.'</tr>';
					$i ++;
				}
				$return .= ''
					.'</tbody>'
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
			$actionUrl = $_SERVER['REDIRECT_URL'];
			if($submitPageId != false) {
				$actionUrl = $webObject->composeUrl($submitPageId);
			}
			
			if($_POST['template-submit'] == "Save") {
				$templateId = $_POST['template-id'];
				$templateContent = $_POST['template-content'];
				$templateContent = str_replace('"', '\"', $templateContent);
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
						if($showError != 'false') {
							$return .= '<h4 class="success">Template added!</h4>';
						}
					} else {
						$dbObject->execute('UPDATE `template` SET `content` = "'.$templateContent.'" WHERE `id` = '.$templateId.';');
						if($showError != 'false') {
							$return .= '<h4 class="success">Template updated!</h4>';
						}
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
					.'<div class="clear"></div>';
				
				require_once('System.class.php');	
				$name = 'Page.editors';
    		$system = new System();
				$propertyEditors = $system->getPropertyValue($name);
		  	$editAreaTextFileRows = $system->getPropertyValue('Page.editAreaTextFileRows');
		  
		  	if($propertyEditors == "edit_area") {
		  		$return .= ''
					.'<div id="editors" class="editors edit-area-editors">'
						.'<div id="template-edit-detail-content">'
							.'<label for="template-edit-detail-content">Template content:</label>'
							.'<textarea id="template-content" class="edit-area html" name="template-content" rows="'.($editAreaTextFileRows > 0 ? $editAreaTextFileRows : 30).'" wrap="off">'.str_replace('~', '&#126', $template['content']).'</textarea>'
						.'</div>'
					.'</div>';
				} else {
					$return .= ''
					.'<div class="template-edit-content">'
						.'<label for="template-edit-detail-content">Content:</label>'
						.'<div class="editor-cover">'
							.'<div class="textarea-cover">'
								.'<textarea id="template-edit-detail-content" class="editor-textarea" name="template-content" rows="15" wrap="off">'.$template['content'].'</textarea>'
							.'</div>'
						.'</div>'
					.'</div>';
				}
				$return .= ''
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
