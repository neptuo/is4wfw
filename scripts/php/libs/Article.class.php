<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Article class
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-10-28
   * 
   */  
  class Article extends BaseTagLib {
  
    /**
     *
     *  Article id for dynamic address.     
     *
     */              
    private $CurrentId = 0;
  
    public function __construct() {
      global $dbObject;
      parent::setTagLibXml("xml/Article.xml");
    }
    
    /**
     *
     *  Shows articles from line.
     *  C tag.
     *  
     *  @param    lineId    		line id
     *  @param    template  		template for displaying (deprecated)
     *  @param    templateId  	template for displaying (using dynamic templates from cms)
     *  @param    pageId    		page id for paeg with detail
     *  @param		pageLangId		language id     
     *  @param		articleLangId	language id     
     *  @param    method    		method for passing detail id
     *  @param		sort					asc (default) / desc
     *  @return   list of articles
     *
     */                   
    public function showLine($lineId, $template = fales, $templateId = false, $pageId = false, $pageLangId = false, $articleLangId = false, $method = false, $sort = false) {
      global $webObject;
      global $dbObject;
      global $loginObject;
      $articleLangId = ($articleLangId != false) ? $articleLangId : $webObject->LanguageId;
      $pageLangId = ($pageLangId != false) ? $pageLangId : $webObject->LanguageId;
      $return = '';
      $detail = false;
      $link = "";
      
      if($pageId != false) {
        $detail = true;
        $link = $webObject->composeUrl($pageId, $pageLangId);
      }
      
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
      $articles = $dbObject->fetchAll("SELECT `article`.`id`, `article_content`.`name`, `article_content`.`head`, `article_content`.`content`, `article_content`.`author`, `article_content`.`timestamp` FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` LEFT JOIN `article_line_right` ON `article`.`line_id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article`.`line_id` = ".$lineId." AND `article_content`.`language_id` = ".$articleLangId." AND `article_line_right`.`type` = ".WEB_R_READ." AND (`group`.`gid` IN (".$loginObject->getGroupsIdsAsString().") OR `group`.`parent_gid` IN (".$loginObject->getGroupsIdsAsString().")) ORDER BY `article_content`.`timestamp` " . $sort . ";");
      if(count($articles) > 0) {
      	$flink = '';
        require_once("scripts/php/classes/CustomTagParser.class.php");
        foreach($articles as $article) {
          /*$templateContentRow = $templateContent;
          $article['timestamp'] = date("H:i:s d:m:Y", $article['timestamp']);
          if($detail) {
            if($method == "static") {
              //$article['head'] = '<a href="'.$link.'?article-id='.$article['id'].'">'.$article['head'].'</a>';
              $flink = $link.'?article-id='.$article['id'];
            } elseif($method == "dynamic") {
              //$article['head'] = '<a href="'.$link.'/'.$article['id'].'">'.$article['head'].'</a>';
              $flink = $link.'/'.$article['id'];
            }
          }
          
          $article['link'] = $flink;
          
          foreach($article as $key => $val) {
            $templateContentRow = str_replace('<tpl:'.$key.' />', $val, $templateContentRow);
          }
          $return .= $templateContentRow;
          */
          
          $_SESSION['article-id'] = $article['id'];
          $_SESSION['current-article']['date'] = date("d.m.Y", $article['timestamp']);
          $_SESSION['current-article']['time'] = date("H:i:s", $article['timestamp']);
          $_SESSION['current-article']['name'] = $article['name'];
          $_SESSION['current-article']['author'] = $article['author'];
          $_SESSION['current-article']['head'] = $article['head'];
          $_SESSION['current-article']['content'] = $article['content'];
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
        $return .= '<div class="no-articles"><h4 class="error">No Articles</h4></div>';
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
     *  @param		articleLangId	language id
     *  @param    method    		method for passing detail id
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
     *  @param    template  		template for displaying (deprecated)
     *  @param    templateId  	template for displaying (using dynamic templates from cms)
     *  @param    articleId   	article id
     *  @param		articleLangId	language id
     *  @param		showError		  show error in output   
     *  @return   article id in template
     *
     */                        
    public function showDetail($template = false, $templateId = false, $articleId = false, $articleLangId = false, $defaultArticleId = false, $showError = false) {
      global $webObject;
      global $dbObject;
      global $loginObject;
      $langId = $webObject->LanguageId;
      $return = '';
      
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
      	require_once("scripts/php/classes/CustomTagParser.class.php");
        /*$article[0]['timestamp'] = date("H:i:s d:m:Y", $article[0]['timestamp']);
        
        foreach($article[0] as $key => $val) {
          $templateContent = str_replace('<tpl:'.$key.' />', $val, $templateContent);
        }
        $return .= $templateContent;*/
        
        $_SESSION['current-article']['date'] = date("d.m.Y", $article[0]['timestamp']);
        $_SESSION['current-article']['time'] = date("H:i:s", $article[0]['timestamp']);
        $_SESSION['current-article']['name'] = $article[0]['name'];
        $_SESSION['current-article']['author'] = $article[0]['author'];
        $_SESSION['current-article']['head'] = $article[0]['head'];
        $_SESSION['current-article']['content'] = $article[0]['content'];
        
				$Parser = new CustomTagParser();
			  $Parser->setContent($templateContent);
			  $Parser->startParsing();
 				$return .= $Parser->getResult();
      } else {
        $return .= '<div class="no-article">No article selected!</div>';
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
     *  @param		detailPafeId    page id for next page
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
      
      if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
			
      if($lineId == false) {
      	if($method == "get" || $method == "post") {
          $lineId = $_REQUEST['line-id'];
        } elseif($method == "session") {
					$lineId = $_SESSION['article-line-id'];
				} else {
          return parent::getFrame('Message', '<h4 class="error">No Article line selected!</h4>', '');
        }
      }
      
      // test na prava zapisu do rady clanku
      $permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
			if(count($permission) > 0) {
				$ok = true;
			} else {
				$return .= '<h4 class="error">Please, select article line!</h4>';
				if($useFrames != "false") {
					return parent::getFrame('Articles in line', $return, '');
				} else {
					return $return;
				}
			}
			
			if($_POST['article-delete'] == "Delete Article") {
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
  	            .'<th class="article-mgm-th article-mgm-id">Id</th>'
    	          .'<th class="article-mgm-th article-mgm-lang">Lang</th>'
      	        .'<th class="article-mgm-th article-mgm-head">Head</th>'
        	      .'<th class="article-mgm-th article-mgm-edit">Edit</th>'
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
                .'<input type="hidden" name="article-add-lang" value="Add Lang" />'
                .'<input type="image" src="~/images/lang_add.png" name="article-add-lang" value="Add Lang" title="Add Language Version" />'
              .'</form>'
              .'<form name="article-add-lang2" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                .'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
                .'<input type="hidden" name="line-id" value="'.$lineId.'" />'
                .'<input type="hidden" name="article-delete" value="Delete Article" />'
                .'<input type="image" src="~/images/page_del.png" class="confirm" name="article-delete" value="Delete Article" title="Delete Article, id('.$article['id'].')" >'
              .'</form>'
              .'</td>'
              : '')
              .'<td class="article-mgm-td article-mgm-lang">'
              .$info['language']
              .'</td>'
              .'<td class="article-mgm-td article-mgm-head">'
                .'<div class="article-head-cover">'
                  .'<span class="article-head-in">'
                    .htmlspecialchars($info['head'])
                  .'</span>'
                .'</div>'
              .'</td>'
              .'<td class="article-mgm-td article-mgm-edit">'
              .'<form name="article-edit" method="post" action="'.$actionUrl.'">'
                .'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
                .'<input type="hidden" name="language-id" value="'.$info['lang_id'].'" />'
                .'<input type="hidden" name="line-id" value="'.$lineId.'" />'
                .'<input type="hidden" name="article-edit" value="Edit" />'
                .'<input type="image" src="~/images/page_edi.png" name="article-edit" value="Edit" title="Edit Article" /> '
              .'</form>'
              .'<form name="article-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                .'<input type="hidden" name="article-id" value="'.$article['id'].'" />'
                .'<input type="hidden" name="language-id" value="'.$info['lang_id'].'" />'
                .'<input type="hidden" name="line-id" value="'.$lineId.'" />'
                .'<input type="hidden" name="article-delete-lang" value="Delete Lang" />'
                .'<input type="image" src="~/images/lang_del.png" class="confirm" name="article-delete-lang" value="Delete Lang" title="Delete Article Language Version, id('.$article['id'].')" />'
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
        $returnTmp .= '<div class="no-articles"><h4 class="error">No Articles in this line!</h4></div>';
      }
      if($useFrames != "false") {
				return parent::getFrame('Articles in line', $returnTmp, '');
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
			
			if($lineId == false) {
      	if($method == "get" || $method == "post") {
          $lineId = $_REQUEST['line-id'];
        } elseif($method == "session") {
					$lineId = $_SESSION['article-line-id'];
				} else {
					if($useFrames != 'false') {
          	return parent::getFrame('Message', '<h4 class="error">No Article line selected!</h4>', '');
          } else {
						return '<h4 class="error">No Article line selected!</h4>';
					}
        }
      }
      
      if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
			
			$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND `article_line_right`.`line_id` = '.$lineId.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;;');
			if(count($permission) > 0) {
  	    $return .= ''
	      .'<div class="article-new">'
      	  .'<form name="article-new" method="post" action="'.$actionUrl.'">'
    	      .'<input type="submit" name="article-new" value="New Article" />'
  	      .'</form>'
	      .'</div>';
			} else {
				if($showError != 'false') {
					$return .= '<h4 class="error">Please, select article line!</h4>';
				}
			}
    
      
      if($useFrames != "false") {
      	return parent::getFrame("Create New Article", $return, "");
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
     */                        
    private function editArticleForm($article, $articleContent) {
      global $dbObject;
      $return = '';
      
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
      
      include_once('System.class.php');
    
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
              .'<label for="article-name">Name:</label> '
              .'<input type="text" name="article-name" value="'.$articleContent['name'].'" />'
            .'</div>'
            .'<div class="article-line">'
              .'<label for="line-id">Article Line:</label> '
              .$lineSelect
            .'</div>'
            .'<div class="article-lang">'
              .'<label for="article-lang-id">Language:</label> '
              .$langSelect
            .'</div>'
            .'<div class="article-author">'
              .'<label for="article-author">Author:</label> '
              .'<input type="text" name="article-author" value="'.$articleContent['author'].'" />'
            .'</div>'
            .'<div class="clear"></div>'
          .'</div>';
      if($propertyEditors == 'edit_area') {
				$return .= ''
					.'<div id="editors" class="editors edit-area-editors">'
						.'<div id="editors-tab" class="editors-tab"></div>'
						.'<div id="cover-article-head">'
							.'<label for="article-head">Article Head</label>'
							.'<textarea id="article-head" class="edit-area html" name="article-head" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 10).'">'.$articleContent['head'].'</textarea>'
						.'</div>'
						.'<div id="cover-article-content">'
							.'<label for="article-content">Article Content</label>'
							.'<textarea id="article-content" class="edit-area html" name="article-content" rows="'.($editAreaContentRows > 0 ? $editAreaContentRows : 20).'">'.$articleContent['content'].'</textarea>'
						.'</div>'
					.'</div>';
			} else {  
      	$return .= ''
          .'<div class="article-head">'
            .'<label for="article-head">Head:</label> '
            .'<div class="editor-cover">'
	            .'<div class="textarea-cover">'
	            	.'<textarea name="article-head" class="editor-textarea editor-closed" rows="5">'.$articleContent['head'].'</textarea>'
	            .'</div>'
            	.'<div class="clear"></div>'
            .'</div>'
          .'</div>'
          .'<div class="article-content">'
            .'<label for="article-content">Content:</label> '
            .'<div class="editor-cover">'
	            .'<div class="textarea-cover">'
  	          	.'<textarea name="article-content" class="editor-textarea editor-tiny" rows="15">'.$articleContent['content'].'</textarea>'
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
              .'<input type="submit" name="article-save" value="Save" /> '
              .'<input type="submit" name="article-save" value="Save and Close" /> '
              .'<input type="submit" name="article-close" value="Close" />'
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
      
      if($_POST['article-line-delete'] == "Delete") {
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
      
      $lines = $dbObject->fetchAll('SELECT `article_line`.`id`, `article_line`.`name` FROM `article_line` LEFT JOIN `article_line_right` ON `article_line`.`id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `id`;');
      
      if(count($lines) > 0) {
        $return .= ''
        .'<div class="show-lines"> '
          .'<table>'
          	.'<thead>'
          	.'<tr>'
          		.'<th class="show-lines-id">Id:</th>'
          		.'<th class="show-lines-name">Name:</th>'
          		.'<th class="show-lines-edit">Edit:</th>'
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
              .(($editable == "true") ? ''
              .'<td>'
                .'<form name="article-line-edit" method="post" action="'.$actionUrl.'">'
                  .'<input type="hidden" name="edit-line-id" value="'.$line['id'].'" />'
                  .'<input type="hidden" name="article-line-edit" value="Edit" />'
                  .'<input type="image" src="~/images/page_edi.png" name="article-line-edit" value="Edit" title="Edit Article line" />'
                .'</form> '
                .((count($artcs) == 0) ? ''
                .'<form name="article-line-delete" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                  .'<input type="hidden" name="delete-line-id" value="'.$line['id'].'" />'
                  .'<input type="hidden" name="article-line-delete" value="Delete" />'
                  .'<input class="confirm" type="image" src="~/images/page_del.png" name="article-line-delete" value="Delete" title="Delete Article line, id('.$line['id'].')" />'
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
        $return .= '<h4 class="error">No lines!</h4>';
      }
      
      if($useFrames != "false") {
      	return parent::getFrame("Article lines", $return, "", true);
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
      
      if($_POST['select-article-line'] == "Select") {
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
        .'<form name="article-select-line" method="'.(($method == "get") ? 'get' : 'post').'" action="'.$_SERVER['REDIRECT_URL'].'">'
        	.'<label for="select-line">Select article line: </label>'
          .'<select id="select-line" name="line-id">';
        foreach($lines as $line) {
          $return .= '<option value="'.$line['id'].'"'.(($actualiLineId == $line['id']) ? ' selected="selected"' : '').'>'.$line['name'].'</option>';
        }
        $return .= ''
          .'</select> '
          .'<input type="submit"'.(($method == "get") ? '' : ' name="select-article-line"').' value="Select" />'
        .'</form>';
      } else {
      	if($showError != 'false') {
        	$return .= '<h4 class="error">No lines to select!</h4>';
        }
      }
      
      if($useFrames != "false") {
      	return parent::getFrame("Select Line", $return, "", true);
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
      
      if($detailPageId != false) {
				$actionUrl = $webObject->composeUrl($detailPageId);
			}
      
      $return .= ''
      .'<form name="create-article-line" method="post" action="'.$actionUrl.'">'
      	.'<input type="hidden" name="">'
        .'<input type="submit" name="article-line-create-submit" value="Create" title="Create Article line" />'
      .'</form>';
      
      if($useFrames != "false") {
				return parent::getFrame('Create Article line', $return, '');
			} else {
				return $return;
			}
    }
    
    public function showEditForm($useFrames = false, $submitPageId = false) {
    	global $dbObject;
    	global $webObject;
    	global $loginObject;
			$return = '';
			$actionUrl = $_SERVER['REDIRECT_URL'];
			
			// Save article .... ;)
			if($_POST['article-save'] == "Save") {
				$article = array('id' => $_POST['article-id'], 'line_id' => $_POST['line-id']);
				$articleContent = array('article_id' => $_POST['article-id'], 'name' => $_POST['article-name'], 'head' => $_POST['article-head'], 'content' => $_POST['article-content'], 'author' => $_POST['article-author'], 'timestamp' => time(), 'language_id' => $_POST['language-id'], 'language_old_id' => $_POST['article-old-lang-id'], 'line_old_id' => $_POST['line-old-id']);
				
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
      	  		  $dbObject->execute("INSERT INTO `article`(`id`, `line_id`) VALUES (".$article['id'].", ".$article['line_id'].");");
    	  	    	$dbObject->execute("INSERT INTO `article_content`(`article_id`, `name`, `head`, `content`, `author`, `timestamp`, `language_id`) VALUES (".$ac['article_id'].", \"".$ac['name']."\", \"".$ac['head']."\", \"".$ac['content']."\", \"".$ac['author']."\", ".$ac['timestamp'].", ".$ac['language_id'].");");
	  		        $return .= '<h4 class="success">New Article Added!</h4>';
	  		        $_POST['article-id'] = $article['id'];
	  		        $_POST['language-id'] = $ac['language_id'];
			        } else {
  	  	    	  $ac = $articleContent;
	      		    $dbObject->execute("INSERT INTO `article_content`(`article_id`, `name`, `head`, `content`, `author`, `timestamp`, `language_id`) VALUES (".$ac['article_id'].", \"".$ac['name']."\", \"".$ac['head']."\", \"".$ac['content']."\", \"".$ac['author']."\", ".$ac['timestamp'].", ".$ac['language_id'].");");
    	  	    	$return .= '<h4 class="success">Article Language Version Added!</h4>';
    	  	    	$_POST['article-id'] = $article['id'];
	  		        $_POST['language-id'] = $ac['language_id'];
  	      		}
		      	} else {
  	    	  	$ac = $articleContent;
    		  	  $dbObject->execute("UPDATE `article` SET `line_id` = ".$article['line_id']." WHERE `id` = ".$article['id'].";");
    			    $dbObject->execute("UPDATE `article_content` SET `name` = \"".$ac['name']."\", `head` = \"".$ac['head']."\", `content` = \"".$ac['content']."\", `author` = \"".$ac['author']."\", `timestamp` = ".$ac['timestamp'].", `language_id` = ".$ac['language_id']." WHERE `article_id` = ".$ac['article_id']." AND `language_id` = ".$ac['language_old_id'].";");
    			    $_POST['article-id'] = $article['id'];
	  		      $_POST['language-id'] = $ac['language_id'];
  		    	  $return .= '<h4 class="success">Article Updated!</h4>';
		      	}
	      	}
				} else {
					$return .= '<h4 class="error">Permission Denied!</h4>';
				}
      }
			
			
			if(array_key_exists('article-id', $_POST) && array_key_exists('language-id', $_POST)) {
				$articleId = $_POST['article-id'];
				$languageId = $_POST['language-id'];
				
				// test na prava pro cteni z prislusne rady!
				$article = $dbObject->fetchAll('SELECT `article_content`.`article_id`, `article_content`.`language_id`, `article_content`.`name`, `article_content`.`head`, `article_content`.`content`, `article_content`.`author`, `article`.`line_id` FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` LEFT JOIN `article_line_right` ON `article`.`line_id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) AND `article_content`.`article_id` = '.$articleId.' AND `article_content`.`language_id` = '.$languageId.' ORDER BY `id`;');
				if(count($article) != 0) {
					$article = $article[0];
				} else {
					$return .= '<h4 class="error">No article selected!</h4>';
    		  if($useFrames != "false") {
						return parent::getFrame('Edit Article', $return, '');
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
      $lineSelect = '<select id="line-id" name="line-id">';
      foreach($lines as $line) {
        $lineSelect .= '<option value="'.$line['id'].'"'.(($line['id'] == $article['line_id']) ? ' selected="selected"' : '').'>'.$line['name'].'</option>';
      }
      $lineSelect .= '</select>';
      
      if($submitPageId != false) {
				$actionUrl = $webObject->composeUrl($submitPageId);
			}
			
			include_once('System.class.php');
    
			$name = 'Article.editors';
    	$system = new System();
		  $propertyEditors = $system->getPropertyValue($name);
		  $editAreaContentRows = $system->getPropertyValue('Article.editAreaContentRows');
		  $editAreaHeadRows = $system->getPropertyValue('Article.editAreaHeadRows');
      
      $return .= ''
      .'<div class="article-mgm-edit">'
        .'<form name="article-edit" method="post" action="'.$actionUrl.'">'
          .'<div class="article-prop">'
            .'<div class="article-name">'
              .'<label for="article-name">Name:</label> '
              .'<input type="text" id="article-name" name="article-name" value="'.$article['name'].'" />'
            .'</div>'
            .'<div class="article-line">'
              .'<label for="line-id">Article Line:</label> '
              .$lineSelect
            .'</div>'
            .'<div class="article-lang">'
              .'<label for="language-id">Language:</label> '
              .$langSelect
            .'</div>'
            .'<div class="article-author">'
              .'<label for="article-author">Author:</label> '
              .'<input type="text" id="article-author" name="article-author" value="'.$article['author'].'" />'
            .'</div>'
            .'<div class="clear"></div>'
          .'</div>';
      if($propertyEditors == 'edit_area') {
				$return .= ''
					.'<div id="editors" class="editors edit-area-editors">'
						.'<div id="editors-tab" class="editors-tab"></div>'
						.'<div id="cover-article-head">'
							.'<label for="article-head">Article Head</label>'
							.'<textarea id="article-head" class="edit-area html" name="article-head" rows="'.($editAreaHeadRows > 0 ? $editAreaHeadRows : 10).'">'.$article['head'].'</textarea>'
						.'</div>'
						.'<div id="cover-article-content">'
							.'<label for="article-content">Article Content</label>'
							.'<textarea id="article-content" class="edit-area html" name="article-content" rows="'.($editAreaContentRows > 0 ? $editAreaContentRows : 20).'">'.$article['content'].'</textarea>'
						.'</div>'
					.'</div>';
			} else {  
      	$return .= ''
          .'<div class="article-head">'
            .'<label for="article-head">Head:</label> '
            .'<div class="editor-cover">'
	            .'<div class="textarea-cover">'
	            	.'<textarea id="article-head" name="article-head" class="editor-textarea editor-closed editor-tiny" rows="5">'.$article['head'].'</textarea>'
	            .'</div>'
            	.'<div class="clear"></div>'
            .'</div>'
          .'</div>'
          .'<div class="article-content">'
            .'<label for="article-content">Content:</label> '
            .'<div class="editor-cover">'
	            .'<div class="textarea-cover">'
  	          	.'<textarea id="article-content" name="article-content" class="editor-textarea editor-tiny" rows="15">'.$article['content'].'</textarea>'
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
              .'<input type="submit" name="article-save" value="Save" /> '
            .'</div>'
            .'<div class="clear"></div>'
          .'</div>'
        .'</form>'
      .'</div>';
			
			if($useFrames != "false") {
				if($new) {
					$title = 'New Article';
				} else {
					$title = 'Edit article';
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
		 *	@param	useFrames			use frames in output
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
			
			$lineId = ((array_key_exists('edit-line-id', $_POST)) ? $_POST['edit-line-id'] : 0);
			// test na prava zapisu do rady clanku
			$permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = '.$lineId.' AND `article_line_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;;');
			if(count($permission) > 0) {
				$ok = true;
			} else {
				$return .= '<h4 class="error">Permission Denied!</h4>';
				if($useFrames != "false") {
					return parent::getFrame('Edit article line', $return, '');
				} else {
					return $return;
				}
			}
			$ok = true;
			
			if($ok) {
				if($_POST['article-line-edit-submit'] == "Save") {
					$name = $_POST['article-line-edit-name'];
					$lineId = $_POST['article-line-edit-id'];
					$read = $_POST['article-right-edit-groups-r'];
					$write = $_POST['article-right-edit-groups-w'];
					$delete = $_POST['article-right-edit-groups-d'];
					if(strlen($name) > 3) {
						if($lineId == 0) {
							$dbObject->execute('INSERT INTO `article_line`(`name`) VALUES ("'.$name.'");');
            	$return .= '<h4 class="success">Article line created!</h4>';
            	$lineId = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `article_line`;');
            	$lineId = $lineId[0]['id'];
						} else {
							$dbObject->execute('UPDATE `article_line` SET `name` = "'.$name.'" WHERE `id` = '.$lineId.';');
            	$return .= '<h4 class="success">Article line updated!</h4>';
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
						$return .= '<h4 class="error">Name must have at least 3 characters!</h4>';
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
			
				$line = $dbObject->fetchAll('SELECT `name` FROM `article_line` WHERE `id` = '.$lineId.';');
				if(count($line) != 0 || $lineId == 0) {
					$return .= ''
					.'<div class="article-line-edit">'
						.'<form nam="article-line-edit" method="post" action="'.$actionUrl.'">'
							.'<div class="article-line-edit-name">'
								.'<label for="article-line-edit-name">Name:</label> '
								.'<input type="text" id="article-line-edit-name" name="article-line-edit-name" value="'.$line[0]['name'].'" />'
							.'</div>'
							.'<div class="clear"></div>'
							.'<div class="article-line-rights">'
								.(($show['read']) ? ''
								.'<div class="article-line-r-r">'
									.'<label for="article-right-edit-groups-r">Read</label>'
									.$groupSelectR
								.'</div>'
								: '')
								.(($show['write']) ? ''
								.'<div class="article-line-r-w">'
									.'<label for="article-right-edit-groups-w">Write</label>'
									.$groupSelectW
								.'</div>'
								: '')
								.(($show['delete']) ? ''
								.'<div class="article-line-r-d">'
									.'<label for="">Delete</label>'
									.$groupSelectD
								.'</div>'
								: '')
							.'</div>'
							.'<div class="clear"></div>'
							.'<div class="article-line-edit-submit">'
								.'<input type="hidden" name="article-line-edit-id" value="'.$lineId.'" />'
								.'<input type="submit" name="article-line-edit-submit" value="Save" />'
							.'</div>'
						.'</form>'
					.'</div>';
				} else {
					$return .= '<h4 class="error">No line to edit!</h4>';
				}
			} else {
				$return .= '<h4 class="error">Permission Denied!</h4>';
			}
			
			if($useFrames != "false") {
				return parent::getFrame('Edit article line', $return, '');
			} else {
				return $return;
			}
		}
		
		public function showDate() {
			return $_SESSION['current-article']['date'];
		}
		
		public function showTime() {
			return $_SESSION['current-article']['time'];
		}
		
		public function showName() {
			return $_SESSION['current-article']['name'];
		}
		
		public function showAuthor() {
			return $_SESSION['current-article']['author'];
		}
		
		public function showHead() {
			return $_SESSION['current-article']['head'];
		}
		
		public function showContent() {
			return $_SESSION['current-article']['content'];
		}
		
		public function showLink() {
			return $_SESSION['current-article']['link'];
		}
    
  }

?>
