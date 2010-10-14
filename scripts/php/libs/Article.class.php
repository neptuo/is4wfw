<?php

	/**
	 *
	 *  Require base tag lib class.
	 *
	 */
	require_once("BaseTagLib.class.php");
	require_once("scripts/php/classes/CustomTagParser.class.php");
	require_once('System.class.php');
  
	/**
	 * 
	 *  Article class
	 *      
	 *  @author     Marek SMM
	 *  @timestamp  2010-10-14
	 * 
	 */  
	class Article extends BaseTagLib {
  
		/**
		 *
		 *  Article id for dynamic address.     
		 *
		 */              
		private $CurrentId = 0;
  
		private $BundleName = 'article';
  	
		private $BundleLang = 'cs';
  
		public function __construct() {
			global $dbObject;
			global $webObject;
			parent::setTagLibXml("xml/Article.xml");
			
			if($webObject->LanguageName != '') {
				$rb = new ResourceBundle();
				if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
					$this->BundleLang = $webObject->LanguageName;
				}
			}
		}
    
		/**
		 * 
		 *  Shows articles from line.
		 *  C tag.
		 *  
		 *  @param    	lineId    		line id
		 *  @param    	template  		template for displaying (deprecated)
		 *  @param    	templateId  	template for displaying (using dynamic templates from cms)
		 *  @param    	pageId    		page id for paeg with detail
		 *  @param		pageLangId		language id     
		 *  @param		articleLangId	language id     
		 *  @param    	method    		method for passing detail id
		 *  @param		sort			asc (default) / desc
		 *  @return   	list of articles
		 *
		 */                   
		public function showLine($lineId = false, $template = fales, $templateId = false, $pageId = false, $pageLangId = false, $articleLangId = false, $method = false, $sort = false, $noDataMessage = false) {
			global $webObject;
			global $dbObject;
			global $loginObject;
			$articleLangId = ($articleLangId != false) ? $articleLangId : $webObject->LanguageId;
			$pageLangId = ($pageLangId != false) ? $pageLangId : $webObject->LanguageId;
			$return = '';
			$detail = false;
			$link = "";
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			if($lineId == '') {
				if(parent::request()->exists('line-url')) {
					$lineId = parent::request()->get('line-url');
				} else {
					parent::getError('lines.notselected');
				}
			}
	  
			if($pageId != false) {
				$detail = true;
				$link = $webObject->composeUrl($pageId, $pageLangId);
			}
			
			$lineInfo = parent::db()->fetchSingle('select `name`, `url` from `article_line` where `id` = '.$lineId.';');
      
			$templateContent = '';
			if($templateId != false) {
				// ziskani templatu ...
				$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
				if(count($rights) > 0 && $templateId > 0) {
					$template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
					$templateContent = $template[0]['content'];
				} else {
					$message = "Permission Denied when reading template[templateId = ".$templateId."]!";
					trigger_error($message, E_USER_WARNING);
					return;
				}
			} elseif($template != false) {
				if(is_file($template) && is_readable($template)) {
					$templateContent = file_get_contents($template);
				} else {
					$message = "Template file doesn't exist or is un-readable!";
					trigger_error($message, E_USER_WARNING);
					return;
				}
			} else {
				$message = "Template or TemplateId must be set!";
				trigger_error($message, E_USER_WARNING);
				return;
			}
        
			$sort = (strtolower($sort) == 'desc' ? 'DESC' : 'ASC'); 
			$articles = $dbObject->fetchAll("SELECT `article`.`id`, `article_content`.`name`, `article_content`.`url`, `article_content`.`head`, `article_content`.`content`, `article_content`.`author`, `article_content`.`timestamp` FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` LEFT JOIN `article_line_right` ON `article`.`line_id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article`.`line_id` = ".$lineId." AND `article_content`.`language_id` = ".$articleLangId." AND `article_line_right`.`type` = ".WEB_R_READ." AND (`group`.`gid` IN (".$loginObject->getGroupsIdsAsString().") OR `group`.`parent_gid` IN (".$loginObject->getGroupsIdsAsString().")) ORDER BY `article_content`.`timestamp` " . $sort . ";");
			if(count($articles) > 0) {
				$flink = '';
				parent::request()->set('line-url', $lineInfo['url']);
				foreach($articles as $article) {
					parent::request()->set('id', $article['id'], 'current-article');
					parent::request()->set('date', $article['timestamp'], 'current-article');
					parent::request()->set('time', $article['timestamp'], 'current-article');
					parent::request()->set('name', $article['name'], 'current-article');
					parent::request()->set('author', $article['author'], 'current-article');
					parent::request()->set('head', $article['head'], 'current-article');
					parent::request()->set('content', $article['content'], 'current-article');
          
					self::setUrl($article['url']);
					if($detail) {
						if($method == "static") {
							$flink = $link.'?article-id='.$article['id'];
						} elseif($method == "dynamic") {
							$flink = $link.'/'.$article['id'];
						}
					}
					$_SESSION['current-article']['link'] = $flink;
          
					$Parser = new CustomTagParser();
					$Parser->setContent($templateContent);
					$Parser->startParsing();
					$return .= $Parser->getResult();
				}
        
				unset($_SESSION['article-id']);
				unset($_SESSION['current-article']);
			} else {
				if($noDataMessage != '') {
					$return .= $noDataMessage;
				} else {
					$return .= parent::getError($rb->get('articles.noarticles'));
				}
			}
        
			return $return;
		}
    
		/**
		 *
		 *	Show article line as RSS document.
		 *	C tag
		 *	
		 *	@param		lineId				article line id
		 *	@param		pageId				page id to show detail     
		 *  @param		articleLangId		language id
		 *  @param    	method    			method for passing detail id
		 *	@return		RSS document		 		 		      
		 *
		 */		 		 		 		     
		public function showRssLine($lineId, $pageId = false, $articleLangId = false, $pageLangId = false, $method = false) {
			global $webObject;
			global $dbObject;
			$return = '';
			$detail = false;
			
			$articleLangId = ($articleLangId != false) ? $articleLangId : $webObject->LanguageId;
			$pageLangId = ($pageLangId != false) ? $pageLangId : $webObject->LanguageId;
      
			if($pageId != false) {
				$detail = true;
				$link = $webObject->composeUrl($pageId, $pageLangId, true);
			}
      
			$lineName = $dbObject->fetchAll('SELECT `name` FROM `article_line` WHERE `id` = '.$lineId.';');
			$langName = $dbObject->fetchAll('SELECT `language` FROM `language` WHERE `id` = '.$articleLangId.';');
			
			// prava na cteni???
			$articles = $dbObject->fetchAll('SELECT `id`, `name`, `head`, `timestamp` FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` WHERE `article`.`line_id` = '.$lineId.' AND `article_content`.`language_id` = '.$articleLangId.';');
			$items = '';
			foreach($articles as $article) {
				$flink = '';
				if($detail) {
					if($method == "static") {
						$flink = $link.'?article-id='.$article['id'];
					} elseif($method == "dynamic") {
						$flink = $link.'/'.$article['id'];
					}
				}
        
				$items .= ''
				.'<item>'
					.'<title>'.$article['name'].'</title>'
					.'<link>'.$flink.'</link>'
					.'<description>'.$article['head'].'</description>'
					.'<pubDate>'.date("d.m.Y H:i", $article['timestamp']).'</pubDate>'
					.'<guid></guid>'
				.'</item>';
			}
			
			if(count($lineName) == 1) {
				$return .= ''
				.'<rss version="2.0">'
					.'<channel>'
	    			.'<title>'.$webObject->getPageTitle().'</title>'
    				.'<link>http://'.$webObject->getHttpHost().WEB_ROOT.'</link>'
    				.'<description>'.$lineName[0]['name'].'</description>'
    				.'<language>'.$langName[0]['language'].'</language>'
    				.'<pubDate>Tue, 10 Jun 2003 04:00:00 GMT</pubDate>'
					.'<lastBuildDate>Tue, 10 Jun 2003 09:41:01 GMT</lastBuildDate>'
	    			.'<docs>http://'.$webObject->getCurrentRequestPath().'</docs>'
    				.'<generator>WFW RSSMM - RSS Generator 1.0</generator>'
    				.'<managingEditor>editor@example.com</managingEditor>'
    				.'<webMaster>webmaster@papayateam.cz</webMaster>'
    				.$items
  				.'</channel>'
				.'</rss>';
				
				echo $return;
				exit;
			}
		}
    
		/**
		 *
		 *  Displaies article detail.
		 *  C tag.
		 *  
		 *  @param    	template  		template for displaying (deprecated)
		 *  @param    	templateId  	template for displaying (using dynamic templates from cms)
		 *  @param   	articleId   	article id
		 *  @param		articleLangId	language id
		 *  @param		showError		show error in output   
		 *  @return   	article id in template
		 *
		 */                        
		public function showDetail($template = false, $templateId = false, $articleId = false, $articleLangId = false, $defaultArticleId = false, $showError = false, $lineId = false) {
			global $webObject;
			global $dbObject;
			global $loginObject;
			$langId = $webObject->LanguageId;
			$return = '';
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			$articleLangId = ($articleLangId != false) ? $articleLangId : $webObject->LanguageId;
    
			if($articleId == false) {
				if(array_key_exists('article-id', $_REQUEST)) {
					$articleId = $_REQUEST['article-id'];
				} elseif(array_key_exists('article-id', $_SESSION)) {
					$articleId = $_SESSION['article-id'];
				} elseif($defaultArticleId != false) {
					$articleId = $defaultArticleId;
				} elseif($this->CurrentId != 0) {
					$articleId = $this->CurrentId;
				} elseif(self::getUrl() != '') { 
					$url = self::getUrl();
					$sql = 'select `article_id` from `article_content` left join `article` on `article_content`.`article_id` = `article`.`id` where `url` = "'.$url.'"'.($lineId != '' && is_numeric($lineId) ? ' and `line_id` = '.$lineId : '').';';
					$arid = parent::db()->fetchAll($sql);
					if(count($arid) == 1) {
						$articleId = $arid[0]['article_id'];
					} else {
						if($showError != 'false') {
							$message = 'Missing argument [article-id]!';
							echo '<h4 class="error">'.$message.'</h4>';
							trigger_error($messgae, E_USER_WARNING);
						}
						return;
					}
				} else {
					if($showError != 'false') {
						$message = 'Missing argument [article-id]!';
						echo '<h4 class="error">'.$message.'</h4>';
						trigger_error($messgae, E_USER_WARNING);
					}
					return;
				}
			}
      
			$templateContent = '';
			if($templateId != false) {
				// ziskani templatu ...
				$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
				if(count($rights) > 0 && $templateId > 0) {
					$template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
					$templateContent = $template[0]['content'];
				} else {
					$message = "Permission Denied when reading template[templateId = ".$templateId."]!";
					trigger_error($message, E_USER_WARNING);
					return;
				}
			} elseif($template != false) {
				if(is_file($template) && is_readable($template)) {
					$templateContent = file_get_contents($template);
				} else {
					$message = "Template file doesn't exist or is un-readable!";
					trigger_error($message, E_USER_WARNING);
					return;
				}
			} else {
				$message = "Template or TemplateId must be set!";
				trigger_error($message, E_USER_WARNING);
				return;
			}
      
			$article = $dbObject->fetchAll("SELECT `name`, `head`, `content`, `author`, `timestamp` FROM `article_content` WHERE `article_id` = ".$articleId." AND `language_id` = ".$articleLangId.";");
			if(count($article) == 1) {
				parent::request()->set('id', $articleId, 'current-article');
				parent::request()->set('date', $article[0]['timestamp'], 'current-article');
				parent::request()->set('time', $article[0]['timestamp'], 'current-article');
				parent::request()->set('name', $article[0]['name'], 'current-article');
				parent::request()->set('author', $article[0]['author'], 'current-article');
				parent::request()->set('head', $article[0]['head'], 'current-article');
				parent::request()->set('content', $article[0]['content'], 'current-article');
        
				$Parser = new CustomTagParser();
				$Parser->setContent($templateContent);
				$Parser->startParsing();
 				$return .= $Parser->getResult();
			} else {
				$return .= '<div class="no-article">'.$rb->get('articles.notselected').'</div>';
			}
			return $return;
		}
    
		/**
		 *
		 *  Dynamicly rewrite address.
		 *  C tag.
		 *  
		 *  return    if article exists, it returns CurrentDynamicPath     
		 *
		 */                        
		public function composeUrl() {
			global $webObject;
			global $phpObject;
			global $dbObject;
			$cdp = $webObject->getCurrentDynamicPath();
      
			$id = $phpObject->str_tr($cdp, "-", 1);
      
			$file = $dbObject->fetchAll("SELECT `name` FROM `article_content` WHERE `article_id` = ".$id[0]." AND `language_id` = ".$webObject->LanguageId.";");
			if(count($file) == 1 && $cdp == $id[0]) {
				$this->CurrentId = $id[0];
				$_SESSION['article']['current_id'] = $id[0];
				return $cdp;
			} else {
				return 'false.false';
			}
		}
    
		/**
		 *
		 *  Article Management.
		 *  C tag.
		 *  
		 *  @param    lineId    			line id
		 *  @param		detailPafeId    page id for next articles.selectline
		 *  @param		method					method of passing arguments
		 *  @param		useFrames				use frames in output		      
		 *  @return   complete article management                    
		 *
		 */                   
		public function showManagement($lineId = false, $detailPageId = false, $method = false, $useFrames = false) {
			global $dbObject;
			global $loginObject;
			global $webObject;
			$return = '';
			$actionUrl = $_SERVER['REDIRECT_URL'];
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
			
			if($lineId == false) {
				if($method == "get" || $method == "post") {
					$lineId = $_REQUEST['line-id'];
				} elseif($method == "session") {
					$lineId = $_SESSION['article-line-id'];
				} else {
					return parent::getFrame('Message', '<h4 class="error">'.$rb->get('articles.nolineselected').'</h4>', '');
				}
			}
      
			// test na prava zapisu do rady clanku
			$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
			if(count($permission) > 0) {
				$ok = true;
			} else {
				$return .= '<h4 class="error">'.$rb->get('articles.selectline').'</h4>';
				if($useFrames != "false") {
					return parent::getFrame($rb->get('articles.inlinetitle'), $return, '');
				} else {
					return $return;
				}
			}
			
			if($_POST['article-delete'] == $rb->get('articles.delete')) {
				$artcId = $_POST['article-id'];
        
				$dbObject->execute("DELETE FROM `article_content` WHERE `article_id` = ".$artcId.";");
				$dbObject->execute("DELETE FROM `article` WHERE `id` = ".$artcId.";");
			} elseif($_POST['article-delete-lang'] == "Delete Lang") {
				$artcId = $_POST['article-id'];
				$langId = $_POST['language-id'];
        
				$dbObject->execute("DELETE FROM `article_content` WHERE `article_id` = ".$artcId." AND `language_id` = ".$langId.";");
				$artcs = $dbObject->fetchAll("SELECT `article_id` FROM `article_content` WHERE `article_id` = ".$artcId.";");
				if(count($artcs) == 0) {
					$dbObject->execute("DELETE FROM `article` WHERE `id` = ".$artcId.";");
				}
			}
      
			$articles = $dbObject->fetchAll("SELECT `id` FROM `article` WHERE `line_id` = ".$lineId.";");
			if(count($articles) > 0) {
				$returnTmp .= ''
				.'<div class="article-mgm-show">'
					.'<table class="article-mgm-table">'
						.'<thead>'
							.'<tr class="article-mgm-tr article-mgm-tr-head">'
								.'<th class="article-mgm-th article-mgm-id">'.$rb->get('articles.id').':</th>'
								.'<th class="article-mgm-th article-mgm-lang">'.$rb->get('articles.lang').':</th>'
								.'<th class="article-mgm-th article-mgm-head">'.$rb->get('articles.head').'</th>'
								.'<th class="article-mgm-th article-mgm-edit">'.$rb->get('articles.action').'</th>'
							.'</tr>'
						.'</thead>'
						.'<tbody>';
				foreach($articles as $article) {
					$infos = $dbObject->fetchAll("SELECT `article_content`.`name`, `article_content`.`head`, `language`.`id` AS `lang_id`, `language`.`language` FROM `article_content` LEFT JOIN `language` ON `article_content`.`language_id` = `language`.`id` WHERE `article_content`.`article_id` = ".$article['id']." ORDER BY `language`.`language`;");
					$lnVersions = count($infos);
					$first = true;
					foreach($infos as $info) {
						$returnTmp .= ''
						.'<tr class="article-mgm-tr'.(($first) ? ' article-mgm-first' : '').'">'
						.(($first) ? ''
							.'<td rowspan="'.$lnVersions.'" class="article-mgm-td article-mgm-id">'
								.'<span>'.$article['id'].'</span>'
								.'<form name="article-add-lang1" method="post" action="'.$actionUrl.'">'
									.'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
									.'<input type="hidden" name="line-id" value="'.$lineId.'" />'
									.'<input type="hidden" name="article-add-lang" value="'.$rb->get('articles.addlang').'" />'
									.'<input type="image" src="~/images/lang_add.png" name="article-add-lang" value="'.$rb->get('articles.addlang').'" title="'.$rb->get('articles.addlangcap').'" />'
								.'</form>'
								.'<form name="article-add-lang2" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
									.'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
									.'<input type="hidden" name="line-id" value="'.$lineId.'" />'
									.'<input type="hidden" name="article-delete" value="'.$rb->get('articles.delete').'" />'
									.'<input type="image" src="~/images/page_del.png" class="confirm" name="article-delete" value="'.$rb->get('articles.delete').'" title="'.$rb->get('articles.deletecap').', id('.$article['id'].')" >'
								.'</form>'
							.'</td>'
						: '')
							.'<td class="article-mgm-td article-mgm-lang">'
								.$info['language']
							.'</td>'
							.'<td class="article-mgm-td article-mgm-head">'
								.'<div class="article-head-cover">'
									.'<span class="article-head-in">'
										.htmlspecialchars($info['name'].' - '.$info['head'])
									.'</span>'
								.'</div>'
							.'</td>'
							.'<td class="article-mgm-td article-mgm-edit">'
								.'<form name="article-edit" method="post" action="'.$actionUrl.'">'
									.'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
									.'<input type="hidden" name="language-id" value="'.$info['lang_id'].'" />'
									.'<input type="hidden" name="line-id" value="'.$lineId.'" />'
									.'<input type="hidden" name="article-edit" value="'.$rb->get('articles.edit').'" />'
									.'<input type="image" src="~/images/page_edi.png" name="article-edit" value="'.$rb->get('articles.edit').'" title="'.$rb->get('articles.editcap').'" /> '
								.'</form>'
								.'<form name="article-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
									.'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
									.'<input type="hidden" name="language-id" value="'.$info['lang_id'].'" />'
									.'<input type="hidden" name="line-id" value="'.$lineId.'" />'
									.'<input type="hidden" name="article-delete-lang" value="'.$rb->get('articles.deletelang').'" />'
									.'<input type="image" src="~/images/lang_del.png" class="confirm" name="article-delete-lang" value="'.$rb->get('articles.deletelang').'" title="'.$rb->get('articles.deletelangcap').', id('.$article['id'].')" />'
								.'</form>'
							.'</td>'
						.'</tr>';
						$first = false;
					}
				}
				$returnTmp .= ''
						.'</tbody>'
					.'</table>'
				.'</div>';
			} else {
				$returnTmp .= '<div class="no-articles"><h4 class="error">'.$rb->get('articled.noinline').'</h4></div>';
			}
		
			if($useFrames != "false") {
				return parent::getFrame($rb->get('articles.inlinetitle'), $returnTmp, '');
			} else {
				return $return;
			}
		}
    
		/**
		 *
		 *	Create article form
		 *	C tag
		 *	
		 *	@param	lineId				article line id		 		      
		 *	@param	detailPageId	page id for next page
		 *	@param	method				method of passing arguments
		 *	@param	useFrames			use frames in output
		 *	@param	showError			show errors in output
		 *	@return	form for redirect to page with edit article 
		 *
		 */		 		 		 		 		     
		public function createArticle($lineId = false, $detailPageId = false, $method = false, $useFrames = false, $showError = false) {
			global $dbObject;
			global $loginObject;
			global $webObject;
			$return = '';
			$actionUrl = $_SERVER['REDIRECT_URL'];
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			
			if($lineId == false) {
				if($method == "get" || $method == "post") {
					$lineId = $_REQUEST['line-id'];
				} elseif($method == "session") {
					$lineId = $_SESSION['article-line-id'];
				} else {
					if($useFrames != 'false') {
						return parent::getFrame('Message', '<h4 class="error">'.$rb->get('articles.nolineselected').'</h4>', '');
					} else {
						return '<h4 class="error">'.$rb->get('articles.nolineselected').'</h4>';
					}
				}
			}
      
			if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
			
			$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND `article_line_right`.`line_id` = '.$lineId.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;;');
			if(count($permission) > 0) {
				$return .= ''
				.'<div class="article-new gray-box">'
					.'<form name="article-new" method="post" action="'.$actionUrl.'">'
						.'<input type="submit" name="article-new" value="'.$rb->get('articles.newcap').'" />'
					.'</form>'
				.'</div>';
			} else {
				if($showError != 'false') {
					$return .= '<h4 class="error">'.$rb->get('articles.selectline').'</h4>';
				}
			}
    
			if($useFrames != "false") {
				return parent::getFrame($rb->get('articles.newtitle'), $return, "");
			} else {
				return $return;
			}
		}
    
		/**
		 *
		 *  Generates form for editing article.
		 *  
		 *  @param    article         array as db table 'article'
		 *  @param    articleContent  array as db table 'article_content'
		 *  @return   form for editing article
		 *
		 *	DEPRECATED!!
		 *
		 */                        
		private function editArticleForm($article, $articleContent) {
			global $dbObject;
			$return = '';
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			$usedLangs = $dbObject->fetchAll("SELECT `language_id` FROM `article_content` WHERE `article_id` = ".$article['id'].";");
			$langs = $dbObject->fetchAll("SELECT `id`, `language` FROM `language` ORDER BY `language`;");
			$langSelect = '<select name="article-lang-id">';
			foreach($langs as $lang) {
				$ok = true;
				foreach($usedLangs as $usedLang) {
					if(in_array($lang['id'], $usedLang)) {
						$ok = false;
					}
					if(($lang['id'] == $articleContent['language_id'])) {
						$ok = true;
					}
				}
				if($ok) {
					$langSelect .= '<option value="'.$lang['id'].'"'.(($lang['id'] == $articleContent['language_id']) ? ' selected="selected"' : '').'>'.$lang['language'].'</option>';
				}
			}
			$langSelect .= '</select>';
      
			$lines = $dbObject->fetchAll("SELECT `id`, `name` FROM `article_line`;");
			$lineSelect = '<select name="line-id">';
			foreach($lines as $line) {
				$lineSelect .= '<option value="'.$line['id'].'"'.(($line['id'] == $article['line_id']) ? ' selected="selected"' : '').'>'.$line['name'].'</option>';
			}
			$lineSelect .= '</select>';
      
			$name = 'Article.editors';
			$system = new System();
			$propertyEditors = $system->getPropertyValue($name);
			$editAreaContentRows = $system->getPropertyValue('Article.editAreaContentRows');
			$editAreaHeadRows = $system->getPropertyValue('Article.editAreaHeadRows');
      
			$return .= ''
			.'<div class="article-mgm-edit">'
				.'<form name="article-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<div class="article-prop">'
						.'<div class="article-name">'
							.'<label for="article-name">'.$rb->get('articles.name').':</label> '
							.'<input type="text" name="article-name" value="'.$articleContent['name'].'" />'
						.'</div>'
						.'<div class="article-line">'
							.'<label for="line-id">'.$rb->get('articles.lines').':</label> '
							.$lineSelect
						.'</div>'
						.'<div class="article-lang">'
							.'<label for="article-lang-id">'.$rb->get('articles.lang').':</label> '
							.$langSelect
						.'</div>'
						.'<div class="article-author">'
							.'<label for="article-author">'.$rb->get('articles.author').':</label> '
							.'<input type="text" name="article-author" value="'.$articleContent['author'].'" />'
						.'</div>'
						.'<div class="clear"></div>'
					.'</div>'
					.'<div class="gray-box">'
						.'<input type="text" class="long-input" name="article-url" value="'.$article['url'].'" />'
					.'</div>';
			if($propertyEditors == 'edit_area') {
				$return .= ''
					.'<div id="editors" class="editors edit-area-editors">'
						.'<div id="editors-tab" class="editors-tab"></div>'
						.'<div id="cover-article-head">'
							.'<label for="article-head">'.$rb->get('acticles.head2').':</label>'
							.'<textarea id="article-head" class="edit-area html" name="article-head" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 10).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $articleContent['head']))).'</textarea>'
						.'</div>'
						.'<div id="cover-article-content">'
							.'<label for="article-content">'.$rb->get('articles.content2').':</label>'
							.'<textarea id="article-content" class="edit-area html" name="article-content" rows="'.($editAreaContentRows > 0 ? $editAreaContentRows : 20).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $articleContent['content']))).'</textarea>'
						.'</div>'
					.'</div>';
			} else if($propertyEditors == 'tiny') {
				$return .= ''
					.'<div id="cover-article-head">'
						.'<label for="article-head">'.$rb->get('acticles.head2').':</label>'
						.'<textarea id="article-head" name="article-head" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 10).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $articleContent['head']))).'</textarea>'
					.'</div>'
					.'<div id="cover-article-content">'
						.'<label for="article-content">'.$rb->get('articles.content2').':</label>'
						.'<textarea id="article-content" name="article-content" rows="'.($editAreaContentRows > 0 ? $editAreaContentRows : 20).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $articleContent['content']))).'</textarea>'
					.'</div>'
					.'<script type="text/javascript">'
						.'initTiny("article-head"); initTiny("article-content");'
					.'</script>';
			} else {  
				$return .= ''
					.'<div class="article-head">'
						.'<label for="article-head">'.$rb->get('acticles.head2').':</label> '
						.'<div class="editor-cover">'
							.'<div class="textarea-cover">'
								.'<textarea name="article-head" class="editor-textarea editor-closed" rows="5">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $articleContent['head']))).'</textarea>'
							.'</div>'
							.'<div class="clear"></div>'
						.'</div>'
					.'</div>'
					.'<div class="article-content">'
						.'<label for="article-content">'.$rb->get('acticles.content2').':</label> '
						.'<div class="editor-cover">'
							.'<div class="textarea-cover">'
								.'<textarea name="article-content" class="editor-textarea editor-tiny" rows="15">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $articleContent['content']))).'</textarea>'
							.'</div>'
							.'<div class="clear"></div>'
						.'</div>'
					.'</div>';
			}
			$return .= ''
					.'<div class="article-bottom">'
						.'<div class="article-submit">'
							.'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
							.'<input type="hidden" name="line-old-id" value="'.$article['line_id'].'" />'
							.'<input type="hidden" name="article-old-lang-id" value="'.$articleContent['language_id'].'" />'
							.'<input type="submit" name="article-save" value="'.$rb->get('articles.save').'" /> '
							.'<input type="submit" name="article-save" value="'.$rb->get('articles.saveandclose').'" /> '
							.'<input type="submit" name="article-close" value="'.$rb->get('articles.close').'" />'
						.'</div>'
						.'<div class="clear"></div>'
					.'</div>'
				.'</form>'
			.'</div>';
      
			return $return;
		}
    
		/**
		 *
		 *  Show Article lines.
		 *  C tag.
		 *  
		 *  @param    editable    		edit able
		 *  @param		detailPageId	  page id for next page
		 *  @param		useFrames				use frames in output     
		 *  @return   Article lines                    
		 *
		 */
		public function showLines($editable = false, $detailPageId = false, $useFrames = false) {
			global $dbObject;
			global $webObject;
			global $loginObject;
			$return = '';
			$actionUrl = '';
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			if($_POST['article-line-delete'] == $rb->get('lines.delete')) {
				$lineId = $_POST['delete-line-id'];
				// test na prava pro delete!
				$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
				if(count($permission) > 0) {
					$pages = $dbObject->fetchAll('SELECT `id` FROM `article` WHERE `line_id` = '.$lineId.';');
					if(count($pages) == 0) {
						$dbObject->execute('DELETE FROM `article_line_right` WHERE `line_id` = '.$lineId.';');
						$dbObject->execute('DELETE FROM `article_line` WHERE `id` = '.$lineId.';');
					
						$return .= '<h4 class="success">Article line deleted!</h4>';
					} else {
						$return .= '<h4 class="error">can\'t delete article line, still exists articles in this line!</h4>';
					}
				} else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
			}
      
			if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
      
			$lines = $dbObject->fetchAll('SELECT `article_line`.`id`, `article_line`.`name`, `article_line`.`url` FROM `article_line` LEFT JOIN `article_line_right` ON `article_line`.`id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `id`;');      
			if(count($lines) > 0) {
				$return .= ''
				.'<div class="show-lines standart clickable"> '
					.'<table>'
						.'<thead>'
							.'<tr>'
								.'<th class="show-lines-id">'.$rb->get('articles.id').':</th>'
								.'<th class="show-lines-name">'.$rb->get('articles.name').':</th>'
								.'<th class="show-lines-name">'.$rb->get('articles.url').':</th>'
								.'<th class="show-lines-edit"></th>'
							.'</tr>'
						.'</thead>'
						.'<tbody>';
				$i = 1;
				foreach($lines as $line) {
					$artcs = $dbObject->fetchAll('SELECT `id` FROM `article` WHERE `line_id` = '.$line['id'].';');
					$return .= ''
							.'<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">'
								.'<td class="article-lines-id">'
									.$line['id']
								.'</td>'
								.'<td class="article-lines-name">'
									.$line['name']
								.'</td>'
								.'<td class="article-lines-url">'
									.$line['url']
								.'</td>'
								.(($editable == "true") ? ''
								.'<td>'
									.'<form name="article-line-edit" method="post" action="'.$actionUrl.'">'
										.'<input type="hidden" name="edit-line-id" value="'.$line['id'].'" />'
										.'<input type="hidden" name="article-line-edit" value="'.$rb->get('lines.edit').'" />'
										.'<input type="image" src="~/images/page_edi.png" name="article-line-edit" value="'.$rb->get('lines.edit').'" title="'.$rb->get('lines.editcap').'" />'
									.'</form> '
									.((count($artcs) == 0) ? ''
									.'<form name="article-line-delete" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
										.'<input type="hidden" name="delete-line-id" value="'.$line['id'].'" />'
										.'<input type="hidden" name="article-line-delete" value="'.$rb->get('lines.delete').'" />'
										.'<input class="confirm" type="image" src="~/images/page_del.png" name="article-line-delete" value="'.$rb->get('lines.delete').'" title="'.$rb->get('lines.deletecap').', id('.$line['id'].')" />'
									.'</form>'
									: '')
								.'</td>'
								: '')
							.'</tr>';
					$i ++;
				}
				$return .= ''
						.'</tbody>'
					.'</table>'
				.'</div>';
			} else {
				$return .= '<h4 class="error">'.$rb->get('lines.nolines').'</h4>';
			}
      
			if($useFrames != "false") {
				return parent::getFrame($rb->get('lines.title'), $return, "", true);
			} else {
				return $return;
			}
		}
    
		/**
		 *
		 *  Setups line id.
		 *  C tag.
		 *  
		 *  @param  method    		setups method (get | post | session)
		 *	@param	showError			show errors in output
		 *	@param	useFrames			use frames in output
		 *  @return form for selecting line id                    
		 *
		 */                   
		public function setLine($method = false, $showError = false, $useFrames = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			if($_POST['select-article-line'] == $rb->get('lines.select')) {
				$lineId = $_POST['line-id'];
				// test na prava pro zapis do rady clanku.
				$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
				if(count($permission) > 0) {
					if($method == 'session') {
						$_SESSION['article-line-id'] = $lineId;
					}
				} else {
					if($showError != 'false') {
						$return .= '<h4 class="error">Permission Denied!</h4>';
					}
				}
			}
      
			$actualLineId = -1;
			if($method == 'get' || $method == 'post') {
				$actualiLineId = $_REQUEST['line-id']; 
			} elseif($method == 'session') {
				$actualiLineId = $_SESSION['article-line-id'];
			}
      
			$lines = $dbObject->fetchAll('SELECT `article_line`.`id`, `article_line`.`name` FROM `article_line` LEFT JOIN `article_line_right` ON `article_line`.`id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `id`;');
			if(count($lines) > 0) {
				$return .= ''
				.'<div class="gray-box">'
					.'<form name="article-select-line" method="'.(($method == "get") ? 'get' : 'post').'" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<label for="select-line" class="padded">'.$rb->get('lines.selectcap').': </label>'
						.'<select id="select-line" name="line-id" class="w200">';
				foreach($lines as $line) {
					$return .= '<option value="'.$line['id'].'"'.(($actualiLineId == $line['id']) ? ' selected="selected"' : '').'>'.$line['name'].'</option>';
				}
				$return .= ''
						.'</select> '
						.'<input type="submit"'.(($method == "get") ? '' : ' name="select-article-line"').' value="'.$rb->get('lines.select').'" />'
					.'</form>'
				.'</div>';
			} else {
				if($showError != 'false') {
					$return .= '<h4 class="error">'.$rb->get('lines.nolines').'</h4>';
				}
			}
      
			if($useFrames != "false") {
				return parent::getFrame($rb->get('lines.selecttitle'), $return, "", true);
			} else {
				return $return;
			}
		}
    
		/**
		 *
		 *  Generates form for creating new line.
		 *  C tag.     
		 *  
		 *	@param	detailPageId				page id for next page
		 *	@param	useFrames						use frames in output
		 *	@return	form for creating article line		 		 		      
		 *
		 */                   
		public function createLine($detailPageId = false, $useFrames = false) {
			global $webObject;
			$return = '';
			$actionUrl = $_SERVER['REDIRECT_URL'];
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
      
			$return .= ''
			.'<div class="gray-box">'
				.'<form name="create-article-line" method="post" action="'.$actionUrl.'">'
					.'<input type="submit" name="article-line-create-submit" value="'.$rb->get('lines.new').'" title="'.$rb->get('lines.newcap').'" />'
				.'</form>'
			.'</div>';
      
			if($useFrames != "false") {
				return parent::getFrame($rb->get('lines.newtitle'), $return, '');
			} else {
				return $return;
			}
		}
    
		/**
		 *
		 *	Generated edit form article editation
		 *	C tag.
		 *
		 */
		public function showEditForm($useFrames = false, $submitPageId = false) {
			global $dbObject;
			global $webObject;
			global $loginObject;
			
			$return = '';
			$actionUrl = $_SERVER['REDIRECT_URL'];
			$article = array();
			$articleContent = array();
			$usedLangs = array();
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			
			if($_POST['article-save'] == $rb->get('articles.save')) {
				$article = array('id' => $_POST['article-id'], 'line_id' => $_POST['line-id']);
				$articleContent = array('article_id' => $_POST['article-id'], 'name' => $_POST['article-name'], 'head' => $_POST['article-head'], 'content' => $_POST['article-content'], 'author' => $_POST['article-author'], 'timestamp' => time(), 'language_id' => $_POST['language-id'], 'language_old_id' => $_POST['article-old-lang-id'], 'line_old_id' => $_POST['line-old-id'], 'url' => $_POST['article-url']);
				
				if($articleContent['url'] == '') {
					$articleContent['url'] = $articleContent['name'];
				}
				
				$articleContent['url'] = strtolower(parent::convertToUrlValid($articleContent['url']));
				$urls = parent::db()->fetchAll('select `article_id` from `article_content` left join `article` on `article_content`.`article_id` = `article`.`id` where `url` = "'.$articleContent['url'].'" and `line_id` = '.$article['line_id'].';');
				if(count($urls) == 0 || (count($urls) == 1 && $urls[0]['article_id'] == $article['id'])) {
					$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$article['line_id'].' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
					if(count($permission) > 0) {
						$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$articleContent['line_old_id'].' AND `article_line_right`.`type` = '.WEB_R_WRITE.' ORDER BY `value` DESC;');
						if(count($permission) > 0 && ($permission[0]['value'] >= $loginObject->getGroupValue())) {
							$artc = $dbObject->fetchAll("SELECT `article_id` FROM `article_content` WHERE `article_id` = ".$article['id']." AND `language_id` = ".$articleContent['language_old_id'].";");
							if(count($artc) == 0) {
								$artc = $dbObject->fetchAll("SELECT `article_id` FROM `article_content` WHERE `article_id` = ".$article['id'].";");
								if(count($artc) == 0) {
									$ac = $articleContent;
									$maxId = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `article`;');
									$article['id'] = $ac['article_id'] = $maxId[0]['id'] + 1;
									// Ulozeni - NOVY clanek
									$dbObject->execute("INSERT INTO `article`(`id`, `line_id`) VALUES (".$article['id'].", ".$article['line_id'].");");
									$dbObject->execute("INSERT INTO `article_content`(`article_id`, `name`, `url`, `head`, `content`, `author`, `timestamp`, `language_id`) VALUES (".$ac['article_id'].", \"".$ac['name']."\", \"".$ac['url']."\", \"".$ac['head']."\", \"".$ac['content']."\", \"".$ac['author']."\", ".$ac['timestamp'].", ".$ac['language_id'].");");
									$return .= '<h4 class="success">'.$rb->get('articles.newcreated').'</h4>';
									$_POST['article-id'] = $article['id'];
									$_POST['language-id'] = $ac['language_id'];
								} else {
									$ac = $articleContent;
									// Ulozeni - NOVA jaz.verze
									$dbObject->execute("INSERT INTO `article_content`(`article_id`, `name`, `url`, `head`, `content`, `author`, `timestamp`, `language_id`) VALUES (".$ac['article_id'].", \"".$ac['name']."\", \"".$ac['url']."\", \"".$ac['head']."\", \"".$ac['content']."\", \"".$ac['author']."\", ".$ac['timestamp'].", ".$ac['language_id'].");");
									$return .= '<h4 class="success">'.$rb->get('articles.langadded').'</h4>';
									$_POST['article-id'] = $article['id'];
									$_POST['language-id'] = $ac['language_id'];
								}
							} else {
								$ac = $articleContent;
								$dbObject->execute("UPDATE `article` SET `line_id` = ".$article['line_id']." WHERE `id` = ".$article['id'].";");
								$dbObject->execute("UPDATE `article_content` SET `name` = \"".$ac['name']."\", `url` = \"".$ac['url']."\", `head` = \"".$ac['head']."\", `content` = \"".$ac['content']."\", `author` = \"".$ac['author']."\", `timestamp` = ".$ac['timestamp'].", `language_id` = ".$ac['language_id']." WHERE `article_id` = ".$ac['article_id']." AND `language_id` = ".$ac['language_old_id'].";");
								$_POST['article-id'] = $article['id'];
								$_POST['language-id'] = $ac['language_id'];
								$return .= '<h4 class="success">'.$rb->get('articles.updated').'</h4>';
							}
						}
					} else {
						$return .= '<h4 class="error">'.$rb->get('articles.noperm').'</h4>';
					}
				} else {
					$return .= parent::getError($rb->get('articles.notuniqueurl'));
				}
			}
			
			if(array_key_exists('article-id', $_POST) && $_POST['article-id'] != '') {
				$articleId = $_POST['article-id'];
				$languageId = $_POST['language-id'];
				
				if($_POST['article-id'] != '' && array_key_exists('language-id', $_POST)) {
					// test na prava pro cteni z prislusne rady!
					$article = $dbObject->fetchAll('SELECT `article_content`.`article_id`, `article_content`.`language_id`, `article_content`.`name`, `article_content`.`url`, `article_content`.`head`, `article_content`.`content`, `article_content`.`author`, `article`.`line_id` FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` LEFT JOIN `article_line_right` ON `article`.`line_id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `article_content`.`article_id` = '.$articleId.' AND `article_content`.`language_id` = '.$languageId.' ORDER BY `id`;');
					if(count($article) != 0) {
						$article = $article[0];
					} else {
						$return .= parent::getError($rb->get('articles.notselected'));
						if($useFrames != "false") {
							return parent::getFrame($rb->get('articles.edittitle'), $return, '');
						} else {
							return $return;
						}
					}
					$new = false;
				} elseif(array_key_exists('article-id', $_POST)) {
					$article['article_id'] = $_POST['article-id'];
					$articleId = $_POST['article-id'];
					$new = true;
				} else {
					$new = true;
				}
				$usedLangs = $dbObject->fetchAll("SELECT `language_id` FROM `article_content` WHERE `article_id` = ".$article['article_id'].";");
			} else {
				$new = true;
				$userLangs = array();
				$article = $articleContent;
			}
			
			$langs = $dbObject->fetchAll("SELECT `id`, `language` FROM `language` ORDER BY `language`;");
      
			$langSelect = '<select id="language-id" name="language-id">';
			foreach($langs as $lang) {
				$ok = true;
				foreach($usedLangs as $usedLang) {
					if(in_array($lang['id'], $usedLang)) {
						$ok = false;
					}
					if(($lang['id'] == $article['language_id'])) {
						$ok = true;
					}
				}
				if($ok) {
					$langSelect .= '<option value="'.$lang['id'].'"'.(($lang['id'] == $article['language_id']) ? ' selected="selected"' : '').'>'.$lang['language'].'</option>';
				}
			}
			$langSelect .= '</select>';
      
			if($article['line_id'] == '') {
				if(array_key_exists('article-line-id', $_SESSION)) {
					$article['line_id'] = $_SESSION['article-line-id'];
				} elseif(array_key_exists('line-id', $_REQUEST)) {
					$article['line_id'] = $_REQUEST['line-id'];
				}
			}
      
			// Testovat prava zapisu do rady!!!
			$lines = $dbObject->fetchAll('SELECT DISTINCT `article_line`.`id`, `article_line`.`name` FROM `article_line` LEFT JOIN `article_line_right` ON `article_line`.`id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
			$lineSelect = '<select id="line-id" name="line-id" class="w160">';
			foreach($lines as $line) {
				$lineSelect .= '<option value="'.$line['id'].'"'.(($line['id'] == $article['line_id']) ? ' selected="selected"' : '').'>'.$line['name'].'</option>';
			}
			$lineSelect .= '</select>';
      
			if($submitPageId != false) {
				$actionUrl = $webObject->composeUrl($submitPageId);
			}
    
			$name = 'Article.editors';
			$system = new System();
			$propertyEditors = $system->getPropertyValue($name);
			$editAreaContentRows = $system->getPropertyValue('Article.editAreaContentRows');
			$editAreaHeadRows = $system->getPropertyValue('Article.editAreaHeadRows');
      
			$return .= ''
			.'<div class="article-mgm-edit">'
				.'<form name="article-edit" method="post" action="'.$actionUrl.'">'
					.'<div class="article-prop">'
						.'<div class="article-name gray-box-float">'
							.'<label for="article-name" class="w60">'.$rb->get('articles.name').':</label> '
							.'<input type="text" id="article-name" name="article-name" value="'.$article['name'].'" class="w300" />'
						.'</div>'
						.'<div class="article-line gray-box-float">'
							.'<label for="line-id" class="padded">'.$rb->get('articles.lines').':</label> '
							.$lineSelect
						.'</div>'
						.'<div class="article-lang gray-box-float">'
							.'<label for="language-id" class="padded">'.$rb->get('articles.lang').':</label> '
							.$langSelect
						.'</div>'
						.'<div class="article-author gray-box-float">'
							.'<label for="article-author" class="padded">'.$rb->get('articles.author').':</label> '
							.'<input type="text" id="article-author" name="article-author" value="'.$article['author'].'" class="w200" />'
						.'</div>'
						.'<div class="clear"></div>'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="article-url" class="w60">'.$rb->get('articles.url').':</label> '
						.'<input type="text" class="long-input" name="article-url" id="article-url" value="'.$article['url'].'" />'
					.'</div>';
				if($propertyEditors == 'edit_area') {
					$return .= ''
					.'<div id="editors" class="editors edit-area-editors">'
						.'<div id="editors-tab" class="editors-tab"></div>'
						.'<div id="cover-article-head">'
							.'<label for="article-head">'.$rb->get('articles.head2').':</label>'
							.'<textarea id="article-head" class="edit-area html" name="article-head" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 10).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['head']))).'</textarea>'
						.'</div>'
						.'<div id="cover-article-content">'
							.'<label for="article-content">'.$rb->get('articles.content2').':</label>'
							.'<textarea id="article-content" class="edit-area html" name="article-content" rows="'.($editAreaContentRows > 0 ? $editAreaContentRows : 20).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['content']))).'</textarea>'
						.'</div>'
					.'</div>';
				} elseif(($propertyEditors == 'tiny')) {  
					$return .= ''
					.'<div class="article-head">'
						.'<label for="article-head">'.$rb->get('articles.head2').':</label> '
						.'<div class="editor-cover">'
							.'<div class="tiny-cover">'
								.'<textarea id="article-head" name="article-head" class="" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 20).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['head']))).'</textarea>'
							.'</div>'
							.'<div class="clear"></div>'
						.'</div>'
					.'</div>'
					.'<div class="article-content">'
						.'<label for="article-content">'.$rb->get('articles.content2').':</label> '
						.'<div class="editor-cover">'
							.'<div class="tiny-cover">'
								.'<textarea id="article-content" name="article-content" class="" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 20).'">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['content']))).'</textarea>'
							.'</div>'
							.'<div class="clear"></div>'
						.'</div>'
					.'</div>'
					.'<script type="text/javascript">'
						.'initTiny("article-head");'
						.'initTiny("article-content");'
						.'tinyMCE.execCommand("mceAddControl", true, "article-content");'
						.'tinyMCE.execCommand("mceAddControl", true, "article-head"); '
					.'</script>';
				} else {  
					$return .= ''
					.'<div class="article-head">'
						.'<label for="article-head">'.$rb->get('articles.head2').':</label> '
						.'<div class="editor-cover">'
							.'<div class="textarea-cover">'
								.'<textarea id="article-head" name="article-head" class="editor-textarea editor-closed editor-tiny" rows="5">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['head']))).'</textarea>'
							.'</div>'
							.'<div class="clear"></div>'
						.'</div>'
					.'</div>'
					.'<div class="article-content">'
						.'<label for="article-content">'.$rb->get('articles.content2').':</label> '
						.'<div class="editor-cover">'
							.'<div class="textarea-cover">'
								.'<textarea id="article-content" name="article-content" class="editor-textarea editor-tiny" rows="15">'.str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['content']))).'</textarea>'
							.'</div>'
							.'<div class="clear"></div>'
						.'</div>'
					.'</div>';
				}
				
				$return .= ''
						.'<div class="article-bottom">'
							.'<div class="article-submit">'
								.'<input type="hidden" name="article-id" value="'.$article['article_id'].'" />'
								.'<input type="hidden" name="line-old-id" value="'.(($new) ? $lines[0]['id'] : $article['line_id']).'" />'
								.'<input type="hidden" name="article-old-lang-id" value="'.$article['language_id'].'" />'
								.'<input type="submit" name="article-save" value="'.$rb->get('articles.save').'" /> '
							.'</div>'
							.'<div class="clear"></div>'
						.'</div>'
					.'</form>'
				.'</div>';
			
			if($useFrames != "false") {
				if($new) {
					$title = $rb->get('articles.newtitle2');
				} else {
					$title = $rb->get('articles.edittitle2');
				}
				return parent::getFrame($title, $return, '');
			} else {
				return $return;
			}
		}
		
		/**
		 *
		 *	Edit article line form
		 *	C tag
		 *	
		 *	@param	useFrames		use frames in output
		 *	@param	submitPageId	page id to submit for to		 		 		 		 
		 *	@return	edit article form
		 *
		 */		 		 		 		 		
		public function showEditLineForm($useFrames = false, $submitPageId = false) {
			global $dbObject;
			global $webObject;
			global $loginObject;
			$return = '';
			$actionUrl = $_SERVER['REDIRECT_URL'];
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			
			$lineId = ((array_key_exists('edit-line-id', $_POST)) ? $_POST['edit-line-id'] : 0);
			// test na prava zapisu do rady clanku
			$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;;');
			if(count($permission) > 0) {
				$ok = true;
			} else {
				$return .= parent::getError($rb->get('articles.noperm'));
				if($useFrames != "false") {
					return parent::getFrame($rb->get('lines.edittitle2'), $return, '');
				} else {
					return $return;
				}
			}
			$ok = true;
			
			if($ok) {
				if($_POST['article-line-edit-submit'] == $rb->get('lines.save')) {
					$name = $_POST['article-line-edit-name'];
					$url = strtolower(parent::convertToValidUrl(strlen($_POST['article-line-edit-url']) == 0 ? $name : $_POST['article-line-edit-url']));
					$lineId = $_POST['article-line-edit-id'];
					$read = $_POST['article-right-edit-groups-r'];
					$write = $_POST['article-right-edit-groups-w'];
					$delete = $_POST['article-right-edit-groups-d'];
					
					$ok = true;
					$urlOk = true;
					if($lineId != 0) {
						$otherLines = parent::db()->fetchAll('select `name` from `article_line` where `url` = "'.$url.'" abd `id` = '.$lineId.';');
						if(count($otherLines) != 0) {
							$ok = false;
							$urlOk = false;
						}
					}
					
					if(strlen($name) > 3 && strlen($url) > 0 && $ok) {
						if($lineId == 0) {
							$dbObject->execute('INSERT INTO `article_line`(`name`, `url`) VALUES ("'.$name.'", "'.$url.'");');
							$return .= '<h4 class="success">'.$rb->get('lines.created').'</h4>';
							$lineId = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `article_line`;');
							$lineId = $lineId[0]['id'];
						} else {
							$dbObject->execute('UPDATE `article_line` SET `name` = "'.$name.'", `url` = "'.$url.'" WHERE `id` = '.$lineId.';');
							$return .= '<h4 class="success">'.$rb->get('lines.updated').'</h4>';
						}
						
						if(count($read) != 0) {
							$dbR = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `article_line_right`.`line_id` = ".$lineId." AND `type` = ".WEB_R_READ.";");
							foreach($dbR as $right) {
								if(!in_array($right, $read)) {
									$dbObject->execute("DELETE FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_READ.";");
								}
							}
							foreach($read as $right) {
								$row = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
								if(count($row) == 0) {
									$dbObject->execute("INSERT INTO `article_line_right`(`line_id`, `gid`, `type`) VALUES (".$lineId.", ".$right.", ".WEB_R_READ.");");
								}
							}
						}
						
						if(count($write) != 0) {
							$dbR = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `article_line_right`.`line_id` = ".$lineId." AND `type` = ".WEB_R_WRITE.";");
							foreach($dbR as $right) {
								if(!in_array($right, $write)) {
									$dbObject->execute("DELETE FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_WRITE.";");
								}
							}
							foreach($write as $right) {
								$row = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
								if(count($row) == 0) {
									$dbObject->execute("INSERT INTO `article_line_right`(`line_id`, `gid`, `type`) VALUES (".$lineId.", ".$right.", ".WEB_R_WRITE.");");
								}
							}
						}
						
						if(count($delete) != 0) {
							$dbR = $dbObject->fetchAll('SELECT `gid` FROM `article_line_right` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `type` = '.WEB_R_DELETE.';');
							foreach($dbR as $right) {
								if(!in_array($right, $delete)) {
									$dbObject->execute("DELETE FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_DELETE.";");
								}
							} 	
							foreach($delete as $right) {
								$row = $dbObject->fetchAll('SELECT `gid` FROM `article_line_right` WHERE `line_id` = '.$lineId.' AND `type` = '.WEB_R_DELETE.' AND `gid` = '.$right.';');
								if(count($row) == 0) {
									$dbObject->execute("INSERT INTO `article_line_right`(`line_id`, `gid`, `type`) VALUES (".$lineId.", ".$right.", ".WEB_R_DELETE.");");
								}
							}
						}
					} else {
						if($urlOk) {
							$return .= parent::getError($rb->get('lines.invalidname'));
						} else {
							$return .= parent::getError($rb->get('lines.invalidurl'));
						}
					}
				}
			
				if($submitPageId != false) {
					$actionUrl = $webObject->composeUrl($submitPageId);
				}
			
				// Ziskat prava ....
				$show = array('read' => true, 'write' => true, 'delete' => false);
				$groupsR = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_READ.";");
				$groupsW = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_WRITE.";");
				$groupsD = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = ".$lineId." AND `type` = ".WEB_R_DELETE.";");
				$allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
				$groupSelectR = '<select id="article-right-edit-groups-r" name="article-right-edit-groups-r[]" multiple="multiple" size="5">';
				$groupSelectW = '<select id="article-right-edit-groups-w" name="article-right-edit-groups-w[]" multiple="multiple" size="5">';
				$groupSelectD = '<select id="article-right-edit-groups-d" name="article-right-edit-groups-d[]" multiple="multiple" size="5">';
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
			
				$line = $dbObject->fetchAll('SELECT `name`, `url` FROM `article_line` WHERE `id` = '.$lineId.';');
				if(count($line) != 0 || $lineId == 0) {
					$return .= ''
					.'<div class="article-line-edit">'
						.'<form nam="article-line-edit" method="post" action="'.$actionUrl.'">'
							.'<div class="gray-box-float">'
								.'<label for="article-line-edit-name" class="w60">'.$rb->get('lines.name').':</label> '
								.'<input type="text" id="article-line-edit-name" name="article-line-edit-name" value="'.$line[0]['name'].'" class="w300" />'
							.'</div>'
							.'<div class="clear"></div>'
							.'<div class="article-url gray-box-float">'
								.'<label for="article-line-edit-url" class="w60">'.$rb->get('articles.url').':</label> '
								.'<input type="text" id="article-line-edit-url" name="article-line-edit-url" value="'.$line[0]['url'].'" class="w300" />'
							.'</div>'
							.'<div class="clear"></div>'
							.'<div class="article-line-rights">'
								.(($show['read']) ? ''
								.'<div class="article-line-r-r">'
									.'<label for="article-right-edit-groups-r">'.$rb->get('lines.permread').':</label>'
									.$groupSelectR
								.'</div>'
								: '')
								.(($show['write']) ? ''
								.'<div class="article-line-r-w">'
									.'<label for="article-right-edit-groups-w">'.$rb->get('lines.permwrite').':</label>'
									.$groupSelectW
								.'</div>'
								: '')
								.(($show['delete']) ? ''
								.'<div class="article-line-r-d">'
									.'<label for="article-right-edit-groups-d">'.$rb->get('lines.permdelete').':</label>'
									.$groupSelectD
								.'</div>'
								: '')
							.'</div>'
							.'<div class="clear"></div>'
							.'<div class="article-line-edit-submit">'
								.'<input type="hidden" name="article-line-edit-id" value="'.$lineId.'" />'
								.'<input type="submit" name="article-line-edit-submit" value="'.$rb->get('lines.save').'" />'
							.'</div>'
						.'</form>'
					.'</div>';
				} else {
					$return .= '<h4 class="error">'.$rb->get('lines.notoedit').'</h4>';
				}
			} else {
				$return .= '<h4 class="error">'.$rb->get('articles.noperm').'</h4>';
			}
			
			if($useFrames != "false") {
				return parent::getFrame($rb->get('lines.edittitle3'), $return, '');
			} else {
				return $return;
			}
		}
		
		// =============== ARTICLE DETAIL ==================================
		
		public function showId() {
			return parent::request()->get('id', 'current-article');
		}
		
		public function showDate($format = 'd.m.Y') {
			if($format == '') {
				$format = 'd.m.Y';
			}
			return date($format, parent::request()->get('date', 'current-article'));
		}
		
		public function showTime($format = 'H:i:s') {
			if($format == '') {
				$format = 'H:i:s';
			}
			return parent::request()->get('time', 'current-article');
		}
		
		public function showName() {
			return parent::request()->get('name', 'current-article');
		}
		
		public function showAuthor() {
			return parent::request()->get('author', 'current-article');
		}
		
		public function showHead() {
			return parent::request()->get('head', 'current-article');
		}
		
		public function showContent() {
			return parent::request()->get('content', 'current-article');
		}
		
		public function showLink() {
			return parent::request()->get('link', 'current-article');
		}
		
		// =============== PROPERTIES ======================================
		
		public function setUrl($url) {
			parent::request()->set('article-url', $url);
			return $url;
		}
		
		public function getUrl() {
			return parent::request()->get('article-url');
		}
		
		public function setLineUrl($url) {
			parent::request()->set('line-url', $url);
			return $url;
		}
		
		public function getLineUrl() {
			return parent::request()->get('line-url');
		}
    
	}

?>
