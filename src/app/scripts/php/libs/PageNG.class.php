<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");  

	/**
	 * 
	 *  Class updating web pages. Next Generation    
	 *      
	 *  @author     Marek SMM
	 *  @timestamp  2009-10-28
	 * 
	 */  
	class PageNG extends BaseTagLib {

		public function __construct() {
			$this->setLocalizationBundle("pageng");
			
			if($_GET['clear'] == 'session') {
				unset($_SESSION['pageng']);
				unset($_GET['clear']);
			}
		}

		public function selectLanguage($useFrames = false, $showMsg = false) {
			global $dbObject;
			global $webObject;
			$rb = $this->rb();
			$return = '';
			
			if($_POST['page-ng-select-language-submit'] == $rb->get('selectlang.submit')) {
				$this->setLanguage($_POST['page-ng-select-language-select']);
				$return .= '<h4 class="success">'.$rb->get('selectlang.success').'</h4>';
			}
			
			$langs = $dbObject->fetchAll('SELECT `id`, `language` FROM `language` ORDER BY `language`;');
			$return .= ''
			.'<div class="page-ng-select-language">'
				.'<form name="page-ng-select-language" action="" method="post">'
					.'<label for="page-ng-select-language-select">'.$rb->get('selectlang.label').'</label> '
					.'<select name="page-ng-select-language-select" id="page-ng-select-language-select"> ';
			foreach($langs as $lang) {
				$return .= ''
				.'<option value="'.$lang['id'].'"'.(($this->getLanguage() == $lang['id']) ? ' selected="selected"' : '').'>'.$lang['language'].'</option>';
			}	
			$return .= ''
					.'</select> '
					.'<input type="submit" name="page-ng-select-language-submit" value="'.$rb->get('selectlang.submit').'" />'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('selectlang.title'), $return, "", true);
			}
		}
			
		public function setLanguage($langId) {
			$_SESSION['pageng']['language'] = $langId;
			return $langId;
		}
		
		public function getLanguage() {
			return $_SESSION['pageng']['language'];
		}
		
		public function setPageId($pageId) {
			$_SESSION['pageng']['pageid'] = $pageId;
			return $langId;
		}
		
		public function getPageId() {
			return $_SESSION['pageng']['pageid'];
		}
		
		public function setActionPageId($pageId) {
			$_SESSION['pageng']['action']['action-field'] = $pageId;
			return $langId;
		}
		
		public function getActionPageId() {
			return $_SESSION['pageng']['action-field']['pageid'];
		}
		
		public function contentCorrectionsPre($value) {
			$value = str_replace("&", "&amp;", $value);
			$value = str_replace(">", "&gt;", $value);
			$value = str_replace("<", "&lt;", $value);
			$value = str_replace('~', '&#126', $value);
		
			return $value;
		}
		
		public function contentCorrectionsPost($value) {
			$value = str_replace('&#126', '~', $value);
			$value = str_replace('&amp;web:page', '&web:page', $value);
		
			return $value;
		}
		
		public function listPages($templateId, $rootPageId, $webProjectId, $langId, $useFrames = false, $showMsg = false) {
			global $dbObject;
			global $webObject;
			global $loginObject;
			$rb = $this->rb();
			$return = '';
			
			if ($this->getLanguage() != '' && $_SESSION['selected-project'] != '') {
				$pages = $this->getPages($rootPageId, $webProjectId, $langId);
			} else {
				$return .= '<h4 class="error">'.$rb->get('pagelist.notsetprojectandlang').'</h4>';
			}
			
			//unset($_SESSION['pageng']);
			//print_r($_SESSION['pageng']);
			
			if (count($pages > 0)) {
				$template = $this->getTemplateById($templateId);
				$unset = false;
				$oldValue = null;
				if (!array_key_exists('pageid' ,$_SESSION['pageng'])) {
					$unset = true;
				} else {
					$oldValue = $_SESSION['pageng']['pageid'];
				}
				foreach($pages as $page) {
					$this->setPageId($page['page_id']);
					$return .= $template();
				}

				if ($unset) {
					unset($_SESSION['pageng']['pageid']);
				} else {
					$_SESSION['pageng']['pageid'] = $oldValue;
				}
			} else {
				$return .= $rb->get('pagelist.nopages');
			}
			
			if ($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('pagelist.title'), $return, "", true);
			}
		}
		
		public function getPages($rootPageId, $webProject, $langId) {
			global $dbObject;
			global $loginObject;
			
			$sqlWhere = '`info`.`language_id` = '.$this->getLanguage().' AND `page`.`wp` = '.$_SESSION['selected-project'].' AND `page_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'))';
			
			if($_SESSION['pageng']['field']['name'] != '') {
				$sqlWhere .= ' AND `info`.`name` like "'.$_SESSION['pageng']['field']['name'].'%"';
			}
			if($_SESSION['pageng']['field']['pageurl']) {
				$sqlWhere .= ' AND `info`.`href` like "'.$_SESSION['pageng']['field']['pageurl'].'%"';
			}
			if($_SESSION['pageng']['field']['id']) {
				$sqlWhere .= ' AND `info`.`page_id` = '.$_SESSION['pageng']['field']['id'];
			}
			if($_SESSION['pageng']['field']['keywords']) {
				$sqlWhere .= ' AND `info`.`keywords` like "'.$_SESSION['pageng']['field']['keywords'].'%"';
			}
			if($_SESSION['pageng']['field']['timestamp']) {
				$sqlWhere .= ' AND `info`.`timestamp` < '.$_SESSION['pageng']['field']['timestamp'];
			}
			if($_SESSION['pageng']['field']['cachetime']) {
				$sqlWhere .= ' AND `info`.`cachetime` = '.$_SESSION['pageng']['field']['pageurl'];
			}
			if($_SESSION['pageng']['field']['tlstart']) {
				$sqlWhere .= ' AND `content`.`tag_lib_start` like %"'.$_SESSION['pageng']['field']['tlstart'].'%"';
			}
			if($_SESSION['pageng']['field']['tlend']) {
				$sqlWhere .= ' AND `content`.`tag_lib_end` like %"'.$_SESSION['pageng']['field']['tlend'].'%"';
			}
			if($_SESSION['pageng']['field']['head']) {
				$sqlWhere .= ' AND `content`.`head` like %"'.$_SESSION['pageng']['field']['head'].'%"';
			}
			if($_SESSION['pageng']['field']['content']) {
				$sqlWhere .= ' AND `content`.`content` like %"'.$_SESSION['pageng']['field']['content'].'%"';
			}
			if($_SESSION['pageng']['field']['inTitle']) {
				$sqlWhere .= ' AND `info`.`in_title` = '.$_SESSION['pageng']['field']['intitle'];
			}
			if($_SESSION['pageng']['field']['inmenu']) {
				$sqlWhere .= ' AND `info`.`in_menu` ='.$_SESSION['pageng']['field']['inmenu'];
			}
			if($_SESSION['pageng']['field']['isvisible']) {
				$sqlWhere .= ' AND `info`.`is_visible` = '.$_SESSION['pageng']['field']['isvisible'];
			}
			
			return $dbObject->fetchAll('SELECT DISTINCT `info`.`page_id` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` LEFT JOIN `content` ON `info`.`page_id` = `content`.`page_id` LEFT JOIN `page_right` ON `info`.`page_id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE '.$sqlWhere.' ORDER BY `info`.`page_id`;');
		}
		
		public function searchFilter($templateId, $useFrames = false, $showMsg = false) {
			global $webObject;
			global $dbObject;
			$rb = $this->rb();
			$return = '';
			
			if($_POST['search-filter-submit'] == $rb->get('searchfilter.submit')) {
				$_SESSION['pageng']['search-filter']['post'] = true;
			} elseif($_POST['search-filter-clear'] == $rb->get('searchfilter.clear')) {
				unset($_SESSION['pageng']['field']);
			}
			
			$template = $this->getTemplateById($templateId);
			$return .= $template();
			
			$return = ''
			.'<div class="search-filter">'
				.'<form name="search-filter" action="" method="post">'
					.'<div class="search-filter-fields">'
						.$return
					.'</div>'
					.'<div class="search-filter-submit">'
						.'<input type="submit" name="search-filter-submit" value="'.$rb->get('searchfilter.submit').'" /> '
						.'<input type="submit" name="search-filter-clear" value="'.$rb->get('searchfilter.clear').'" />'
					.'</div>'
				.'</form>'
			.'</div>';
			
			unset($_SESSION['pageng']['search-filter']);
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('searchfilter.title'), $return, "", true);
			}
		}
		
		public function pageDetailBeforeForm() {
			global $dbObject;
			print_r($_SESSION['pageng']);
			
			$_POST['page-id'] = $_SESSION['pageng']['action-field']['pageid'];
			$_POST['page-lang-id'] = $_SESSION['pageng']['action-field']['langid'];
			
			if($_POST['page-id'] != '') {
				$parent = $dbObject->fetchAll('SELECT `page`.`parent_id` FROM `page` WHERE `page`.`id` = '.$_POST['page-id'].';');
				$_POST['parent-id'] = $parent[0]['parent_id'];
			} else {
				$_POST['parent-id'] = $_SESSION['pageng']['action-field']['parentpageid'];
			}
			
			if(array_key_exists('pagengpageaddsub', $_SESSION['pageng']['action-field']) && $_POST['parent-id'] == 0) {
				$_POST['add-new-page'] = 'Add new page';
			} elseif(array_key_exists('pagengpageaddsub', $_SESSION['pageng']['action-field']) && $_POST['parent-id'] != 0) {
				$_POST['page-add-sub'] = 'Add sub page';
			} else {
				$_POST['page-edit'] = "Edit";
			}
			
			print_r($_POST);
		}
		
		public function pageDetailAfterForm($backPageId = false) {
			global $webObject;
			
			if(($_POST['edit-save'] == "Save and Close" || $_POST['edit-close'] == "Close") && $backPageId != false) {
				unset($_SESSION['pageng']['action-field']);
				$webObject->redirectTo($backPageId);
			}
			
			return;
		}
		
		public function pageName($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['name'] = $_POST['pageng-page-field-name'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif(array_key_exists('pageng-page-field-name', $_POST) && $_POST['search-filter-clear'] != $rb->get('searchfilter.clear')) {
				$_SESSION['pageng']['field']['name'] = $_POST['pageng-page-field-name'];
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'value' || (strtolower($type) == 'edit' && $_SESSION['pageng']['action-field']['name'] == false)) {
				$page = $dbObject->fetchAll('SELECT `name` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['name'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['name'];
			} elseif(strtolower($type) == 'edit') {
				$value = $_SESSION['pageng']['action-field']['name'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$return .= ''
				.'<div class="page-field-name">'
					.'<label for="pageng-page-field-name">'.$rb->get('pagefield.name.label').'</label> '
					.'<input type="text" name="pageng-page-field-name" id="pageng-page-field-name" value="'.$value.'" />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['name'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageId($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['id'] = $_POST['pageng-page-field-id'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-id', $_POST)) {
				$_SESSION['pageng']['field']['id'] = $_POST['pageng-page-field-id'];
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `page_id` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['page_id'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['id'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$return .= ''
				.'<div class="page-field-id">'
					.'<label for="pageng-page-field-id">'.$rb->get('pagefield.id.label').'</label> '
					.'<input type="text" name="pageng-page-field-id" id="pageng-page-field-id" value="'.$value.'" />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['page_id'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageUrl($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['pageurl'] = $_POST['pageng-page-field-pageurl'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-pageurl', $_POST)) {
				$_SESSION['pageng']['field']['pageurl'] = $_POST['pageng-page-field-pageurl'];
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `href` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['href'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['pageurl'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$return .= ''
				.'<div class="page-field-pageurl">'
					.'<label for="pageng-page-field-pageurl">'.$rb->get('pagefield.pageurl.label').'</label> '
					.'<input type="text" name="pageng-page-field-pageurl" id="pageng-page-field-pageurl" value="'.$value.'" />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['href'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageKeywords($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['keywords'] = $_POST['pageng-page-field-keywords'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-keywords', $_POST)) {
				$_SESSION['pageng']['field']['keywords'] = $_POST['pageng-page-field-keywords'];
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `keywords` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['keywords'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['keywords'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$return .= ''
				.'<div class="page-field-keywords">'
					.'<label for="pageng-page-field-keywords">'.$rb->get('pagefield.keywords.label').'</label> '
					.'<input type="text" name="pageng-page-field-keywords" id="pageng-page-field-keywords" value="'.$value.'" />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['keywords'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageTimestamp($type, $pageId = false, $langId = false, $ignore = false, $format = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['timestamp'] = $_POST['pageng-page-field-timestamp'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-timestamp', $_POST)) {
				$_SESSION['pageng']['field']['timestamp'] = $_POST['pageng-page-field-timestamp'];
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `timestamp` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['timestamp'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['timestamp'];
			}
			
			if(strtolower($type) == 'input') {
				$return .= ''
				.'<div class="page-field-timestamp">'
					.'<label for="pageng-page-field-timestamp">'.$rb->get('pagefield.timestamp.label').'</label> '
					.'<input type="text" name="pageng-page-field-timestamp" id="pageng-page-field-timestamp" value="'.$value.'" />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'edit') {
				return time();
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					if($format != false) {
						return date($format, $page[0]['timestamp']);
					} else {
						return $page[0]['timestamp'];
					}
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageCachetime($type, $pageId = false, $langId = false, $ignore = false, $verbous = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['cachetime'] = $_POST['pageng-page-field-cachetime'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-cachetime', $_POST)) {
				$_SESSION['pageng']['field']['cachetime'] = $_POST['pageng-page-field-cachetime'];
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `cachetime` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['cachetime'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['cachetime'];
			}
			
			if(strtolower($type) == 'input') {
				$return .= ''
				.'<div class="page-field-cachetime">'
					.'<label for="pageng-page-field-cachetime">'.$rb->get('pagefield.cachetime.label').'</label> '
					.'<select name="pageng-page-field-cachetime" id="pageng-page-field-cachetime">'
						.'<option value="-1"'.(($value == -1) ? 'selected="selected"' : '').'>'.$rb->get('pagefield.cachetime.dontuse').'</option>'
						.'<option value="60"'.(($value == 60) ? 'selected="selected"' : '').'>'.$rb->get('pagefield.cachetime.oneminute').'</option>'
						.'<option value="3600"'.(($value == 3600) ? 'selected="selected"' : '').'>'.$rb->get('pagefield.cachetime.onehour').'</option>'
						.'<option value="86400"'.(($value == 86400) ? 'selected="selected"' : '').'>'.$rb->get('pagefield.cachetime.oneday').'</option>'
						.'<option value="0"'.(($value == 0) ? 'selected="selected"' : '').'>'.$rb->get('pagefield.cachetime.unlimited').'</option>'
					.'</select>'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'edit') {
				return time();
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					if($verbous == "true") {
						if($page[0]['cachetime'] == -1) {
							return $rb->get('pagefield.cachetime.dontuse');
						} elseif($page[0]['cachetime'] == 60) {
							return $rb->get('pagefield.cachetime.minute');
						} elseif($page[0]['cachetime'] == 3600) {
							return $rb->get('pagefield.cachetime.onehour');
						} elseif($page[0]['cachetime'] == 86400) {
							return $rb->get('pagefield.cachetime.oneday');
						} elseif($page[0]['cachetime'] == 0) {
							return $rb->get('pagefield.cachetime.unlimited');
						}
					} else {
						return $page[0]['cachetime'];
					}
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageTagLibStart($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['tlstart'] = $_POST['pageng-page-field-tlstart'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-tlstart', $_POST)) {
				$value = $this->contentCorrectionsPost($_POST['pageng-page-field-tlstart']);
				$_SESSION['pageng']['field']['tlstart'] = $value;
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `tag_lib_start` FROM `content` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['tag_lib_start'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['name'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$value = $this->contentCorrectionsPre($value);
				$return .= ''
				.'<div class="page-field-tlstart">'
					.'<label for="pageng-page-field-tlstart">'.$rb->get('pagefield.tlstart.label').'</label> '
					.'<textarea name="pageng-page-field-tlstart" id="pageng-page-field-tlstart">'.$value.'</textarea>'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['tag_lib_start'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageTagLibEnd($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['tlend'] = $_POST['pageng-page-field-tlend'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-tlend', $_POST)) {
				$value = $this->contentCorrectionsPost($_POST['pageng-page-field-tlend']);
				$_SESSION['pageng']['field']['tlend'] = $value;
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `tag_lib_end` FROM `content` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['tag_lib_end'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['tlend'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$value = $this->contentCorrectionsPre($value);
				$return .= ''
				.'<div class="page-field-tlend">'
					.'<label for="pageng-page-field-tlend">'.$rb->get('pagefield.tlend.label').'</label> '
					.'<textarea name="pageng-page-field-tlend" id="pageng-page-field-tlend">'.$value.'</textarea>'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['tag_lib_end'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageHead($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['head'] = $_POST['pageng-page-field-head'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-head', $_POST)) {
				$value = $this->contentCorrectionsPost($_POST['pageng-page-field-head']);
				$_SESSION['pageng']['field']['head'] = $value;
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `head` FROM `content` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['head'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['head'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$value = $this->contentCorrectionsPre($value);
				$return .= ''
				.'<div class="page-field-head">'
					.'<label for="pageng-page-field-head">'.$rb->get('pagefield.head.label').'</label> '
					.'<textarea name="pageng-page-field-head" id="pageng-page-field-head">'.$value.'</textarea>'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['head'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageContent($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['action-field']['content'] = $_POST['pageng-page-field-content'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-content', $_POST)) {
				$value = $this->contentCorrectionsPost($_POST['pageng-page-field-content']);
				$_SESSION['pageng']['field']['content'] = $value;
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `content` FROM `content` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['content'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['content'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$value = $this->contentCorrectionsPre($value);
				$return .= ''
				.'<div class="page-field-content">'
					.'<label for="pageng-page-field-content">'.$rb->get('pagefield.content.label').'</label> '
					.'<textarea name="pageng-page-field-content" id="pageng-page-field-content">'.$value.'</textarea>'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['content'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageInTitle($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['field']['intitle'] = ($_POST['pageng-page-field-intitle'] == 'on' ? 1 : 0);
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-intitle', $_POST)) {
				$_SESSION['pageng']['field']['intitle'] = ($_POST['pageng-page-field-intitle'] == 'on' ? 1 : 0);
			} else {
				if($_SESSION['pageng']['search-filter']['post']) {
					$_SESSION['pageng']['field']['intitle'] = 0;
				}
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `in_title` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['in_title'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['intitle'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$return .= ''
				.'<div class="page-field-intitle">'
					.'<label for="pageng-page-field-intitle">'.$rb->get('pagefield.intitle.label').'</label> '
					.'<input type="checkbox" name="pageng-page-field-intitle" id="pageng-page-field-intitle"'.(($value == 1) ? 'checked="checked" ' : '').' />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['in_title'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageInMenu($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['field']['inmenu'] = ($_POST['pageng-page-field-inmenu'] == 'on' ? 1 : 0);
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-inmenu', $_POST)) {
				$_SESSION['pageng']['field']['inmenu'] = ($_POST['pageng-page-field-inmenu'] == 'on' ? 1 : 0);
			} else {
				if($_SESSION['pageng']['search-filter']['post']) {
					$_SESSION['pageng']['field']['inmenu'] = 0;
				}
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `in_menu` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['in_menu'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['inmenu'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$return .= ''
				.'<div class="page-field-inmenu">'
					.'<label for="pageng-page-field-inmenu">'.$rb->get('pagefield.inmenu.label').'</label> '
					.'<input type="checkbox" name="pageng-page-field-inmenu" id="pageng-page-field-inmenu"'.(($value == 1) ? 'checked="checked" ' : '').' />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['in_menu'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageIsVisible($type, $pageId = false, $langId = false, $ignore = false) {
			global $dbObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['field']['isvisible'] = ($_POST['pageng-page-field-isvisible'] == 'on' ? 1 : 0);
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-isvisible', $_POST)) {
				$_SESSION['pageng']['field']['isvisible'] = ($_POST['pageng-page-field-isvisible'] == 'on' ? 1 : 0);
			} else {
				if($_SESSION['pageng']['search-filter']['post']) {
					$_SESSION['pageng']['field']['isvisible'] = 0;
				}
			}
			
			$value = '';
			$page = array();
			if(strtolower($type) == 'edit' || strtolower($type) == 'value') {
				$page = $dbObject->fetchAll('SELECT `is_visible` FROM `info` WHERE `page_id` = '.$pageId.' AND `language_id` = '.$langId.';');
				if(count($page) == 1) {
					$value = $page[0]['is_visible'];
				}
			} elseif($type == 'input') {
				$value = ($ignore == "true") ? "" : $_SESSION['pageng']['field']['isvisible'];
			}
			
			if(strtolower($type) == 'input' || strtolower($type) == 'edit') {
				$return .= ''
				.'<div class="page-field-isvisible">'
					.'<label for="pageng-page-field-isvisible">'.$rb->get('pagefield.isvisible.label').'</label> '
					.'<input type="checkbox" name="pageng-page-field-isvisible" id="pageng-page-field-isvisible"'.(($value == 1) ? 'checked="checked" ' : '').' />'
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				if(count($page) == 1) {
					return $page[0]['is_visible'];
				} else {
					return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageRightRead($type, $pageId = false, $langId = false, $verbous = false) {
			global $dbObject;
			global $loginObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['field']['rightread'] = $_POST['pageng-page-field-rightread'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-rightread', $_POST)) {
				$_SESSION['pageng']['field']['rightread'] = $_POST['pageng-page-field-rightread'];
			}
			
			if($pageId == false) {
		$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$_SESSION['selected-project']." AND `type` = ".WEB_R_READ.";");
			} else {
			$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_READ.";");
			}
			
		$groups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
			$show = true;
			if(strtolower($type) == 'edit') {
				$groupSelect = '<select id="pageng-page-field-rightread" name="pageng-page-field-rightread[]" multiple="multiple" size="5">';
		foreach($groups as $group) {
			$selectedR = false;
			foreach($groupsR as $gp) {
			if($gp['gid'] == $group['gid']) {
				$selectedR = true;
				$show = true;
			}
			}
			$groupSelect .= '<option'.(($selectedR) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
		}
		$groupSelect .= '</select>';
				
				$return = ''
				.'<div class="page-field-rightread">'
					.(($show) ? ''
					.'<label for="pageng-page-field-rightread">'.$rb->get('pagefield.rightread.label').'</label> '
					.$groupSelect
					: '')
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				$groupSelect = '';
		foreach($groupsR as $group) {
			$show = true;
			$name = '';
			foreach($groups as $gp) {
			if($gp['gid'] == $group['gid']) {
				$name = $gp['name'];
				$show = true;
			}
			}
			if($groupSelect == '') {
						$groupSelect = $name;
					} else {
						$groupSelect .= ', '.$name;
					}
		}
		
		if($verbous == "true") {
					return (($show) ? $groupSelect : '');
				} else {
					if($show) {
						return $groupsR;
					}
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageRightWrite($type, $pageId = false, $langId = false, $verbous = false) {
			global $dbObject;
			global $loginObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['field']['rightwrite'] = $_POST['pageng-page-field-rightwrite'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-rightwrite', $_POST)) {
				
				$_SESSION['pageng']['field']['rightwrite'] = $_POST['pageng-page-field-rightwrite'];
			}
			
			if($pageId == false) {
		$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$_SESSION['selected-project']." AND `type` = ".WEB_R_WRITE.";");
			} else {
			$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_WRITE.";");
			}
			
		$groups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
			$show = true;
			if(strtolower($type) == 'edit') {
				$groupSelect = '<select id="pageng-page-field-rightwrite" name="pageng-page-field-rightwrite[]" multiple="multiple" size="5">';
		foreach($groups as $group) {
			$selectedR = false;
			foreach($groupsR as $gp) {
			if($gp['gid'] == $group['gid']) {
				$selectedR = true;
				$show = true;
			}
			}
			$groupSelect .= '<option'.(($selectedR) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
		}
		$groupSelect .= '</select>';
				
				$return = ''
				.'<div class="page-field-rightwrite">'
					.(($show) ? ''
					.'<label for="pageng-page-field-rightwrite">'.$rb->get('pagefield.rightwrite.label').'</label> '
					.$groupSelect
					: '')
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				$groupSelect = '';
		foreach($groupsR as $group) {
			$show = true;
			$name = '';
			foreach($groups as $gp) {
			if($gp['gid'] == $group['gid']) {
				$name = $gp['name'];
				$show = true;
			}
			}
			if($groupSelect == '') {
						$groupSelect = $name;
					} else {
						$groupSelect .= ', '.$name;
					}
		}
				
				if($verbous == "true") {
					return (($show) ? $groupSelect : '');
				} else {
					if($show) {
						return $groupsR;
					}
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageRightDelete($type, $pageId = false, $langId = false, $verbous = false) {
			global $dbObject;
			global $loginObject;
			$rb = $this->rb();
			
			if($_POST['page-detail-form-save'] == $rb->get('pagedetail.save') || $_POST['page-detail-form-save-and-close'] == $rb->get('pagedetail.saveandclose')) {
				$_SESSION['pageng']['field']['rightdelete'] = $_POST['pageng-page-field-rightdelete'];
			} elseif($_POST['page-detail-form-close'] == $rb->get('pagedetail.close')) {
				
			} elseif($_SESSION['pageng']['search-filter']['post'] == true && array_key_exists('pageng-page-field-rightdelete', $_POST)) {
				
				$_SESSION['pageng']['field']['rightdelete'] = $_POST['pageng-page-field-rightdelete'];
			}
			
			if($pageId == false) {
		$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = ".$_SESSION['selected-project']." AND `type` = ".WEB_R_DELETE.";");
			} else {
			$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = ".$pageId." AND `type` = ".WEB_R_DELETE.";");
			}
			
		$groups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
			$show = false;
			if(strtolower($type) == 'edit') {
				$groupSelect = '<select id="pageng-page-field-rightdelete" name="pageng-page-field-rightdelete[]" multiple="multiple" size="5">';
		foreach($groups as $group) {
			$selectedR = false;
			foreach($groupsR as $gp) {
			if($gp['gid'] == $group['gid']) {
				$selectedR = true;
				$show = true;
			}
			}
			$groupSelect .= '<option'.(($selectedR) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
		}
		$groupSelect .= '</select>';
				
				$return = ''
				.'<div class="page-field-rightdelete">'
					.(($show) ? ''
					.'<label for="pageng-page-field-rightdelete">'.$rb->get('pagefield.rightdelete.label').'</label> '
					.$groupSelect
					: '')
				.'</div>';
				
				return $return;
			} elseif(strtolower($type) == 'value') {
				$groupSelect = '';
		foreach($groupsR as $group) {
			$show = true;
			$name = '';
			foreach($groups as $gp) {
			if($gp['gid'] == $group['gid']) {
				$name = $gp['name'];
				$show = true;
			}
			}
			if($groupSelect == '') {
						$groupSelect = $name;
					} else {
						$groupSelect .= ', '.$name;
					}
		}
		
		if($verbous == "true") {
					return (($show) ? $groupSelect : '');
				} else {
					if($show) {
						return $groupsR;
					}
				}
			} else {
				return '<h4 class="error">'.$rb->get('pagefield.error').'</h4>';
			}
		}
		
		public function pageActionEdit($pageId, $langId, $type, $detailPageId = false) {
			global $webObject;
			$rb = $this->rb();
			$return = '';
			
			if($_POST['pagengpageedit'] == $rb->get('pageaction.pageedit')) {
				$_SESSION['pageng']['action-field']['pageid'] = $pageId;
				$_SESSION['pageng']['action-field']['langid'] = $langId;
				if($detailPageId != false) {
					$webObject->redirectTo($detailPageId);
				}
			}
			
			$return .= ''
			.'<div class="page-action page-action-edit">'
				.'<form name="page-action-edit" action="" method="post">'
					.'<input type="hidden" name="pagengpageid" value="'.$pageId.'" />'
					.'<input type="hidden" name="pagenglangid" value="'.$langId.'" />'
					.(($type == 'image') ? ''
					.'<input type="hidden" name="pagengpageedit" value="'.$rb->get('pageaction.pageedit').'" />'
					.'<input type="image" src="~/images/page_edi.png" name="pagengpageedit" value="'.$rb->get('pageaction.pageedit').'" title="'.$rb->get('pageaction.pageeditlabel').'" />'
					: ''
					.'<input type="submit" name="pagengpageedit" value="'.$rb->get('pageaction.pageedit').'" title="'.$rb->get('pageaction.pageeditlabel').'" />'
					)
				.'</form>'
			.'</div>';
			
			return $return;
		}
		
		public function pageActionDelete($pageId, $langId, $type) {
			$rb = $this->rb();
			$return = '';
			
			if($_POST['pagengpagedelete'] == $rb->get('pageaction.pagedelete')) {
				// SQL DELETE ...
			}
			
			$return .= ''
			.'<div class="page-action page-action-delete">'
				.'<form name="page-action-delete" action="" method="post">'
					.'<input type="hidden" name="pagengpageid" value="'.$pageId.'" />'
					.'<input type="hidden" name="pagenglangid" value="'.$langId.'" />'
					.(($type == 'image') ? ''
					.'<input type="hidden" name="pagengpagedelete" value="'.$rb->get('pageaction.pagedelete').'" />'
					.'<input type="image" src="~/images/page_del.png" name="pagengpagedelete" value="'.$rb->get('pageaction.pagedelete').'" title="'.$rb->get('pageaction.pagedeletelabel').'" />'
					: ''
					.'<input class="confirm" type="submit" name="pagengpagedelete" value="'.$rb->get('pageaction.pagedelete').'" title="'.$rb->get('pageaction.pagedeletelabel').'" />'
					)
				.'</form>'
			.'</div>';
			
			return $return;
		}
		
		public function pageActionAddsub($pageId, $langId, $type, $detailPageId = false) {
			global $webObject;
			$rb = $this->rb();
			$return = '';
			
			if($_POST['pagengpageaddsub'] == $rb->get('pageaction.pageaddsub')) {
				$_SESSION['pageng']['action-field']['parentpageid'] = $_POST['pagengpageid'];
				$_SESSION['pageng']['action-field']['langid'] = $_POST['pagenglangid'];
				$_SESSION['pageng']['action-field']['pagengpageaddsub'] = true;
				if($detailPageId != false) {
					$webObject->redirectTo($detailPageId);
				}
			}
			
			$return .= ''
			.'<div class="page-action page-action-addsub">'
				.'<form name="page-action-addsub" action="" method="post">'
					.'<input type="hidden" name="pagengpageid" value="'.$pageId.'" />'
					.'<input type="hidden" name="pagenglangid" value="'.$langId.'" />'
					.(($type == 'image') ? ''
					.'<input type="hidden" name="pagengpageaddsub" value="'.$rb->get('pageaction.pageaddsub').'" />'
					.'<input type="image" src="~/images/page_add.png" name="pagengpagedel" value="'.$rb->get('pageaction.pageaddsub').'" title="'.$rb->get('pageaction.pageaddsublabel').'" />'
					: ''
					.'<input type="submit" name="pagengpageaddsub" value="'.$rb->get('pageaction.pageaddsub').'" title="'.$rb->get('pageaction.pageaddsublabel').'" />'
					)
				.'</form>'
			.'</div>';
			
			return $return;
		}
		
	}

?>
