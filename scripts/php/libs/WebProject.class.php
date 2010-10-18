<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  require_once("System.class.php");
  
  /**
   * 
   *  Class WebProject.
   *  management of web projects
   *      
   *  @author     Marek SMM
   *  @timestamp  2010-08-16
   * 
   */  
  class WebProject extends BaseTagLib {
  
  	private $BundleName = 'webproject';
  	
  	private $BundleLang = 'cs';
  
    public function __construct() {
    	global $webObject;
    	
      	parent::setTagLibXml("xml/WebProject.xml");
      
      	if($webObject->LanguageName != '') {
			$rb = new ResourceBundle();
			if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
				$this->BundleLang = $webObject->LanguageName;
			}
		}
      
      	require_once("scripts/php/classes/ResourceBundle.class.php");
    }
		
	/**
	 *
	 *	Select web project id and save it to session var.
	 *	C tag.
	 *
	 */		 		 		 		 		
	public function selectProject($useFrames = false, $showMsg = false) {
		global $dbObject;
		global $loginObject;
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		$projects = array();
		
		if($_POST['select-project'] == $rb->get('selectproject.submit')) {
			$projectId = $_POST['project-id'];
			$permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `wp` = '.$projectId.' AND `type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
			if(count($permission) > 0) {
				$_SESSION['selected-project'] = $projectId;
				if($showMsg != 'false') {
					$return .= '<h4 class="success">'.$rb->get('selectproject.success').'</h4>';
				}
			} else {
				if($showMsg != 'false') {
					$return .= '<h4 class="error">'.$rb->get('selectproject.failed').'</h4>';
				}
			}
			$projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
		} else {
			$projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
			$ok = false;
			foreach($projects as $project) {
				if($_SESSION['selected-project'] == $project['id']) {
					$ok = true;
				}
			}
			if(!$ok) {
				$val = parent::system()->getPropertyValue('WebProject.defaultProjectId');
				$proj = parent::db()->fetchSingle('select `id` from `web_project` where `id` = '.$val.';');
				if($val > 0 && $proj != array()) {
					$_SESSION['selected-project'] = $val;
				} elseif(count($projects) > 0) {
					$_SESSION['selected-project'] = $projects[0]['id'];
				} else {
					$_SESSION['selected-project'] = '';
				}
			}
		}
		
		$projectId = $_SESSION['selected-project'];
		
		if(count($projects) > 0) {
			$return .= ''
			.'<div class="select-project">'
				.'<form name="select-project" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<label for="select-project">'.$rb->get('selectproject.label').'</label> '
					.'<select id="select-project" name="project-id">';
			$projects = $dbObject->fetchAll('SELECT DISTINCT `web_project`.`id`, `web_project`.`name` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `web_project`.`name`;');
			foreach($projects as $project) {
				$permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `wp` = '.$project['id'].' AND `type` = '.WEB_R_WRITE.' ORDER BY `value` DESC;');
				if(count($permission)) {
					$return .= '<option value="'.$project['id'].'"'.(($projectId == $project['id']) ? ' selected="selected"' : '').'>'.$project['name'].'</option>';
				}
			}
			$return .= ''
					.'</select> '
					.'<input type="submit" name="select-project" value="'.$rb->get('selectproject.submit').'" />'
				.'</form>'
			.'</div>';
		} else {
			$return .= '<div class="select-project">'.parent::getWarning('No projects').'</div>';
		}
			
		if($useFrames == "false") {
			return $return;
		} else {
			return parent::getFrame($rb->get('selectproject.label'), $return, "", true);
		}
	}
		
		/**
		 *
		 *	Show all web projects which user can see, edit, delete ...
		 *	C tag.		 
		 *
		 *
		 */		 		 		 		 		
		public function showProjects($detailPageId = false, $editable = false) {
			global $webObject;
			global $dbObject;
			global $loginObject;
			$return = '';
			
			if($_POST['delete'] == "Delete Project") {
				$projectId = $_POST['wp'];
				$permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`wp` = '.$projectId.' AND `web_project_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
				if(count($permission) > 0) {
					$pages = $dbObject->fetchAll('SELECT `id` FROM `page` WHERE `wp` = '.$projectId.';');
					if(count($pages) == 0) {
						$dbObject->execute('DELETE FROM `web_project_right` WHERE `wp` = '.$projectId.';');
						$dbObject->execute('DELETE FROM `web_alias` WHERE `project_id` = '.$projectId.';');
						$dbObject->execute('DELETE FROM `web_project` WHERE `id` = '.$projectId.';');
					
						$return .= '<h4 class="success">Project deleted!</h4>';
					} else {
						$return .= '<h4 class="error">can\'t delete project, still exists pages in this project!</h4>';
					}
				} else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
			}
			
			$actionUrl = $_SERVER['REDIRECT_URL'];
			if($editable == "true" && $detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
			
			$projects = $dbObject->fetchAll('SELECT DISTINCT `web_project`.`id`, `web_project`.`name`, `web_project`.`url`, `web_project`.`http`, `web_project`.`https` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `id`;');
			
			if(count($projects) == 0) {
				$return .= '<h4 class="error">No projects to show!</h4>';
			} else {
				$return .= ''
				.'<table class="show-projects data-table standart clickable">'
					.'<thead>'
						.'<tr>'
							.'<th class="th-id">Id:</th>'
							.'<th class="th-name">Name:</th>'
							.'<th class="th-url">Url:</th>'
							.'<th class="th-url">View:</th>'
							.'<th class="th-protocol">Protocol:</th>'
							.(($editable == "true") ? ''
							.'<th class="th-edit"></th>'
							: '')
						.'</tr>'
					.'</thead>'
					.'<tbody>';
			
				$i = 1;
				foreach($projects as $project) {
					$rights = $dbObject->fetchAll("SELECT `group`.`name` FROM `group` LEFT JOIN `web_project_right` ON `group`.`gid` = `web_project_right`.`gid` WHERE `page_right`.`pid` = ".$project['id']." AND `web_project_right`.`type` = ".WEB_R_WRITE.";");
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
  	      }
    	    
      	  $pages = $dbObject->fetchAll('SELECT `id` FROM `page` WHERE `wp` = '.$project['id'].' LIMIT 1;');
	        if($ok == true) {
						$return .= ''
						.'<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">'
							.'<td class="td-id">'.$project['id'].'</td>'
							.'<td class="td-name">'.$project['name'].'</td>'
							.'<td class="td-url">'.$project['url'].'</td>'
							.'<td class="td-url">'.(($project['http'] == 1 || $project['https'] == 1) ? '<a target="_blank" href="'.(($project['http'] == 1) ? 'http://' : 'https://').$project['url'].'">view</a>' : '').'</td>'
							.'<td class="td-protocol">'
								.(($project['http'] == 1) ? 'http ' : '')
								.(($project['https'] == 1) ? 'https ' : '')
							.'</td>'
							.'<td class="td-edit">'
							.(($editable == "true") ? ''
								.'<form name="edit-projects1" method="post" action="'.$actionUrl.'"> '
									.'<input type="hidden" name="wp" value="'.$project['id'].'" />'
									.'<input type="hidden" name="edit" value="Edit Project" />'
									.'<input type="image" src="~/images/page_edi.png" name="edit" value="Edit Project" title="Edit project" />'
								.'</form>'
								.((count($pages) == 0) ? ''
								.'<form name="edit-projects2" method="post" action="'.$_SERVER['REDIRECT_URL'].'"> '
									.'<input type="hidden" name="wp" value="'.$project['id'].'" />'
									.'<input type="hidden" name="delete" value="Delete Project" />'
									.'<input class="confirm" type="image" src="~/images/page_del.png" name="delete" value="Delete Project" title="Delete project, id('.$project['id'].')" />'
								.'</form>'
								: '')
							: '')
							.'</td>'
						.'</tr>';
					
						$i ++;
					}
				}
			
				$return .= ''
					.'</tbody>'
				.'</table>';
			}	

			$right = $dbObject->fetchAll('SELECT `web_project_right`.`wp` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `web_project_right`.`wp` = 0;');
			if(count($right) != 0) {
				$return .= ''
				.(($editable == "true") ? ''
				.'<hr />'
				.'<form name="projects-new" method="post" action="'.$actionUrl.'">'
					.'<input type="submit" name="new-project" value="New Project" title="Create new project" />'
					.''
					.''
				.'</form>'
				: '');
			}
			
			//$loginObject->getGroups();
			
			return parent::getFrame('Web projects', $return, "", true);
		}
		
		/**
		 *
		 *	Generates from for editing project.
		 *	C tag.		 
		 *
		 */		 		 		 		
		public function showEditForm($showNotSelectedError = false) {
			global $webObject;
			global $dbObject;
			global $loginObject;
			$projectData = null;
			$formSave = false;
			$return = '';
			
			
			// ukladani chybovych stranek .... ;)
			// project-errors-all
			
			
			if($_POST['save-project'] == 'Save') {
				$project = array('id' => $_POST['project-id'], 'name' => $_POST['project-name'], 'url' => $_POST['project-url'], 'http' => $_POST['project-http'], 'https' => $_POST['project-https'], 'alias1' => $_POST['project-alias1'], 'alias2' => $_POST['project-alias2'], 'alias3' => $_POST['project-alias3'], 'read' => $_POST['project-right-edit-groups-r'], 'write' => $_POST['project-right-edit-groups-w'], 'delete' => $_POST['project-right-edit-groups-d'], 'wysiwyg' => $_POST['project-edit-styles-wysiwyg'], 'error_all_pid' => $_POST['project-errors-all'], 'error_404_pid' => $_POST['project-errors-404'], 'error_403_pid' => $_POST['project-errors-403']);
				$errors = array();
				
				$permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`wp` = '.$project['id'].' AND `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
				if(count($permission) > 0) {
					$sameNames = $dbObject->fetchAll('SELECT `id` FROM `web_project` WHERE `name` = "'.$project['name'].'" AND `id` != '.$project['id'].';');
					if(count($sameNames) != 0) {
						$errors[] = 'Project with this name already exists!';
					}
					$sameUrls = $dbObject->fetchAll('SELECT `id` FROM `web_project` WHERE `url` IN ("'.$project['url'].'"'.((strlen($project['alias1']) != 0) ? ', "'.$project['alias1'].'"' : '').((strlen($project['alias2']) != 0) ? ', "'.$project['alias2'].'"' : '').((strlen($project['alias3']) != 0) ? ', "'.$project['alias3'].'"' : '').') AND `id` != '.$project['id'].';');
					if(count($sameUrls) != 0) {
						$errors[] = 'Project with this url or alias url already exists!';
					}
					$sameUrls = $dbObject->fetchAll('SELECT `project_id` FROM `web_alias` WHERE `url` IN ("'.$project['url'].'"'.((strlen($project['alias1']) != 0) ? ', "'.$project['alias1'].'"' : '').((strlen($project['alias2']) != 0) ? ', "'.$project['alias2'].'"' : '').((strlen($project['alias3']) != 0) ? ', "'.$project['alias3'].'"' : '').') AND `project_id` != '.$project['id'].';');
					if(count($sameUrls) != 0) {
						$errors[] = 'Project with this url or alias url already exists!';
					}
					$project['http'] = (($project['http'] == "on") ? 1 : 0);
					$project['https'] = (($project['https'] == "on") ? 1 : 0);
					
					if(count($errors) == 0) {
						if($project['id'] == 0) {
							// vlozit novy projekt
							$dbObject->execute('INSERT INTO `web_project`(`name`, `url`, `http`, `https`, `error_all_pid`, `error_404_pid`, `error_403_pid`) VALUES ("'.$project['name'].'", "'.$project['url'].'", '.$project['http'].', '.$project['https'].', '.$project['error_all_pid'].', '.$project['error_404_pid'].', '.$project['error_403_pid'].');');
							$projectId = $dbObject->fetchAll('SELECT `id` FROM `web_project` WHERE `name` = "'.$project['name'].'";');
							$projectId = $projectId[0]['id'];
							if(strlen($project['alias1']) != 0) {
								$dbObject->execute('INSERT INTO `web_alias`(`project_id`, `url`, `http`, `https`) VALUES ("'.$projectId.'", "'.$project['alias1'].'", '.$project['http'].', '.$project['https'].');');
							}
							if(strlen($project['alias2']) != 0) {
								$dbObject->execute('INSERT INTO `web_alias`(`project_id`, `url`, `http`, `https`) VALUES ("'.$projectId.'", "'.$project['alias2'].'", '.$project['http'].', '.$project['https'].');');
							}
							if(strlen($project['alias3']) != 0) {
								$dbObject->execute('INSERT INTO `web_alias`(`project_id`, `url`, `http`, `https`) VALUES ("'.$projectId.'", "'.$project['alias3'].'", '.$project['http'].', '.$project['https'].');');
							}
							
            	foreach($project['read'] as $right) {
              	$dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (".$projectId.", ".$right.", ".WEB_R_READ.");");
            	}
          	  foreach($project['write'] as $right) {
        	      $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (".$projectId.", ".$right.", ".WEB_R_WRITE.");");
      	      }
    	        foreach($project['delete'] as $right) {
  	            $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (".$projectId.", ".$right.", ".WEB_R_DELETE.");");
	            }					
						} else {
							// update stavajiciho projektu 
							$dbObject->execute('UPDATE `web_project` SET `name` = "'.$project['name'].'", `url` = "'.$project['url'].'", `http` = '.$project['http'].', `https` = '.$project['https'].', `error_all_pid` = '.$project['error_all_pid'].', `error_404_pid` = '.$project['error_404_pid'].', `error_403_pid` = '.$project['error_403_pid'].' WHERE `id` = '.$project['id'].';');
							// UPDATE ALIASuu !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
							$dbA = $dbObject->fetchAll("SELECT `id`, `url` FROM `web_alias` WHERE `web_alias`.`project_id` = ".$project['id'].";");
							$newAliases = array(0 => true, 1 => true, 2 => true); 
  	          foreach($dbA as $alias) {
  	          	if($alias['url'] != $project['alias1'] && $alias['url'] != $project['alias2'] && $alias['url'] != $project['alias3']) {
									$dbObject->execute('DELETE FROM `web_alias` WHERE `id` = '.$alias['id'].';');
								}
								if($alias['url'] == $project['alias1']) {
									$newAliases[0] = false;
								}
								if($alias['url'] == $project['alias2']) {
									$newAliases[1] = false;
								}
								if($alias['url'] == $project['alias3']) {
									$newAliases[2] = false;
								}
            	}
							for($i = 0; $i < 3; $i ++) {
								if($newAliases[$i] && strlen($project['alias'.($i + 1)]) != 0) {
									$dbObject->execute('INSERT INTO `web_alias`(`project_id`, `url`, `http`, `https`) VALUES ("'.$project['id'].'", "'.$project['alias'.($i + 1)].'", '.$project['http'].', '.$project['https'].');');
								}
							}
							if(count($project['read']) != 0) {
								$dbR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `web_project_right`.`wp` = ".$project['id']." AND `type` = ".WEB_R_READ.";");
  		          foreach($dbR as $right) {
		              if(!in_array($right, $project['read'])) {
                		$dbObject->execute("DELETE FROM `web_project_right` WHERE `wp` = ".$project['id']." AND `type` = ".WEB_R_READ.";");
              		}
            		}
          		  foreach($project['read'] as $right) {
        		      $row = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$project['id']." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
      		        if(count($row) == 0) {
    		            $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (".$project['id'].", ".$right.", ".WEB_R_READ.")");
  		            }
		            }
	            }
	            if(count($project['write']) != 0) {
	            	$dbR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `web_project_right`.`wp` = ".$project['id']." AND `type` = ".WEB_R_WRITE.";");
  	        	  foreach($dbR as $right) {
    	    	      if(!in_array($right, $project['write'])) {
      		          $dbObject->execute("DELETE FROM `web_project_right` WHERE `wp` = ".$project['id']." AND `type` = ".WEB_R_WRITE.";");
    	  	        }
  	      	    }
	          	  foreach($project['write'] as $right) {
              		$row = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$project['id']." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
            	  	if(count($row) == 0) {
          	      	$dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (".$project['id'].", ".$right.", ".WEB_R_WRITE.")");
	        	      }
  	    	      }
  	    	    }
     	      	if(count($project['delete']) != 0) {
	    	        $dbR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `web_project_right`.`wp` = ".$project['id']." AND `type` = ".WEB_R_DELETE.";");
  		          foreach($dbR as $right) {
	  	            if(!in_array($right, $project['delete'])) {
      	          	$dbObject->execute("DELETE FROM `web_project_right` WHERE `wp` = ".$project['id']." AND `type` = ".WEB_R_DELETE.";");
        	      	}
          	  	}
          		  foreach($project['delete'] as $right) {
        	    	  $row = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$project['id']." AND `type` = ".WEB_R_DELETE." AND `gid` = ".$right.";");
      	        	if(count($row) == 0) {
    	            	$dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (".$project['id'].", ".$right.", ".WEB_R_DELETE.");");
	  	            }
		            }
		          }
     	      	if(count($project['wysiwyg']) != 0) {
	    	        $stylesS = $dbObject->fetchAll("SELECT `tf_id` FROM `wp_wysiwyg_file` WHERE `wp` = ".$project['id'].";");
  		          foreach($stylesS as $style) {
	  	            if(!in_array($style, $project['wysiwyg'])) {
      	          	$dbObject->execute("DELETE FROM `wp_wysiwyg_file` WHERE `wp` = ".$project['id']." AND `tf_id` = ".$style['tf_id'].";");
        	      	}
          	  	}
          		  foreach($project['wysiwyg'] as $style) {
        	    	  $row = $dbObject->fetchAll("SELECT `tf_id` FROM `wp_wysiwyg_file` WHERE `wp` = ".$project['id']." AND `tf_id` = ".$style.";");
      	        	if(count($row) == 0) {
    	            	$dbObject->execute("INSERT INTO `wp_wysiwyg_file`(`wp`, `tf_id`) VALUES (".$project['id'].", ".$style.");");
	  	            }
		            }
		          }
						}
						$return .= '<h4 class="success">Web project settings saved!</h4>';
					} else {
						foreach($errors as $error) {
							$return .= '<h4 class="error">'.$error.'</h4>';
						}
					}
			
					$_POST['edit'] = "Edit Project";
					$projectData = $project;
					$fromSave = true;
				} else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
			}
			
			if($_POST['edit'] == "Edit Project" || $_POST['new-project'] == "New Project") {
				if($fromSave) {
					$project = $projectData;
					$projectId = $project['id'];
				} elseif($_POST['edit'] == "Edit Project") {
					$projectId = $_POST['wp'];
					$project = $dbObject->fetchAll('SELECT `name`, `url`, `http`, `https`, `error_all_pid`, `error_404_pid`, `error_403_pid` FROM `web_project` WHERE `id` = '.$projectId.';');
					if(count($project) != 0) {
						$aliases = $dbObject->fetchAll('SELECT `url`, `http`, `https` FROM `web_alias` WHERE `project_id` = '.$projectId.';');
						$project = $project[0];
						$project['id'] = $projectId;
						$project['alias1'] = ((count($aliases) >= 1) ? $aliases[0]['url'] : '');
						$project['alias2'] = ((count($aliases) >= 2) ? $aliases[1]['url'] : '');
						$project['alias3'] = ((count($aliases) >= 3) ? $aliases[2]['url'] : '');
					} else {
						// Vypis chybu!
					}
				} else {
					$project = array('id' => 0, 'name' => '', 'url' => '', 'http' => 1, 'https' => 1, 'alias1' => '', 'alias2' => '', 'alias3' => '');
					$projectId = 0;
				}
				
				// Ziskat prava ....
				$show = array('read' => true, 'write' => true, 'delete' => false);
				$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$projectId." AND `type` = ".WEB_R_READ.";");
        $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$projectId." AND `type` = ".WEB_R_WRITE.";");
        $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$projectId." AND `type` = ".WEB_R_DELETE.";");
        $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
        $groupSelectR = '<select id="project-right-edit-groups-r-'.$project['id'].'" name="project-right-edit-groups-r[]" multiple="multiple" size="5">';
        $groupSelectW = '<select id="project-right-edit-groups-w-'.$project['id'].'" name="project-right-edit-groups-w[]" multiple="multiple" size="5">';
        $groupSelectD = '<select id="project-right-edit-groups-d-'.$project['id'].'" name="project-right-edit-groups-d[]" multiple="multiple" size="5">';
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
        
        $project['error-all-options'] = $project['error-404-options'] = $project['error-403-options'] = '<option value="0">Don\'t set</option>';
        $prjs = $dbObject->fetchAll('SELECT `web_project`.`id`, `web_project`.`name` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `id`;');
				foreach($prjs as $prj) {
					$project['error-all-options'] .= '<optgroup class="webproject" label="'.$prj['name'].'">';
					$project['error-404-options'] .= '<optgroup class="webproject" label="'.$prj['name'].'">';
					$project['error-403-options'] .= '<optgroup class="webproject" label="'.$prj['name'].'">';
					//$strProjects .= '<optgroup class="webproject" value="wp'.$project['id'].'">'.$project['name'].'</optgroup>';
					$pages = $dbObject->fetchAll('SELECT DISTINCT `page`.`id`, `info`.`name` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `page_right` ON `page`.`id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `page_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `page`.`wp` = '.$prj['id'].' ORDER BY `info`.`page_id`;');
					$i = 0;
					foreach($pages as $page) {
						$project['error-all-options'] .= '<option'.(($project['error_all_pid'] == $page['id']) ? ' selected="selected"' : '').' value="'.$page['id'].'">- '.(($page['id'] < 10) ? '000'.$page['id'] : ($page['id'] < 100) ? '00'.$page['id'] : ($page['id'] < 1000) ? '0'.$page['id'] : $page['id'] ).' - '.$page['name'].'</option>'; 
						$project['error-404-options'] .= '<option'.(($project['error_404_pid'] == $page['id']) ? ' selected="selected"' : '').' value="'.$page['id'].'">- '.(($page['id'] < 10) ? '000'.$page['id'] : ($page['id'] < 100) ? '00'.$page['id'] : ($page['id'] < 1000) ? '0'.$page['id'] : $page['id'] ).' - '.$page['name'].'</option>'; 
						$project['error-403-options'] .= '<option'.(($project['error_403_pid'] == $page['id']) ? ' selected="selected"' : '').' value="'.$page['id'].'">- '.(($page['id'] < 10) ? '000'.$page['id'] : ($page['id'] < 100) ? '00'.$page['id'] : ($page['id'] < 1000) ? '0'.$page['id'] : $page['id'] ).' - '.$page['name'].'</option>';
						//$strProjects .= '<option value="'.$page['id'].'">- '.(($page['id'] < 10) ? '0'.$page['id'] : $page['id'] ).' - '.$page['name'].'</option>';
						$i ++;
					}
					$project['error-all-options'] .= '</optgroup>';
					$project['error-404-options'] .= '</optgroup>';
					$project['error-403-options'] .= '</optgroup>';
				}
        
        // Vybrat styly pro wysiwyg!!!!
        $allStyles = $dbObject->fetchAll('SELECT `id`, `name` FROM `page_file` WHERE `wp` = '.$projectId.' AND `type` = '.WEB_TYPE_CSS.';');
        $selectedStyles = $dbObject->fetchAll('SELECT `tf_id` FROM `wp_wysiwyg_file` WHERE `wp` = '.$projectId.';');
        $styleSelect = '<select id="project-edit-styles-wysiwyg-'.$project['id'].'" name="project-edit-styles-wysiwyg[]" multiple="multiple" size="5">';
        foreach($allStyles as $style) {
        	$selected = false;
        	foreach($selectedStyles as $sStyle) {
            if($style['id'] == $sStyle['tf_id']) {
              $selected = true;
            }
          }
          $styleSelect .= '<option'.(($selected) ? ' selected="selected"' : '').' value="'.$style['id'].'">'.$style['name'].'</option>';
        }
        $styleSelect .= '</select>';
        
				// Vytvorit formular ....
				$return .= ''
				.'<form name="project-edit-detail" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<div class="project-prop">'
						.'<div class="project-edit-name">'
							.'<label for="project-edit-name'.$project['id'].'">Name:</label> '
							.'<input type="text" id="project-edit-name'.$project['id'].'" name="project-name" value="'.$project['name'].'" />'
						.'</div>'
						.'<div class="project-edit-url">'
							.'<label for="project-edit-url-'.$project['id'].'">Url:</label> '
							.'<input type="text" id="project-edit-url-'.$project['id'].'" name="project-url" value="'.$project['url'].'" />'
						.'</div>'
						.'<div class="project-edit-alias1">'
							.'<label for="project-edit-alias1-'.$project['id'].'">Alias 1:</label> '
							.'<input type="text" id="project-edit-alias1-'.$project['id'].'" name="project-alias1" value="'.$project['alias1'].'" />'
						.'</div>'
						.'<div class="project-edit-alias2">'
							.'<label for="project-edit-alias2-'.$project['id'].'">Alias 2:</label> '
							.'<input type="text" id="project-edit-alias2-'.$project['id'].'" name="project-alias2" value="'.$project['alias2'].'" />'
						.'</div>'
						.'<div class="project-edit-alias3">'
							.'<label for="project-edit-alias3-'.$project['id'].'">Alias 3:</label> '
							.'<input type="text" id="project-edit-alias3-'.$project['id'].'" name="project-alias3" value="'.$project['alias3'].'" />'
						.'</div>'
						.'<div class="project-edit-error-all">'
							.'<label for="project-edit-error-all-'.$project['id'].'">All Errors:</label> '
							.'<select id="project-edit-error-all-'.$project['id'].'" name="project-errors-all">'.$project['error-all-options'].'</select>'
						.'</div>'
						.'<div class="project-edit-error-404">'
							.'<label for="project-edit-error-404-'.$project['id'].'">Error 404:</label> '
							.'<select id="project-edit-error-404-'.$project['id'].'" name="project-errors-404">'.$project['error-404-options'].'</select>'
						.'</div>'
						.'<div class="project-edit-error-403">'
							.'<label for="project-edit-error-403-'.$project['id'].'">Error 403:</label> '
							.'<select id="project-edit-error-403-'.$project['id'].'" name="project-errors-403">'.$project['error-403-options'].'</select>'
						.'</div>'
						.'<div class="project-edit-http">'
							.'<label for="project-http-'.$project['id'].'">Http</label> '
							.'<input type="checkbox" id="protocol-http-'.$project['id'].'" name="project-http"'.(($project['http'] == 1) ? ' checked="checked"' : '').' /> '
						.'</div>'
						.'<div class="project-edit-https">'
							.'<label for="project-https-'.$project['id'].'">Https</label> '
							.'<input type="checkbox" id="protocol-https-'.$project['id'].'" name="project-https"'.(($project['https'] == 1) ? ' checked="checked"' : '').' /> '
						.'</div>'
					.'</div>'
          .'<div class="project-edit-rights">'
          	.(($show['read']) ? ''
            .'<div class="project-edit-right-read">'
            	.'<label for="project-right-edit-groups-r-'.$project['id'].'">Read</label>'
              .$groupSelectR
            .'</div>'
            : '')
            .(($show['write']) ? ''
            .'<div class="project-edit-right-write">'
            	.'<label for="project-right-edit-groups-w-'.$project['id'].'">Write</label>'
            	.$groupSelectW
          	.'</div>'
          	: '')
          	.(($show['delete']) ? ''
          	.'<div class="project-edit-right-delete">'
            	.'<label for="project-right-edit-groups-d-'.$project['id'].'">Delete</label>'
            	.$groupSelectD
            .'</div>'
            : '')
          	.'<div class="clear"></div>'
          	.'<div class="project-edit-styles-wysiwyg">'
          		.'<label for="project-edit-styles-wysiwyg-'.$project['id'].'">Styles for wysiwyg:</label> '
          		.$styleSelect
          	.'</div>'
          .'</div>'
          .'<div class="clear"></div>'
          .'<hr />'
          .'<div class="project-edit-submit">'
          	.'<input type="hidden" name="project-id" value="'.$project['id'].'" />'
          	.'<input type="submit" name="save-project" value="Save" />'
          .'</div>'
				.'</from>';
				
			} else {
				if($showNotSelectedError != 'false') {
					$return .= '<h4 class="error">Please return to previous page and select project to edit.</h4>';
				}
			}
			
			return parent::getFrame('Edit Web project', $return, '', true);
		}
		
		public function setSelectedProject($selProject) {
			$_SESSION['selected-project'] = $selProject;
			return $selProject;
		}
		
		public function getSelectedProject() {
			return $_SESSION['selected-project'];
		}
  }

?>
