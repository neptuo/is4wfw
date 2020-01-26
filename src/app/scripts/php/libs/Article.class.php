<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FullTagParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Order.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/RoleHelper.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseGrid.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/FileAdmin.class.php");

    /**
     * 
     *  Article class
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-01-21
     * 
     */
    class Article extends BaseTagLib {
        public static $LineRightDesc = array(
            'article_line_right', 'line_id', 'gid', 'type'
        );
        
        public static $ArticlePageSize = 10;

        /**
         *
         *  Article id for dynamic address.     
         *
         */
        private $CurrentId = 0;
        private $BundleName = 'article';
        private $BundleLang = 'cs';

        public function __construct() {
            self::setTagLibXml("Article.xml");
            self::setLocalizationBundle('article');
        }
        
        protected function canUser($objectId, $rightType) {
            return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(Article::$LineRightDesc, $objectId, $rightType));
        }

        private function getGroupPermCached($name, $default = 'true') {
            if (parent::request()->exists($name, 'article-gperm')) {
                return parent::request()->get($name, 'article-gperm');
            } else {
                $value = parent::getGroupPerm($name, parent::login()->getMainGroupId(), true, $default);
                $value = ($value['value'] == 'true' ? true : false);
                parent::request()->set($name, $value, 'article-gperm');
                return $value;
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
         *  @param		sortBy
         *  @param		sort			asc (default) / desc
         *  @param		noDateMessage
         *  @param		limit
         *  @return   	list of articles
         *
         */
        public function showLine($lineId = false, $template = fales, $templateId = false, $pageId = false, $pageLangId = false, $articleLangId = false, $method = false, $sortBy = false, $sort = false, $noDataMessage = false, $limit = false, $visible = false, $labelIds = false, $pageable = false) {
            global $webObject;
            global $dbObject;
            global $loginObject;
            $articleLangId = ($articleLangId != false) ? $articleLangId : $webObject->LanguageId;
            $pageLangId = ($pageLangId != false) ? $pageLangId : $webObject->LanguageId;
            $return = '';
            $detail = false;
            $link = "";
            $rb = self::rb();

            if ($lineId == '') {
                if (parent::request()->exists('line-url')) {
                    $lineId = parent::request()->get('line-url');
                } else {
                    parent::getError('lines.notselected');
                }
            }

            if ($pageId != false) {
                $detail = true;
                $link = $webObject->composeUrl($pageId, $pageLangId);
            } else {
                $pageId = parent::web()->getPageId();
            }

            $lineInfo = parent::db()->fetchSingle('select `name`, `url` from `article_line` where `id` = ' . $lineId . ';');

            $templateContent = '';
            if ($templateId != false) {
                // ziskani templatu ...
                $templateContent = parent::getTemplateContent($templateId);
            } elseif ($template != false) {
                if (is_file($template) && is_readable($template)) {
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

            if ($sortBy == 'order') {
                $sortBy = '`article`.`order`';
            } elseif ($sortBy == 'name') {
                $sortBy = '`article_content`.`name`';
            } elseif ($sortBy == 'url') {
                $sortBy = '`article_content`.`url`';
            } elseif ($sortBy == 'head') {
                $sortBy = '`article_content`.`head`';
            } elseif ($sortBy == 'content') {
                $sortBy = '`article_content`.`content`';
            } else {
                $sortBy = '`article_content`.`timestamp`';
            }

            $pageSize = 0;
            if($pageable) {
                $pageSize = $limit;
                $limit = ' limit ' . (self::getArticlePage() * $limit).', '.$limit;
            } elseif ($limit != '') {
                $limit = ' limit ' . $limit;
            }

            if ($visible != '') {
                $visipart = 'and `article`.`visible` = ';
                switch ($visible) {
                    case '0':
                    case '1':
                    case '2': $visible = $visipart . $visible;
                        break;
                    case 'invisible': $visible = $visipart . '0';
                        break;
                    case 'archive': $visible = $visipart . '1';
                        break;
                    case 'visible': $visible = $visipart . '2';
                        break;
                    case 'all': $visible = '';
                        break;
                }
            } else {
                $visible = 'and `article`.`visible` = 2';
            }

            $labelsWhere = '';
            $labelsJoin = '';
            if ($labelIds != '') {
                $labelsJoin = ' join `article_attached_label` on `article`.`id` = `article_attached_label`.`article_id`';
                $labelsWhere = ' and `label_id` in (' . $labelIds . ')';
            }

            $sort = (strtolower($sort) == 'desc' ? 'DESC' : 'ASC');
            $fromWhere = "FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` " . $labelsJoin . " WHERE `article`.`line_id` = " . $lineId . " AND `article_content`.`language_id` = " . $articleLangId . " " . $visible . $labelsWhere . " ORDER BY " . $sortBy . " " . $sort;
            
            $articles = $dbObject->fetchAll("SELECT distinct `article`.`id`, `article_content`.`name`, `article_content`.`url`, `article_content`.`head`, `article_content`.`content`, `article_content`.`author`, `article_content`.`timestamp`, `article_content`.`datetime`, `article`.`visible`, `article`.`directory_id` ".$fromWhere . $limit . ";");
            if (count($articles) > 0 && self::canUser($lineId, WEB_R_READ)) {
                $flink = '';
                parent::request()->set('line-url', $lineInfo['url']);
                $articleOldId = self::getArticleId();
                $articleDirectoryOldId = self::getArticleDirectoryId();
                $lastArticleLanguageIdId = self::getArticleLanguageId();
                foreach ($articles as $article) {
                    self::setArticleId($article['id']);
                    self::setIsActiveArticle($article['id'] == $articleOldId);
                    self::setArticleDirectoryId($article['directory_id']);
                    self::setHasHead(strlen($article['head']) > 0);
                    self::setHasContent(strlen($article['content']) > 0);
                    self::setIsExternalUrl(strpos($article['url'], '://') !== false);
                    self::setArticleLanguageId($articleLangId);
                    parent::request()->set('id', $article['id'], 'current-article');
                    parent::request()->set('date', $article['timestamp'], 'current-article');
                    parent::request()->set('time', $article['timestamp'], 'current-article');
                    parent::request()->set('datetime', $article['datetime'], 'current-article');
                    parent::request()->set('name', $article['name'], 'current-article');
                    parent::request()->set('author', $article['author'], 'current-article');
                    parent::request()->set('head', $article['head'], 'current-article');
                    parent::request()->set('content', $article['content'], 'current-article');
                    parent::request()->set('visible', $rb->get('articles.visible.' . $article['visible']), 'current-article');

                    self::setUrl($article['url']);
                    if ($detail) {
                        if ($method == "static") {
                            $flink = $link . '?article-id=' . $article['id'];
                        } elseif ($method == "dynamic") {
                            $flink = $link . '/' . $article['id'];
                        }
                    }
                    parent::request()->set('link', $flink, 'current-article');
                    $_SESSION['current-article']['link'] = $flink;

                    $Parser = new FullTagParser();
                    $Parser->setContent($templateContent);
                    $Parser->startParsing();
                    $return .= $Parser->getResult();
                }
                if($pageable) {
                    $total = parent::db()->fetchSingle('select count(`article`.`id`) as `id` '.$fromWhere.';');
                    $return .= self::getPaging($total['id'], $pageSize, self::getArticlePage(), $pageId, $pageLangId);
                }
                
                self::setArticleId($articleOldId);
                self::setArticleDirectoryId($articleDirectoryOldId);
                self::setArticleLanguageId($lastArticleLanguageIdId);
                unset($_SESSION['article-id']);
                unset($_SESSION['current-article']);
            } else {
                if ($noDataMessage != '') {
                    $return .= $noDataMessage;
                } else {
                    $return .= parent::getError($rb->get('articles.noarticles'));
                }
            }

            return $return;
        }
        
        private function getArticlePage() {
            $start = 0;
            if(array_key_exists("article-page", $_REQUEST)) {
                $start = $_REQUEST["article-page"] - 1;
            }
            
            return $start;
        }
        
        private function getPaging($total, $size, $index, $pageId, $pageLangId) {
            $pages = ceil($total / $size);
            //parent::log('Paging: '.$total.', '.$size.', '.$pages);
            if($pages == 0 || $pages == 1) {
                return;
            }
        
            $return = '<div class="article-nav">';
            $url = parent::web()->composeUrl($pageId, $pageLangId);
            for($i = 0; $i < $pages; $i ++) {
                if($i != 0) {
                    $return .= '<a class="'.($i == self::getArticlePage() ? 'current' : '').'" href="' . parent::addUrlParameter($url, 'article-page', $i + 1) . '">['.($i + 1).']</a> ';
                } else {
                    $return .= '<a class="'.($i == self::getArticlePage() ? 'current' : '').'" href="' . parent::removeUrlParameter($url, 'article-page') . '">['.($i + 1).']</a> ';
                }
            }
            
            return $return.'<div class="clear"></div></div>';
        }

        /**
         *
         * 	Show article line as RSS document.
         * 	C tag
         * 	
         * 	@param		lineId				article line id
         * 	@param		pageId				page id to show detail     
         *  @param		articleLangId		language id
         *  @param    	method    			method for passing detail id
         * 	@return		RSS document		 		 		      
         *
         */
        public function showRssLine($lineId, $pageId = false, $articleLangId = false, $pageLangId = false, $method = false) {
            global $webObject;
            global $dbObject;
            $return = '';
            $detail = false;

            $articleLangId = ($articleLangId != false) ? $articleLangId : $webObject->LanguageId;
            $pageLangId = ($pageLangId != false) ? $pageLangId : $webObject->LanguageId;

            if ($pageId != false) {
                $detail = true;
                $link = $webObject->composeUrl($pageId, $pageLangId, true);
            }

            $lineName = $dbObject->fetchAll('SELECT `name` FROM `article_line` WHERE `id` = ' . $lineId . ';');
            $langName = $dbObject->fetchAll('SELECT `language` FROM `language` WHERE `id` = ' . $articleLangId . ';');

            // prava na cteni???
            $articles = $dbObject->fetchAll('SELECT `id`, `name`, `head`, `timestamp` FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` WHERE `article`.`line_id` = ' . $lineId . ' AND `article_content`.`language_id` = ' . $articleLangId . ';');
            $items = '';
            foreach ($articles as $article) {
                $flink = '';
                if ($detail) {
                    if ($method == "static") {
                        $flink = $link . '?article-id=' . $article['id'];
                    } elseif ($method == "dynamic") {
                        $flink = $link . '/' . $article['id'];
                    }
                }

                $items .= ''
                . '<item>'
                    . '<title>' . $article['name'] . '</title>'
                    . '<link>' . $flink . '</link>'
                    . '<description>' . $article['head'] . '</description>'
                    . '<pubDate>' . date("d.m.Y H:i", $article['timestamp']) . '</pubDate>'
                    . '<guid></guid>'
                . '</item>';
            }

            if (count($lineName) == 1) {
                $return .= ''
                . '<rss version="2.0">'
                    . '<channel>'
                        . '<title>' . $webObject->getPageTitle() . '</title>'
                        . '<link>http://' . $webObject->getHttpHost() . INSTANCE_URL . '</link>'
                        . '<description>' . $lineName[0]['name'] . '</description>'
                        . '<language>' . $langName[0]['language'] . '</language>'
                        . '<pubDate>Tue, 10 Jun 2003 04:00:00 GMT</pubDate>'
                        . '<lastBuildDate>Tue, 10 Jun 2003 09:41:01 GMT</lastBuildDate>'
                        . '<docs>http://' . $webObject->getCurrentRequestPath() . '</docs>'
                        . '<generator>WFW RSSMM - RSS Generator 1.0</generator>'
                        . '<managingEditor>editor@example.com</managingEditor>'
                        . '<webMaster>webmaster@papayateam.cz</webMaster>'
                        . $items
                    . '</channel>'
                . '</rss>';

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
        public function showDetail($template = false, $templateId = false, $articleId = false, $articleLangId = false, $defaultArticleId = false, $showError = false, $lineId = 0, $nextLinkText = '', $prevLinkText = '') {
            global $dbObject;
            global $loginObject;

            $templateContent = '';
            if ($templateId != false) {
                // ziskani templatu ...
                $rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = ' . $templateId . ' AND `template_right`.`type` = ' . WEB_R_READ . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
                if (count($rights) > 0 && $templateId > 0) {
                    $template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = ' . $templateId . ';');
                    $templateContent = $template[0]['content'];
                } else {
                    $message = "Permission Denied when reading template[templateId = " . $templateId . "]!";
                    trigger_error($message, E_USER_WARNING);
                    return;
                }
            } elseif ($template != false) {
                if (is_file($template) && is_readable($template)) {
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

            return $this->showDetailFullTag($templateContent, $articleId, $articleLangId, $defaultArticleId, $showError, $lineId, $nextLinkText, $prevLinkText);
        }

        // C-tag
        public function showDetailFullTag($templateContent, $articleId = false, $articleLangId = false, $defaultArticleId = false, $showError = false, $lineId = 0, $nextLinkText = '', $prevLinkText = '') {
            global $webObject;
            global $dbObject;
            global $loginObject;
            $langId = $webObject->LanguageId;
            $return = '';
            $rb = self::rb();

            $articleLangId = ($articleLangId != false) ? $articleLangId : $webObject->LanguageId;

            if ($articleId == false) {
                if (array_key_exists('article-id', $_REQUEST)) {
                    $articleId = $_REQUEST['article-id'];
                } elseif (array_key_exists('article-id', $_SESSION)) {
                    $articleId = $_SESSION['article-id'];
                } elseif ($defaultArticleId != false) {
                    $articleId = $defaultArticleId;
                } elseif ($this->CurrentId != 0) {
                    $articleId = $this->CurrentId;
                } elseif (self::getUrl() != '') {
                    $url = self::getUrl();
                    $sql = 'select `article_id` from `article_content` left join `article` on `article_content`.`article_id` = `article`.`id` where `url` = "' . $url . '"' . ($lineId != 0 && is_numeric($lineId) ? ' and `line_id` = ' . $lineId : '') . ';';
                    $arid = parent::db()->fetchAll($sql);
                    if (count($arid) == 1) {
                        $articleId = $arid[0]['article_id'];
                    } else {
                        if ($showError != 'false') {
                            $message = 'Missing argument [article-id]!';
                            echo '<h4 class="error">' . $message . '</h4>';
                            trigger_error($messgae, E_USER_WARNING);
                        }
                        return;
                    }
                } else {
                    if ($showError != 'false') {
                        $message = 'Missing argument [article-id]!';
                        echo '<h4 class="error">' . $message . '</h4>';
                        trigger_error($messgae, E_USER_WARNING);
                    }
                    return;
                }
            }

            $article = $dbObject->fetchAll("SELECT `name`, `keywords`, `head`, `content`, `author`, `timestamp`, `datetime`, `article`.`directory_id` FROM `article_content` JOIN `article` ON `article_content`.`article_id` = `article`.`id` WHERE `article_id` = " . $articleId . " AND `language_id` = " . $articleLangId . ";");
            if (count($article) == 1) {
                $lastDirectoryId = self::getArticleDirectoryId();
                $lastArticleLanguageId = self::getArticleLanguageId();
                self::setArticleDirectoryId($article[0]['directory_id']);
                self::setArticleLanguageId($articleLangId);
                self::setHasHead(strlen($article[0]['head']) > 0);
                self::setHasContent(strlen($article[0]['content']) > 0);
                self::setIsExternalUrl(strpos($article[0]['url'], '://') !== false);

                parent::request()->set('id', $articleId, 'current-article');
                parent::request()->set('directoryid', $article[0]['directory_id'], 'current-article');
                parent::request()->set('date', $article[0]['timestamp'], 'current-article');
                parent::request()->set('time', $article[0]['timestamp'], 'current-article');
                parent::request()->set('datetime', $article[0]['datetime'], 'current-article');
                parent::request()->set('name', $article[0]['name'], 'current-article');
                parent::request()->set('keywords', $article[0]['keywords'], 'current-article');
                parent::request()->set('author', $article[0]['author'], 'current-article');
                parent::request()->set('head', $article[0]['head'], 'current-article');
                parent::request()->set('content', $article[0]['content'], 'current-article');

                $Parser = new FullTagParser();
                $Parser->setContent($templateContent);
                $Parser->startParsing();
                $return .= $Parser->getResult();
                $return .= self::nextPrevNavigation($articleId, $lineId, $webObject->getPageId(), $nextLinkText, $prevLinkText);
                
                self::setArticleDirectoryId($lastDirectoryId);
                self::setArticleLanguageId($lastArticleLanguageId);
            } else {
                $return .= '<div class="no-article">' . $rb->get('articles.notselected') . '</div>';
            }
            return $return;
        }
        
        private function nextPrevNavigation($articleId, $lineId, $pageId, $nextLinkText, $prevLinkText) {
            
            $result = '';
            if(($nextLinkText != '' || $prevLinkText != '') && $lineId != 0) {
                $oldId = self::getArticleId();
                $prevId = 0;
                $nextId = 0;
                
                $articles = parent::db()->fetchAll('select `id` from `article` where `line_id` = '.$lineId.' order by `order` desc;');
                for($i = 0; $i < count($articles); $i ++) {
                    if($articles[$i]['id'] == $articleId) {
                        if($i > 0) {
                            $prevId = $articles[$i - 1]['id'];
                        }
                        if($i < (count($articles) - 1)) {
                            $nextId = $articles[$i + 1]['id'];
                        }
                    }
                }
                //echo $articleId.'>'.$prevId.'--'.$nextId;
                
                self::setArticleId($prevId);
                self::setIsActiveArticle($prevId == $oldId);
                $prevUrl = parent::web()->composeUrl($pageId);
                self::setArticleId($nextId);
                self::setIsActiveArticle($nextId == $oldId);
                $nextUrl = parent::web()->composeUrl($pageId);
                
                $result .= ''
                .'<div class="article-nav">'
                    .($prevId != 0 ? '<a href="'.$prevUrl.'" class="article-nav-prev">'.$prevLinkText.'</a>' : '')
                    .($nextId != 0 ? '<a href="'.$nextUrl.'" class="article-nav-next">'.$nextLinkText.'</a>' : '')
                    .'<div class="clear"></div>'
                .'</div>';
                
                self::setArticleId($oldId);
            }
            return $result;
        }

        /**
         *
         * 	Display labels (all|for line|for article)
         * 	C tag.
         *
         */
        public function showLabels($templateId, $articleId = false, $lineId = false, $languageId = false, $sortBy = false, $sort = false, $limit = false, $noDataMessage = false) {
            $return = '';
            $columnSql = ' al.`id`, al.`name`, al.`url`';
            $whereSql = '';
            $joinSql = '';

            $isLanguageRequired = false;
            $joinLanguageId = parent::web()->LanguageId;
            if($languageId != '') {
                $joinLanguageId = $languageId;
                $isLanguageRequired = true;
            }

            $joinSql .= ($languageId != '' ? '' : ' LEFT') . ' JOIN `article_label_language` `all` ON al.`id` = `all`.`label_id` AND `all`.`language_id` = ' . $joinLanguageId;
            $columnSql .= ', `all`.`name` AS `all_name`, `all`.`url` AS `all_url`';

            if ($articleId != '') {
                if ($whereSql == '') {
                    $whereSql .= ' WHERE';
                } else {
                    $whereSql .= ' AND';
                }
                $whereSql .= ' `article_attached_label`.`article_id` = ' . $articleId;
                $joinSql .= ' JOIN `article_attached_label` ON al.`id` = `article_attached_label`.`label_id`';
            }
            if ($lineId != '') {
                if ($whereSql == '') {
                    $whereSql .= ' WHERE';
                } else {
                    $whereSql .= ' AND';
                }
                $whereSql .= ' `article_line_label`.`line_id` = ' . $lineId;
                $joinSql .= ' JOIN `article_line_label` ON al.`id` = `article_line_label`.`label_id`';
            }
            if ($sort != 'desc') {
                $sort = 'asc';
            }
            if ($sortBy != 'id' && $sortBy != 'name' && $sortBy != 'url' && $sortBy != 'order') {
                $sortBy = 'order';
            }
            if ($limit != '') {
                $limit = 'LIMIT ' . $limit;
            }

            $sortBy = '`' . $sortBy . '`';
            if($isLanguageRequired && $sortBy != '`id`') {
                $sortBy = '`all`.' . $sortBy;
            }

            $oldLabelId = self::getLabelId();

            $labels = parent::db()->fetchAll('SELECT' . $columnSql . ' FROM `article_label` AS al' . $joinSql . $whereSql . ' ORDER BY ' . $sortBy . ' ' . $sort . ' ' . $limit . ';');
            if (count($labels) > 0) {
                $templateContent = parent::getTemplateContent($templateId);
                $i = 1;
                foreach ($labels as $label) {
                    $item = array('id' => $label['id'], 'name' => $label['name'], 'url' => $label['url']);
                    if($label['all_name'] != '') {
                        $item['name'] = $label['all_name'];
                    }
                    if($label['all_url'] != '') {
                        $item['url'] = $label['all_url'];
                    }

                    parent::request()->set('i', $i, 'current-label');
                    parent::request()->set('label', $item, 'current-label');
                    self::setIsActiveLabel($label['id'] == $oldLabelId);
                    self::setLabelId($item['id']);
                    $parser = new FullTagParser();
                    $parser->setContent($templateContent);
                    $parser->startParsing();
                    $return .= $parser->getResult();
                    $i++;
                }
            } else {
                if ($noDataMessage != '') {
                    $return .= parent::getWarning($noDataMessage);
                }
            }

            self::setLabelId($oldLabelId);
            return $return;
        }

        /**
         *
         * 	Shows field from ArticleLabel.
         *
         */
        public function showLabel($type, $labelId = false) {
            $return = '';
            $label = array();
            if ($labelId == '') {
                $label = parent::request()->get('label', 'current-label');
            } else {
                $label = parent::db()->fetchSingle('select `id`, `name`, `url` from `article_label` where `id` = ' . $labelId);
            }

            switch ($type) {
                case 'i': $return .= parent::request()->get('i', 'current-label');
                    break;
                case 'id': $return .= $label['id'];
                    break;
                case 'name': $return .= $label['name'];
                    break;
                case 'url': $return .= $label['url'];
                    break;
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
         *  DECPRECATED!!
         *
         */
        public function composeUrl() {
            global $webObject;
            global $phpObject;
            global $dbObject;
            $cdp = $webObject->getCurrentDynamicPath();

            $id = $phpObject->str_tr($cdp, "-", 1);

            $file = $dbObject->fetchAll("SELECT `name` FROM `article_content` WHERE `article_id` = " . $id[0] . " AND `language_id` = " . $webObject->LanguageId . ";");
            if (count($file) == 1 && $cdp == $id[0]) {
                $this->CurrentId = $id[0];
                $_SESSION['article']['current_id'] = $id[0];
                self::setArticleId($id[0]);
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
         *  @param	  detailPageId    page id for next articles.selectline
         *  @param	  method					method of passing arguments
         *  @param	  useFrames				use frames in output
         *  @return   complete article management
         *
         */
        public function showManagement($lineId = false, $detailPageId = false, $method = false, $useFrames = false, $newArticleButton = false, $labelFilter = false, $pageable = false, $customFormId = false) {
            global $dbObject;
            global $loginObject;
            global $webObject;
            $return = '';
            $actionUrl = $_SERVER['REQUEST_URI'];
            $rb = self::rb();

            if ($detailPageId != false) {
                $actionUrl = $webObject->composeUrl($detailPageId);
            }

            if ($lineId == false) {
                if ($method == "get" || $method == "post") {
                    $lineId = $_REQUEST['line-id'];
                } elseif ($method == "session") {
                    $lineId = $_SESSION['article-line-id'];
                } else {
                    return parent::getFrame('Message', '<h4 class="error">' . $rb->get('articles.nolineselected') . '</h4>', '');
                }
            }

            if(!self::canUser($lineId, WEB_R_WRITE)) {
                $return .= parent::getError(parent::rb('articles.selectline'));
                if ($useFrames != "false") {
                    return parent::getFrame($rb->get('articles.inlinetitle'), $return, '');
                } else {
                    return $return;
                }
            }

            $hasCustomForm = $customFormId != '';
            if ($hasCustomForm) {
                parent::php()->autoRegisterPrefix('cf');
                global $cfObject;
            }
            
            if ($_POST['article-edit'] == $rb->get('articles.edit')) {
                $url = parent::addUrlParameter($actionUrl, 'article-id', $_POST['article-id']);
                $url = parent::addUrlParameter($url, 'language-id', $_POST['language-id']);
                parent::web()->redirectTo($url);
            } elseif($_POST['article-add-lang'] == $rb->get('articles.addlang')) {
                $url = parent::addUrlParameter($actionUrl, 'article-id', $_POST['article-id']);
                $url = parent::addUrlParameter($url, 'line-id', $_POST['line-id']);
                parent::web()->redirectTo($url);
            }

            if ($_POST['article-move-up'] == $rb->get('articles.moveup')) {
                $artcId = $_POST['article-id'];
                $lineId = $_POST['line-id'];

                // Vyber clanek pred a prohod ordery
                //parent::db()->setMockMode(true);
                $article = parent::db()->fetchSingle('select `id`, `order` from `article` where `id` = ' . $artcId . ';');
                $articlesInLine = parent::db()->fetchAll('select `id`, `order` from `article` where `line_id` = ' . $lineId . ' order by `order` desc;');
                for ($i = 0; $i < count($articlesInLine); $i++) {
                    if ($articlesInLine[$i]['id'] == $article['id']) {
                        if ($i != 0) {
                            $order = $article['order'];
                            parent::db()->execute('update `article` set `order` = ' . $articlesInLine[$i - 1]['order'] . ' where `id` = ' . $article['id'] . ';');
                            parent::db()->execute('update `article` set `order` = ' . $order . ' where `id` = ' . $articlesInLine[$i - 1]['id'] . ';');
                            $returnTmp .= parent::getSuccess($rb->get('articles.moved'));
                            break;
                        } else {
                            $returnTmp .= parent::getError($rb->get('articles.cantmove'));
                            break;
                        }
                    }
                }
                //parent::db()->setMockMode(false);
            } elseif ($_POST['article-move-down'] == $rb->get('articles.movedown')) {
                $artcId = $_POST['article-id'];
                $lineId = $_POST['line-id'];

                // Vyber clanek za a prohod ordery
                //parent::db()->setMockMode(true);
                $article = parent::db()->fetchSingle('select `id`, `order` from `article` where `id` = ' . $artcId . ';');
                $articlesInLine = parent::db()->fetchAll('select `id`, `order` from `article` where `line_id` = ' . $lineId . ' order by `order` desc;');
                for ($i = 0; $i < count($articlesInLine); $i++) {
                    if ($articlesInLine[$i]['id'] == $article['id']) {
                        if ($i != count($articlesInLine) - 1) {
                            $order = $article['order'];
                            parent::db()->execute('update `article` set `order` = ' . $articlesInLine[$i + 1]['order'] . ' where `id` = ' . $article['id'] . ';');
                            parent::db()->execute('update `article` set `order` = ' . $order . ' where `id` = ' . $articlesInLine[$i + 1]['id'] . ';');
                            $returnTmp .= parent::getSuccess($rb->get('articles.moved'));
                            break;
                        } else {
                            $returnTmp .= parent::getError($rb->get('articles.cantmove'));
                            break;
                        }
                    }
                }
                //parent::db()->setMockMode(false);
            }

            if ($_POST['article-delete'] == $rb->get('articles.delete')) {
                $artcId = $_POST['article-id'];

                $article = $dbObject->fetchSingle("SELECT `directory_id` from `article` where `id` = " . $artcId . ";");

                $dbObject->execute("DELETE FROM `article_content` WHERE `article_id` = " . $artcId . ";");
                $dbObject->execute("DELETE FROM `article` WHERE `id` = " . $artcId . ";");

                if($article['directory_id'] != '') {
                    $fa = new FileAdmin();
                    $fa->deleteDirectory($article['directory_id'], true);
                }

                $languages = parent::dao('Language')->getList();
                foreach ($languages as $language) {
                    if ($hasCustomForm) {
                        $cfObject->delete($customFormId, array('id' =>  $artcId, 'language_id' => $language['id']));
                    }
                }
            } elseif ($_POST['article-delete-lang'] == $rb->get('articles.deletelang')) {
                $artcId = $_POST['article-id'];
                $langId = $_POST['language-id'];

                $dbObject->execute("DELETE FROM `article_content` WHERE `article_id` = " . $artcId . " AND `language_id` = " . $langId . ";");
                $artcs = $dbObject->fetchAll("SELECT `article_id` FROM `article_content` WHERE `article_id` = " . $artcId . ";");
                if (count($artcs) == 0) {
                    $article = $dbObject->fetchSingle("SELECT `directory_id` from `article` where `id` = " . $artcId . ";");
                    
                    $dbObject->execute("DELETE FROM `article` WHERE `id` = " . $artcId . ";");
                    
                    if($article['directory_id'] != null) {
                        $fa = new FileAdmin();
                        $fa->deleteDirectory($article['directory_id'], true);
                    }
                }

                if ($hasCustomForm) {
                    $cfObject->delete($customFormId, array('id' =>  $artcId, 'language_id' => $langId));
                }
            }

            $returnTmp .= ''
                . '<div class="article-mgm-show">';
                
            if($labelFilter) {
                if($_POST['filter-labels'] == parent::rb('lines.select')) {
                    $labels = array();
                    foreach($_POST['label-filter'] as $key=>$value) {
                        $labels[] = $key;
                    }
                    parent::session()->set('filter-labels', $labels, 'article');
                }
            
                $labels = parent::db()->fetchAll('select `id`, `name` from `article_line_label` as `all` left join `article_label` as `al` on `all`.`label_id` = `id` where `all`.`line_id` = '.$lineId.' order by `name`;');
                $options = '';
                foreach($labels as $label) {
                    $options .= ''
                        .'<input type="checkbox" name="label-filter['.$label['id'].']" id="label-filter-'.$label['id'].'"'.(self::isLabelFiltered($label['id']) ? ' checked="checked"' : '').' />'
                        .'<label for="label-filter-'.$label['id'].'">'.$label['name'].'</label>';
                }
            
                $returnTmp .= ''
                .'<div class="article-mgm-label-filter gray-box">'
                    .'<form name="article-mgm-label-filter" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .parent::rb('label.edittitle').': '
                        .$options.' '
                        .'<input type="submit" name="filter-labels" value="'.parent::rb('lines.select').'" />'
                    .'</form>'
                .'</div>';
            }
            
            $sql = '';
            $countSql = '';
            if(!parent::session()->exists('filter-labels', 'article')) {
                $sql = 'SELECT `id` FROM `article` WHERE `line_id` = ' . $lineId;
                $countSql = 'SELECT count(`id`) as `id` FROM `article` WHERE `line_id` = ' . $lineId . ' order by `order` desc';
            } else {
                $sql = 'SELECT `id` FROM `article` left join `article_attached_label` on `article`.`id` = `article_attached_label`.`article_id` WHERE `line_id` = ' . $lineId . ' and `label_id` in ('.self::filteredLabelsSql().')';
                $countSql = 'SELECT count(`id`) as `id` FROM `article` left join `article_attached_label` on `article`.`id` = `article_attached_label`.`article_id` WHERE `line_id` = ' . $lineId . ' and `label_id` in ('.self::filteredLabelsSql().') order by `order` desc';
            }
            
            
            $articles = $dbObject->fetchAll($sql . " order by `order` desc".($pageable ? ' limit '.(self::getArticlePage() * self::getArticlePageSize()).','.self::getArticlePageSize() : '').";");
            if (count($articles) > 0) {
                $returnTmp .= ''
                    . '<table class="article-mgm-table">'
                        . '<thead>'
                            . '<tr class="article-mgm-tr article-mgm-tr-head">'
                                . '<th class="article-mgm-th article-mgm-id">' . $rb->get('articles.id') . ':</th>'
                                . '<th class="article-mgm-th article-mgm-lang">' . $rb->get('articles.lang') . ':</th>'
                                . '<th class="article-mgm-th article-mgm-head">' . $rb->get('articles.head') . '</th>'
                                . '<th class="article-mgm-th article-mgm-edit">' . $rb->get('articles.action') . '</th>'
                            . '</tr>'
                        . '</thead>'
                        . '<tbody>';
                foreach ($articles as $article) {
                    $infos = $dbObject->fetchAll("SELECT `article_content`.`name`, `article_content`.`head`, `language`.`id` AS `lang_id`, `language`.`language` FROM `article_content` LEFT JOIN `language` ON `article_content`.`language_id` = `language`.`id` WHERE `article_content`.`article_id` = " . $article['id'] . " ORDER BY `language`.`language`;");
                    $lnVersions = count($infos);
                    $first = true;
                    foreach ($infos as $info) {
                        $returnTmp .= ''
                        . '<tr class="article-mgm-tr' . (($first) ? ' article-mgm-first' : '') . '">'
                            . (($first) ? ''
                                . '<td rowspan="' . $lnVersions . '" class="article-mgm-td article-mgm-id">'
                                    . '<span>' . $article['id'] . '</span>'
                                    . '<div class="clear"></div>'
                                    . '<form name="article-add-lang1" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                        . '<input type="hidden" name="article-id" value="' . $article['id'] . '" />'
                                        . '<input type="hidden" name="line-id" value="' . $lineId . '" />'
                                        . '<input type="hidden" name="article-add-lang" value="' . $rb->get('articles.addlang') . '" />'
                                        . '<input type="image" src="~/images/lang_add.png" name="article-add-lang" value="' . $rb->get('articles.addlang') . '" title="' . $rb->get('articles.addlangcap') . '" />'
                                    . '</form>'
                                    . '<form name="article-move-up" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                        . '<input type="hidden" name="article-id" value="' . $article['id'] . '" />'
                                        . '<input type="hidden" name="line-id" value="' . $lineId . '" />'
                                        . '<input type="hidden" name="article-move-up" value="' . $rb->get('articles.moveup') . '" />'
                                        . '<input type="image" src="~/images/arro_up.png" name="article-move-up" value="' . $rb->get('articles.moveup') . '" title="' . $rb->get('articles.moveupcap') . '" /> '
                                    . '</form>'
                                    . '<div class="clear"></div>'
                                    . '<form name="article-add-lang2" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                        . '<input type="hidden" name="article-id" value="' . $article['id'] . '" />'
                                        . '<input type="hidden" name="line-id" value="' . $lineId . '" />'
                                        . '<input type="hidden" name="article-delete" value="' . $rb->get('articles.delete') . '" />'
                                        . '<input type="image" src="~/images/page_del.png" class="confirm" name="article-delete" value="' . $rb->get('articles.delete') . '" title="' . $rb->get('articles.deletecap') . ', id(' . $article['id'] . ')" >'
                                    . '</form>'
                                    . '<form name="article-move-down" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                        . '<input type="hidden" name="article-id" value="' . $article['id'] . '" />'
                                        . '<input type="hidden" name="line-id" value="' . $lineId . '" />'
                                        . '<input type="hidden" name="article-move-down" value="' . $rb->get('articles.movedown') . '" />'
                                        . '<input type="image" src="~/images/arro_do.png" name="article-move-down" value="' . $rb->get('articles.movedown') . '" title="' . $rb->get('articles.movedowncap') . '" /> '
                                    . '</form>'
                                . '</td>' : '')
                                . '<td class="article-mgm-td article-mgm-lang">'
                                    . $info['language']
                                . '</td>'
                                . '<td class="article-mgm-td article-mgm-head">'
                                    . '<div class="article-head-cover">'
                                        . '<span class="article-head-in">'
                                            . '<span style="color: black;">'.htmlspecialchars($info['name']) . '</span> - ' . htmlspecialchars($info['head'])
                                        . '</span>'
                                    . '</div>'
                                . '</td>'
                                . '<td class="article-mgm-td article-mgm-edit">'
                                    . '<form name="article-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                        . '<input type="hidden" name="article-id" value="' . $article['id'] . '" />'
                                        . '<input type="hidden" name="language-id" value="' . $info['lang_id'] . '" />'
                                        . '<input type="hidden" name="line-id" value="' . $lineId . '" />'
                                        . '<input type="hidden" name="article-edit" value="' . $rb->get('articles.edit') . '" />'
                                        . '<input type="image" src="~/images/page_edi.png" name="article-edit" value="' . $rb->get('articles.edit') . '" title="' . $rb->get('articles.editcap') . '" /> '
                                    . '</form>'
                                    . '<form name="article-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                        . '<input type="hidden" name="article-id" value="' . $article['id'] . '" />'
                                        . '<input type="hidden" name="language-id" value="' . $info['lang_id'] . '" />'
                                        . '<input type="hidden" name="line-id" value="' . $lineId . '" />'
                                        . '<input type="hidden" name="article-delete-lang" value="' . $rb->get('articles.deletelang') . '" />'
                                        . '<input type="image" src="~/images/lang_del.png" class="confirm" name="article-delete-lang" value="' . $rb->get('articles.deletelang') . '" title="' . $rb->get('articles.deletelangcap') . ', id(' . $article['id'] . ')" />'
                                    . '</form>'
                                . '</td>'
                            . '</tr>';
                        $first = false;
                    }
                }
                $returnTmp .= ''
                        . '</tbody>'
                    . '</table>'
                . '</div>';
            } else {
                $returnTmp .= '<div class="no-articles"><h4 class="error">' . $rb->get('articled.noinline') . '</h4></div>';
            }
            
            if($pageable) {
                $total = $dbObject->fetchSingle($countSql);
                $paging = self::getPaging($total['id'], self::getArticlePageSize(), self::getArticlePage(), $_SERVER['REQUEST_URI'], 0);
                
                if(strlen($paging) > 0) {
                    $returnTmp .= '<div class="gray-box">' . $paging . '</div>';
                }
            }

            if ($newArticleButton == 'true') {
                $returnTmp .= self::createArticle($lineId, $detailPageId, $method, "false", false);
            }

            if ($useFrames != "false") {
                return parent::getFrame($rb->get('articles.inlinetitle'), $returnTmp, '');
            } else {
                return $return;
            }
        }
        
        private function filteredLabelsSql() {
            if(!parent::session()->exists('filter-labels', 'article')) {
                return '';
            }
            
            $result = '';
            foreach(parent::session()->get('filter-labels', 'article') as $label) {
                if(strlen($result) != '') {
                    $result .= ', ';
                }
                $result .= $label;
            }
            return $result;
        }
        
        private function isLabelFiltered($labelId) {
            if(!parent::session()->exists('filter-labels', 'article')) {
                return true;
            }
        
            foreach(parent::session()->get('filter-labels', 'article') as $label) {
                if($label == $labelId) {
                    return true;
                }
            }
            
            return false;
        }
        
        private function getArticlePageSize() {
            return parent::getUserProperty('Article.pageSize', Article::$ArticlePageSize);
        }

        /**
         *
         * 	Create article form
         * 	C tag
         *
         * 	@param	lineId				article line id
         * 	@param	detailPageId	page id for next page
         * 	@param	method				method of passing arguments
         * 	@param	useFrames			use frames in output
         * 	@param	showError			show errors in output
         * 	@return	form for redirect to page with edit article
         *
         */
        public function createArticle($lineId = false, $detailPageId = false, $method = false, $useFrames = false, $showError = false) {
            global $dbObject;
            global $loginObject;
            global $webObject;
            $return = '';
            $actionUrl = $_SERVER['REQUEST_URI'];
            $rb = self::rb();

            if ($lineId == false) {
                if ($method == "get" || $method == "post") {
                    $lineId = $_REQUEST['line-id'];
                } elseif ($method == "session") {
                    $lineId = $_SESSION['article-line-id'];
                } else {
                    if ($useFrames != 'false') {
                        return parent::getFrame('Message', '<h4 class="error">' . $rb->get('articles.nolineselected') . '</h4>', '');
                    } else {
                        return '<h4 class="error">' . $rb->get('articles.nolineselected') . '</h4>';
                    }
                }
            }
            
                    if ($detailPageId != false) {
                        $actionUrl = $webObject->composeUrl($detailPageId);
                    }

            if($_POST['article-new'] == $rb->get('articles.newcap')) {
                $url = parent::addUrlParameter($actionUrl, 'line-id', $lineId);
                parent::web()->redirectTo($url);
                return;
            }

            if (self::canUser($lineId, WEB_R_WRITE)) {
                $return .= ''
                . '<div class="article-new gray-box">'
                    . '<form name="article-new" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<input type="submit" name="article-new" value="' . $rb->get('articles.newcap') . '" />'
                    . '</form>'
                . '</div>';
            } else {
                if ($showError != 'false') {
                    $return .= parent::getError(parent::rb('articles.selectline'));
                }
            }

            if ($useFrames != "false") {
                return parent::getFrame($rb->get('articles.newtitle'), $return, "");
            } else {
                return $return;
            }
        }

        private function getLinesWithWriteRight() {
            $lines = parent::db()->fetchAll('SELECT distinct `article_line`.`id`, `article_line`.`name`, `article_line`.`url` FROM `article_line` LEFT JOIN `article_line_right` ON `article_line`.`id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`gid` IN (' . implode(',', RoleHelper::getCurrentRoles()) . ') ORDER BY `id`;');
            return $lines;
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
        public function showLines($editable = false, $detailPageId = false, $useFrames = false, $newLineButton = false) {
            global $dbObject;
            global $webObject;
            global $loginObject;
            $return = '';
            $actionUrl = '';
            $rb = self::rb();

            if ($_POST['article-line-delete'] == $rb->get('lines.delete')) {
                $lineId = $_POST['delete-line-id'];
                // test na prava pro delete!
                if (self::canUser($lineId, WEB_R_DELETE)) {
                    $pages = $dbObject->fetchAll('SELECT `id` FROM `article` WHERE `line_id` = ' . $lineId . ';');
                    if (count($pages) == 0) {
                        $dbObject->execute('DELETE FROM `article_line_right` WHERE `line_id` = ' . $lineId . ';');
                        $dbObject->execute('DELETE FROM `article_line` WHERE `id` = ' . $lineId . ';');

                        $return .= '<h4 class="success">Article line deleted!</h4>';
                    } else {
                        $return .= '<h4 class="error">can\'t delete article line, still exists articles in this line!</h4>';
                    }
                } else {
                    $return .= '<h4 class="error">Permission Denied!</h4>';
                }
            }

            if ($detailPageId != false) {
                $actionUrl = $webObject->composeUrl($detailPageId);
            }

            $lines = self::getLinesWithWriteRight();
            if (count($lines) > 0) {
                $return .= ''
                        . '<div class="show-lines standart clickable"> '
                        . '<table>'
                        . '<thead>'
                        . '<tr>'
                        . '<th class="show-lines-id">' . $rb->get('articles.id') . ':</th>'
                        . '<th class="show-lines-name">' . $rb->get('articles.name') . ':</th>'
                        . '<th class="show-lines-name">' . $rb->get('articles.url') . ':</th>'
                        . '<th class="show-lines-edit"></th>'
                        . '</tr>'
                        . '</thead>'
                        . '<tbody>';
                $i = 1;
                foreach ($lines as $line) {
                    $artcs = $dbObject->fetchAll('SELECT `id` FROM `article` WHERE `line_id` = ' . $line['id'] . ';');
                    $return .= ''
                            . '<tr class="' . ((($i % 2) == 0) ? 'even' : 'idle') . '">'
                            . '<td class="article-lines-id">'
                            . $line['id']
                            . '</td>'
                            . '<td class="article-lines-name">'
                            . $line['name']
                            . '</td>'
                            . '<td class="article-lines-url">'
                            . $line['url']
                            . '</td>'
                            . (($editable == "true") ? ''
                                    . '<td>'
                                    . '<form name="article-line-edit" method="post" action="' . $actionUrl . '">'
                                    . '<input type="hidden" name="edit-line-id" value="' . $line['id'] . '" />'
                                    . '<input type="hidden" name="article-line-edit" value="' . $rb->get('lines.edit') . '" />'
                                    . '<input type="image" src="~/images/page_edi.png" name="article-line-edit" value="' . $rb->get('lines.edit') . '" title="' . $rb->get('lines.editcap') . '" />'
                                    . '</form> '
                                    . ((count($artcs) == 0) ? ''
                                            . '<form name="article-line-delete" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                            . '<input type="hidden" name="delete-line-id" value="' . $line['id'] . '" />'
                                            . '<input type="hidden" name="article-line-delete" value="' . $rb->get('lines.delete') . '" />'
                                            . '<input class="confirm" type="image" src="~/images/page_del.png" name="article-line-delete" value="' . $rb->get('lines.delete') . '" title="' . $rb->get('lines.deletecap') . ', id(' . $line['id'] . ')" />'
                                            . '</form>' : '')
                                    . '</td>' : '')
                            . '</tr>';
                    $i++;
                }
                $return .= ''
                        . '</tbody>'
                        . '</table>'
                        . '</div>';
            } else {
                $return .= '<h4 class="error">' . $rb->get('lines.nolines') . '</h4>';
            }

            if ($newLineButton == 'true') {
                $return .= ''
                        . '<hr />'
                        . self::createLine($detailPageId, "false");
            }

            if ($useFrames != "false") {
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
         * 	@param	showError			show errors in output
         * 	@param	useFrames			use frames in output
         *  @return form for selecting line id
         *
         */
        public function setLine($method = false, $hideWhenOnlyOne = false, $showError = false, $useFrames = false) {
            global $dbObject;
            global $loginObject;
            $return = '';
            $rb = self::rb();

            if ($_POST['select-article-line'] == $rb->get('lines.select')) {
                $lineId = $_POST['line-id'];
                // test na prava pro zapis do rady clanku.
                if (self::canUser($lineId, WEB_R_WRITE)) {
                    if ($method == 'session') {
                        $_SESSION['article-line-id'] = $lineId;
                    }
                } else {
                    if ($showError != 'false') {
                        $return .= '<h4 class="error">Permission Denied!</h4>';
                    }
                }
            }

            $actualLineId = -1;
            if ($method == 'get' || $method == 'post') {
                $actualiLineId = $_REQUEST['line-id'];
            } elseif ($method == 'session') {
                $actualiLineId = $_SESSION['article-line-id'];
            }

            $lines = $dbObject->fetchAll('SELECT distinct `article_line`.`id`, `article_line`.`name` FROM `article_line` LEFT JOIN `article_line_right` ON `article_line`.`id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`gid` IN (' . implode(',', RoleHelper::getCurrentRoles()) . ') ORDER BY `id`;');
            if (count($lines) > 0) {
                if(count($lines) == 1) {
                    $_SESSION['article-line-id'] = $lines[0]['id'];
                }
            
                $return .= ''
                        . '<div class="gray-box">'
                        . '<form name="article-select-line" method="' . (($method == "get") ? 'get' : 'post') . '" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<label for="select-line" class="padded">' . $rb->get('lines.selectcap') . ': </label>'
                        . '<select id="select-line" name="line-id" class="w200">';
                foreach ($lines as $line) {
                    $return .= '<option value="' . $line['id'] . '"' . (($actualiLineId == $line['id']) ? ' selected="selected"' : '') . '>' . $line['name'] . '</option>';
                }
                $return .= ''
                        . '</select> '
                        . '<input type="submit"' . (($method == "get") ? '' : ' name="select-article-line"') . ' value="' . $rb->get('lines.select') . '" />'
                        . '</form>'
                        . '</div>';
            } else {
                if ($showError != 'false') {
                    $return .= '<h4 class="error">' . $rb->get('lines.nolines') . '</h4>';
                }
            }
            
            if(count($lines) == 1 && $hideWhenOnlyOne == 'true') {
                return;
            }

            if ($useFrames != "false") {
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
         * 	@param	detailPageId				page id for next page
         * 	@param	useFrames						use frames in output
         * 	@return	form for creating article line
         *
         */
        public function createLine($detailPageId = false, $useFrames = false) {
            global $webObject;
            $return = '';
            $actionUrl = $_SERVER['REQUEST_URI'];
            $rb = self::rb();

            if ($detailPageId != false) {
                $actionUrl = $webObject->composeUrl($detailPageId);
            }

            $return .= ''
                    . '<div class="gray-box">'
                    . '<form name="create-article-line" method="post" action="' . $actionUrl . '">'
                    . '<input type="submit" name="article-line-create-submit" value="' . $rb->get('lines.new') . '" title="' . $rb->get('lines.newcap') . '" />'
                    . '</form>'
                    . '</div>';

            if ($useFrames != "false") {
                return parent::getFrame($rb->get('lines.newtitle'), $return, '');
            } else {
                return $return;
            }
        }

        /**
         *
         * 	Generates artcile edit form.
         * 	C tag.
         *
         */
        public function showEditForm($useFrames = false, $submitPageId = false, $backPageId = false, $lineId = false, $customFormId = '', $customFormTemplateId = '', $supportedLanguageId) {
            global $dbObject;
            global $webObject;
            global $loginObject;

            $return = '';
            $actionUrl = parent::redirectUrlWithQueryString();

            $article = array();
            $articleContent = array();
            $usedLangs = array();
            $rb = self::rb();

            $isClosing = $_POST['article-save-close'] == $rb->get('articles.saveandclose') || $_POST['article-close'] == $rb->get('articles.close');
            $hasCustomForm = $customFormId != '' && $customFormTemplateId != '';
            if ($hasCustomForm) {
                parent::php()->autoRegisterPrefix('cf');
                global $cfObject;
            }

            if ($_POST['article-save'] == $rb->get('articles.save') || $_POST['article-save-close'] == $rb->get('articles.saveandclose')) {
                $article = array('id' => $_POST['article-id'], 'line_id' => $_POST['line-id'], 'visible' => $_POST['article-visible'], 'order' => $_POST['article-id'], 'labels' => $_POST['article-labels']);
                $articleContent = array(
                    'article_id' => $_POST['article-id'], 
                    'name' => $_POST['article-name'], 
                    'head' => $_POST['article-head'], 
                    'content' => $_POST['article-content'], 
                    'author' => $_POST['article-author'], 
                    'timestamp' => time(), 
                    'datetime' => $_POST['article-datetime'], 
                    'language_id' => $_POST['language-id'], 
                    'language_old_id' => $_POST['article-old-lang-id'], 
                    'line_old_id' => $_POST['line-old-id'], 
                    'url' => $_POST['article-url'], 
                    'keywords' => $_POST['article-keywords']
                );

                if (trim($articleContent['datetime']) == '') {
                    $articleContent['datetime'] = date("j.n.Y", time());
                }

                if (trim($articleContent['url']) == '') {
                    $articleContent['url'] = $articleContent['name'];
                }

                //parent::db()->setMockMode(true);
                if(strpos($articleContent['url'], '://') === false) {
                    $articleContent['url'] = strtolower(parent::convertToUrlValid($articleContent['url'], false));
                }

                $idSql = '';
                if ($article['id'] != '') {
                    $idSql = ' and `article_id` = ' . $article['id'];
                }
                $langSql = '';
                if ($articleContent['language_id'] != '') {
                    $langSql = ' and `language_id` = ' . $articleContent['language_id'];
                }

                $isRedirectRequired = false;
                $isSaved = false;
                $urls = parent::db()->fetchAll('select `article_id` from `article_content` left join `article` on `article_content`.`article_id` = `article`.`id` where `url` = "' . $articleContent['url'] . '" and `line_id` = ' . $article['line_id'] . $idSql . $langId . ';');
                if (count($urls) == 0 || (count($urls) == 1 && $urls[0]['article_id'] == $article['id'])) {
                    $permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = ' . $article['line_id'] . ' AND `article_line_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value` DESC;');
                    if (self::canUser($article['line_id'], WEB_R_WRITE)) {
                        $permission = $dbObject->fetchAll('SELECT `value` FROM `article_line_right` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`line_id` = ' . $articleContent['line_old_id'] . ' AND `article_line_right`.`type` = ' . WEB_R_WRITE . ' ORDER BY `value` DESC;');
                        if (self::canUser($articleContent['line_old_id'], WEB_R_WRITE)) {
                            if ($articleContent['language_old_id'] == "") {
                                $artc = array();
                            } else {
                                $artc = $article['id'] == '' ? array() : $dbObject->fetchAll("SELECT `article_id` FROM `article_content` WHERE `article_id` = " . $article['id'] . " AND `language_id` = " . $articleContent['language_old_id'] . ";");
                            }
                            if (count($artc) == 0) {
                                $artc = $article['id'] == '' ? array() : $dbObject->fetchAll("SELECT `article_id` FROM `article_content` WHERE `article_id` = " . $article['id'] . ";");
                                if (count($artc) == 0) {
                                    $ac = $articleContent;
                                    $maxId = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `article`;');
                                    $article['id'] = $ac['article_id'] = $maxId[0]['id'] + 1;
                                    // Ulozeni - NOVY clanek
                                    $maxOrder = parent::db()->fetchSingle('select `order` from `article` order by `order` desc limit 1');
                                    $maxOrder['order']++;
                                    $dbObject->execute("INSERT INTO `article`(`id`, `line_id`, `order`, `visible`) VALUES (" . $article['id'] . ", " . $article['line_id'] . ", " . $maxOrder['order'] . ", " . $article['visible'] . ");");
                                    $dbObject->execute("INSERT INTO `article_content`(`article_id`, `name`, `url`, `keywords`, `head`, `content`, `author`, `timestamp`, `datetime`, `language_id`) VALUES (" . $ac['article_id'] . ", \"" . $dbObject->escape($ac['name']) . "\", \"" . $dbObject->escape($ac['url']) . "\", \"" . $dbObject->escape($ac['keywords']) . "\", \"" . $dbObject->escape($ac['head']) . "\", \"" . $dbObject->escape($ac['content']) . "\", \"" . $dbObject->escape($ac['author']) . "\", " . $ac['timestamp'] . ", \"" . $dbObject->escape($ac['datetime']) . "\", " . $ac['language_id'] . ");");
                                    $return .= '<h4 class="success">' . $rb->get('articles.newcreated') . '</h4>';
                                    $_POST['article-id'] = $article['id'];
                                    $_POST['language-id'] = $ac['language_id'];

                                    $line = $dbObject->fetchSingle("SELECT `parentdirectory_id` from `article_line` where `id` = " . $article['line_id'] . ";");
                                    if($line['parentdirectory_id'] != '') {
                                        $fa = new FileAdmin();
                                        $directoryName = $article['id'] . " - " . $ac['name'];
                                        $directory = $fa->createDirectory($line['parentdirectory_id'], $directoryName);
                                        if($directory['id'] != '') {
                                            $dbObject->execute("UPDATE `article` SET `directory_id` = " . $directory['id'] . " WHERE `id` = " . $article['id'] . ";");
                                        }
                                    }
                                    $isRedirectRequired = true;
                                    $isSaved = true;
                                } else {
                                    $ac = $articleContent;
                                    // Ulozeni - NOVA jaz.verze
                                    $dbObject->execute("INSERT INTO `article_content`(`article_id`, `name`, `url`, `keywords`, `head`, `content`, `author`, `timestamp`, `datetime`, `language_id`) VALUES (" . $ac['article_id'] . ", \"" . $dbObject->escape($ac['name']) . "\", \"" . $dbObject->escape($ac['url']) . "\", \"" . $dbObject->escape($ac['keywords']) . "\", \"" . $dbObject->escape($ac['head']) . "\", \"" . $dbObject->escape($ac['content']) . "\", \"" . $dbObject->escape($ac['author']) . "\", " . $ac['timestamp'] . ", \"" . $dbObject->escape($ac['datetime']) . "\", " . $ac['language_id'] . ");");
                                    $return .= '<h4 class="success">' . $rb->get('articles.langadded') . '</h4>';
                                    $_POST['article-id'] = $article['id'];
                                    $_POST['language-id'] = $ac['language_id'];
                                    $isRedirectRequired = true;
                                    $isSaved = true;
                                }
                            } else {
                                $ac = $articleContent;
                                $dbObject->execute("UPDATE `article` SET `line_id` = " . $article['line_id'] . ", `visible`= " . $article['visible'] . " WHERE `id` = " . $article['id'] . ";");
                                $dbObject->execute("UPDATE `article_content` SET `name` = \"" . $dbObject->escape($ac['name']) . "\", `url` = \"" . $dbObject->escape($ac['url']) . "\", `keywords` = \"" . $dbObject->escape($ac['keywords']) . "\", `head` = \"" . $dbObject->escape($ac['head']) . "\", `content` = \"" . $dbObject->escape($ac['content']) . "\", `author` = \"" . $dbObject->escape($ac['author']) . "\", `timestamp` = " . $ac['timestamp'] . ", `datetime` = \"" . $dbObject->escape($ac['datetime']) . "\", `language_id` = " . $ac['language_id'] . " WHERE `article_id` = " . $ac['article_id'] . " AND `language_id` = " . $ac['language_old_id'] . ";");
                                $_POST['article-id'] = $article['id'];
                                $_POST['language-id'] = $ac['language_id'];
                                $isRedirectRequired = $ac['language_id'] != $ac['language_old_id'];
                                $isSaved = true;
                                $return .= '<h4 class="success">' . $rb->get('articles.updated') . '</h4>';
                            }

                            parent::db()->execute('delete from `article_attached_label` where `article_id` = ' . $article['id'] . ';');
                            foreach ($article['labels'] as $label) {
                                parent::db()->execute('insert into `article_attached_label`(`article_id`, `label_id`) values (' . $article['id'] . ', ' . $label . ');');
                            }
                        }
                    } else {
                        $return .= parent::getError(parent::rb('articles.noperm'));
                    }
                } else {
                    $return .= parent::getError($rb->get('articles.notuniqueurl'));
                }
                //parent::db()->setMockMode(false);
            }

            if (array_key_exists('article-id', $_REQUEST) && $_REQUEST['article-id'] != '') {
                $articleId = $_REQUEST['article-id'];
                $languageId = $_REQUEST['language-id'];
            } else if (array_key_exists('article-id', $_POST) && $_POST['article-id'] != '') {
                $articleId = $_POST['article-id'];
                $languageId = $_POST['language-id'];
            }

            $cfContent = '';
            if ((!$isClosing || $isSaved) && $hasCustomForm) {
                parent::web()->setIsInsideForm(true);
                $additionalKeys = array('id' => $articleId, 'language_id' => $languageId);
                $cfContent .= $cfObject->form($customFormId, $customFormTemplateId, 'db', false, $additionalKeys);
                parent::web()->setIsInsideForm(false);
            }

            if ($isClosing) {
                $url = $webObject->composeUrl($backPageId);
                $url = parent::addUrlQueryString($url);
                $url = parent::removeUrlParameter($url, 'article-id');
                $url = parent::removeUrlParameter($url, 'language-id');

                parent::web()->redirectTo($url);
            } else if($isRedirectRequired) {
                $url = $_SERVER['REQUEST_URI'];
                $url = parent::addUrlParameter($url, 'article-id', $article['id']);
                $url = parent::addUrlParameter($url, 'language-id', $ac['language_id']);
                $url = parent::removeUrlParameter($url, 'line-id');
                
                parent::web()->redirectTo($url);
                return;
            }

            if (array_key_exists('article-id', $_REQUEST) && $_REQUEST['article-id'] != '') {
                $articleId = $_REQUEST['article-id'];
                $languageId = $_REQUEST['language-id'];

                if (array_key_exists('language-id', $_REQUEST)) {
                    // test na prava pro cteni z prislusne rady!
                    $article = $dbObject->fetchAll('SELECT `article_content`.`article_id`, `article_content`.`language_id`, `article_content`.`name`, `article_content`.`url`, `article_content`.`keywords`, `article_content`.`head`, `article_content`.`content`, `article_content`.`author`, `article_content`.`datetime`, `article`.`line_id`, `article`.`visible` FROM `article_content` LEFT JOIN `article` ON `article_content`.`article_id` = `article`.`id` LEFT JOIN `article_line_right` ON `article`.`line_id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`gid` IN (' . implode(',', RoleHelper::getCurrentRoles()) . ') AND `article_content`.`article_id` = ' . $articleId . ' AND `article_content`.`language_id` = ' . $languageId . ' ORDER BY `id`;');
                    if (count($article) != 0) {
                        $article = $article[0];
                    } else {
                        $return .= parent::getError($rb->get('articles.notselected'));
                        if ($useFrames != "false") {
                            return parent::getFrame($rb->get('articles.edittitle'), $return, '');
                        } else {
                            return $return;
                        }
                    }
                    $new = false;
                } elseif ($articleId != '') {
                    $article['article_id'] = $articleId;
                    $articleId = $articleId;
                    $new = true;
                } else {
                    $new = true;
                }
                $usedLangs = $dbObject->fetchAll("SELECT `language_id` FROM `article_content` WHERE `article_id` = " . $article['article_id'] . ";");
            } else {
                $new = true;
                $userLangs = array();
                $article = array_merge($article, $articleContent);
                if (!array_key_exists('visible', $article)) {
                    $article['visible'] = 2;
                    $article['author'] = parent::getUserProperty('Article.author', '');
                    $article['language_id'] = parent::getUserProperty('Article.languageId', '1');
                    $article['datetime'] = date("j.n.Y", time());
                }
            }

            $langSqlWhere = '';
            if(strlen($supportedLanguageId) > 0) {
                $langSqlWhere = ' WHERE `id` in (' . $supportedLanguageId . ')';
            }
            $langs = $dbObject->fetchAll("SELECT `id`, `language` FROM `language`" . $langSqlWhere . " ORDER BY `language`;");

            $langSelect = '<select id="language-id" name="language-id">';
            foreach ($langs as $lang) {
                $ok = true;
                foreach ($usedLangs as $usedLang) {
                    if (in_array($lang['id'], $usedLang)) {
                        $ok = false;
                    }
                    if (($lang['id'] == $article['language_id'])) {
                        $ok = true;
                    }
                }
                if ($ok) {
                    $langSelect .= '<option value="' . $lang['id'] . '"' . (($lang['id'] == $article['language_id']) ? ' selected="selected"' : '') . '>' . $lang['language'] . '</option>';
                }
            }
            $langSelect .= '</select>';

            if ($article['line_id'] == '') {
                if ($lineId != false) {
                    $article['line_id'] = $lineId;
                } else if (array_key_exists('article-line-id', $_SESSION)) {
                    $article['line_id'] = $_SESSION['article-line-id'];
                } elseif (array_key_exists('line-id', $_REQUEST)) {
                    $article['line_id'] = $_REQUEST['line-id'];
                }
            }

            // Testovat prava zapisu do rady!!!
            $lines = $dbObject->fetchAll('SELECT DISTINCT `article_line`.`id`, `article_line`.`name` FROM `article_line` LEFT JOIN `article_line_right` ON `article_line`.`id` = `article_line_right`.`line_id` LEFT JOIN `group` ON `article_line_right`.`gid` = `group`.`gid` WHERE `article_line_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`gid` IN (' . implode(',', RoleHelper::getCurrentRoles()) . ');');
            $lineSelect = '<select id="line-id" name="line-id" class="w160">';
            foreach ($lines as $line) {
                $lineSelect .= '<option value="' . $line['id'] . '"' . (($line['id'] == $article['line_id']) ? ' selected="selected"' : '') . '>' . $line['name'] . '</option>';
            }
            $lineSelect .= '</select>';

            if ($submitPageId != false) {
                $actionUrl = $webObject->composeUrl($submitPageId);
                $actionUrl = parent::addUrlParameter($actionUrl, 'article-id', $article['article_id']);
                $actionUrl = parent::addUrlParameter($actionUrl, 'language-id', $article['language_id']);
            }

            $labelList = '';
            $labels = parent::db()->fetchAll('select `id`, `name` from `article_label` join `article_line_label` on `article_label`.`id` = `article_line_label`.`label_id` where `line_id` = ' . $article['line_id'] . ' order by `name`');
            if($articleId != '') {
                $usedLabels = parent::db()->fetchAll('select `label_id` from `article_attached_label` where `article_id` = ' . $articleId . ';');
            } else {
                $usedLabels = array();
            }
            foreach ($labels as $label) {
                $used = false;
                foreach ($usedLabels as $ul) {
                    if ($label['id'] == $ul['label_id']) {
                        $used = true;
                        break;
                    }
                }
                $labelList .= ''
                        . '<input type="checkbox" name="article-labels[]" id="article-labels-' . $label['id'] . '" ' . ($used ? ' checked="checked"' : '') . ' value="' . $label['id'] . '" />'
                        . '<label for="article-labels-' . $label['id'] . '">' . $label['name'] . '</label> ';
            }

            $name = 'Article.editors';
            $propertyEditors = parent::system()->getPropertyValue($name);
            $editAreaContentRows = parent::system()->getPropertyValue('Article.editAreaContentRows');
            $editAreaHeadRows = parent::system()->getPropertyValue('Article.editAreaHeadRows');

            $return .= ''
            . '<div class="article-mgm-edit">'
                . '<form name="article-edit" method="post" action="' . $actionUrl . '">'
                    . '<div class="article-prop">'
                        . '<div class="article-name gray-box-float">'
                            . '<label for="article-name" class="w60">' . $rb->get('articles.name') . ':</label> '
                            . '<input type="text" id="article-name" name="article-name" value="' . $article['name'] . '" class="w300" />'
                        . '</div>'
                        . '<div class="article-line gray-box-float">'
                            . '<label for="line-id" class="padded">' . $rb->get('articles.lines') . ':</label> '
                            . $lineSelect
                        . '</div>'
                        . '<div class="article-lang gray-box-float">'
                            . '<label for="language-id" class="padded">' . $rb->get('articles.lang') . ':</label> '
                            . $langSelect
                        . '</div>'
                        . '<div class="article-author gray-box-float">'
                            . '<label for="article-author" class="padded">' . $rb->get('articles.author') . ':</label> '
                            . '<input type="text" id="article-author" name="article-author" value="' . $article['author'] . '" class="w200" />'
                        . '</div>'
                        . '<div class="clear"></div>'
                    . '</div>'
                    . '<div class="gray-box-float">'
                        . '<label for="article-url" class="w60" title="' . $rb->get('articles.url-title') . '">' . $rb->get('articles.url') . ':</label> '
                        . '<input type="text" class="long-input" name="article-url" id="article-url" value="' . $article['url'] . '" />'
                    . '</div>'
                    . '<div class="gray-box-float">'
                        . '<label for="article-visible" class="w80">' . $rb->get('articles.visible') . ':</label> '
                        . '<select name="article-visible" id="article-visible">'
                            . '<option' . ($article['visible'] == 0 ? ' selected="selected"' : '') . ' value="0">' . $rb->get('articles.visible.0') . '</option>'
                            . '<option' . ($article['visible'] == 1 ? ' selected="selected"' : '') . ' value="1">' . $rb->get('articles.visible.1') . '</option>'
                            . '<option' . ($article['visible'] == 2 ? ' selected="selected"' : '') . ' value="2">' . $rb->get('articles.visible.2') . '</option>'
                        . '</select>'
                    . '</div>'
                    . '<div class="clear"></div>'
                    . '<div class="gray-box-float">'
                        . '<label for="article-keywords" class="w60">' . $rb->get('articles.keywords') . ':</label> '
                        . '<input type="text" class="long-input" name="article-keywords" id="article-keywords" value="' . $article['keywords'] . '" />'
                    . '</div>'
                    . '<div class="gray-box-float">'
                        . '<label for="article-datetime" class="w80">' . $rb->get('articles.datetime') . ':</label> '
                        . '<input type="text" class="w110" name="article-datetime" id="article-datetime" value="' . $article['datetime'] . '" />'
                    . '</div>'
                    . '<div class="clear"></div>'
                    . '<div class="gray-box">'
                        . '<label class="w60">' . $rb->get('label.edittitle') . ':</label>'
                        . $labelList
                        . '<span class="padded small-note">' . $rb->get('lines.labelnote') . '</span>'
                    . '</div>'
                    . '<div class="clear"></div>';
            
            $return .= $cfContent;

            if ($propertyEditors == 'edit_area') {
                $return .= ''
                        . '<div id="editors" class="editors edit-area-editors">'
                        . '<div id="editors-tab" class="editors-tab"></div>'
                        . ((self::getGroupPermCached('Article.Head')) ? ''
                        . '<div id="cover-article-head">'
                        . '<label for="article-head">' . $rb->get('articles.head2') . ':</label>'
                        . '<textarea id="article-head" class="edit-area html" name="article-head" rows="' . ($editAreaHeadRows > 0 ? $editAreaHeadRows : 10) . '">' . str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['head']))) . '</textarea>'
                        . '</div>' : '')
                        . ((self::getGroupPermCached('Article.Content')) ? ''
                        . '<div id="cover-article-content">'
                            . '<label for="article-content">' . $rb->get('articles.content2') . ':</label>'
                            . '<textarea id="article-content" class="edit-area html" name="article-content" rows="' . ($editAreaContentRows > 0 ? $editAreaContentRows : 20) . '">' . str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['content']))) . '</textarea>'
                        . '</div>' : '')
                        . '</div>';
            } elseif (($propertyEditors == 'tiny')) {
                $return .= ''
                        . ((self::getGroupPermCached('Article.Head')) ? ''
                        . '<div class="article-head">'
                        . '<label for="article-head">' . $rb->get('articles.head2') . ':</label> '
                        . '<div class="editor-cover">'
                        . '<div class="tiny-cover">'
                        . '<textarea id="article-head" name="article-head" class="" rows="' . ($editAreaHeadRows > 0 ? $editAreaHeadRows : 20) . '">' . str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['head']))) . '</textarea>'
                        . '</div>'
                        . '<div class="clear"></div>'
                        . '</div>'
                        . '</div>' : '')
                        . ((self::getGroupPermCached('Article.Content')) ? ''
                        . '<div class="article-content">'
                        . '<label for="article-content">' . $rb->get('articles.content2') . ':</label> '
                        . '<div class="editor-cover">'
                        . '<div class="tiny-cover">'
                        . '<textarea id="article-content" name="article-content" class="" rows="' . ($editAreaHeadRows > 0 ? $editAreaHeadRows : 20) . '">' . str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['content']))) . '</textarea>'
                        . '</div>'
                        . '<div class="clear"></div>'
                        . '</div>'
                        . '</div>' : '');

                $js = parent::autolib('js');
                $return .= $js->tinyMce("article-head", self::web()->LanguageName);
                $return .= $js->tinyMce("article-content", self::web()->LanguageName);
            } else {
                $return .= ''
                        . ((self::getGroupPermCached('Article.Head')) ? ''
                        . '<div class="article-head">'
                        . '<label for="article-head">' . $rb->get('articles.head2') . ':</label> '
                        . '<div class="editor-cover">'
                        . '<div class="textarea-cover">'
                        . '<textarea id="article-head" name="article-head" class="editor-textarea editor-closed editor-tiny" rows="5">' . str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['head']))) . '</textarea>'
                        . '</div>'
                        . '<div class="clear"></div>'
                        . '</div>'
                        . '</div>'
                        : '')
                        . ((self::getGroupPermCached('Article.Content')) ? ''
                        . '<div class="article-content">'
                        . '<label for="article-content">' . $rb->get('articles.content2') . ':</label> '
                        . '<div class="editor-cover">'
                        . '<div class="textarea-cover">'
                        . '<textarea id="article-content" name="article-content" class="editor-textarea editor-tiny" rows="15">' . str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $article['content']))) . '</textarea>'
                        . '</div>'
                        . '<div class="clear"></div>'
                        . '</div>'
                        . '</div>' : '');
            }

            $returnBack = '';
            if($backPageId != false) {
                $returnBack = ''
                . '<input type="submit" name="article-save-close" value="' . $rb->get('articles.saveandclose') . '" /> '
                . '<input type="submit" name="article-close" value="' . $rb->get('articles.close') . '" /> ';
            }

            $return .= ''
                    . '<div class="article-bottom">'
                    . '<div class="article-submit">'
                    . '<input type="hidden" name="article-id" value="' . $article['article_id'] . '" />'
                    . '<input type="hidden" name="line-old-id" value="' . (($new) ? $lines[0]['id'] : $article['line_id']) . '" />'
                    . '<input type="hidden" name="article-old-lang-id" value="' . $article['language_id'] . '" />'
                    . '<input type="submit" name="article-save" value="' . $rb->get('articles.save') . '" /> '
                    . $returnBack
                    . '</div>'
                    . '<div class="clear"></div>'
                    . '</div>'
                    . '</form>'
                    . '</div>';

            if ($useFrames != "false") {
                if ($new) {
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
         * 	Edit article line form
         * 	C tag
         * 	
         * 	@param	useFrames		use frames in output
         * 	@param	submitPageId	page id to submit for to		 		 		 		 
         * 	@return	edit article form
         *
         */
        public function showEditLineForm($useFrames = false, $submitPageId = false) {
            global $dbObject;
            global $webObject;
            global $loginObject;
            $return = '';
            $ok = true;
            $actionUrl = $_SERVER['REQUEST_URI'];
            $rb = self::rb();

            $lineId = ((array_key_exists('edit-line-id', $_POST)) ? $_POST['edit-line-id'] : 0);
            // test na prava zapisu do rady clanku
            if (self::canUser($lineId, WEB_R_WRITE)) {
                $ok = true;
            } else {
                if($lineId != 0) {
                    $return .= parent::getError($rb->get('articles.noperm'));
                }

                if ($useFrames != "false") {
                    return parent::getFrame($rb->get('lines.edittitle2'), $return, '');
                } else {
                    return $return;
                }
            }

            if ($_POST['article-line-edit-submit'] != $rb->get('lines.save') && $_POST['article-line-edit'] != $rb->get('lines.edit') && $_POST['article-line-create-submit'] != $rb->get('lines.new')) {
                $ok = false;
            }

            if ($ok) {
                if ($_POST['article-line-edit-submit'] == $rb->get('lines.save')) {
                    $name = $_POST['article-line-edit-name'];
                    $url = strtolower(parent::convertToValidUrl(strlen($_POST['article-line-edit-url']) == 0 ? $name : $_POST['article-line-edit-url']));
                    $parentDirectoryId = $_POST['article-line-edit-parentdirectoryid'];
                    $lineId = $_POST['article-line-edit-id'];
                    $read = $_POST['article-right-edit-groups-r'];
                    $write = $_POST['article-right-edit-groups-w'];
                    $delete = $_POST['article-right-edit-groups-d'];
                    $labels = $_POST['article-line-labels'];

                    $ok = true;
                    $urlOk = true;
                    if ($lineId != 0) {
                        $otherLines = parent::db()->fetchAll('select `name` from `article_line` where `url` = "' . $url . '" and `id` != ' . $lineId . ';');
                        if (count($otherLines) != 0) {
                            $ok = false;
                            $urlOk = false;
                        }
                    }

                    if (strlen($name) > 3 && strlen($url) > 0 && $ok) {
                        if($parentDirectoryId == '') {
                            $parentDirectoryId = 'NULL';
                        }

                        if ($lineId == 0) {
                            $dbObject->execute('INSERT INTO `article_line`(`name`, `url`, `parentdirectory_id`) VALUES ("' . $name . '", "' . $url . '", ' . $parentDirectoryId . ');');
                            $return .= '<h4 class="success">' . $rb->get('lines.created') . '</h4>';
                            $lineId = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `article_line`;');
                            $lineId = $lineId[0]['id'];
                        } else {
                            $dbObject->execute('UPDATE `article_line` SET `name` = "' . $name . '", `url` = "' . $url . '", `parentdirectory_id` = ' . $parentDirectoryId . ' WHERE `id` = ' . $lineId . ';');
                            $return .= '<h4 class="success">' . $rb->get('lines.updated') . '</h4>';
                        }

                        parent::db()->execute('delete from `article_line_label` where `line_id` = ' . $lineId . ';');
                        foreach ($labels as $label) {
                            parent::db()->execute('insert into `article_line_label`(`line_id`, `label_id`) values (' . $lineId . ', ' . $label . ');');
                        }


                        if (count($read) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `article_line_right`.`line_id` = " . $lineId . " AND `type` = " . WEB_R_READ . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right, $read)) {
                                    $dbObject->execute("DELETE FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_READ . ";");
                                }
                            }
                            foreach ($read as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_READ . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `article_line_right`(`line_id`, `gid`, `type`) VALUES (" . $lineId . ", " . $right . ", " . WEB_R_READ . ");");
                                }
                            }
                        }

                        if (count($write) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `article_line_right`.`line_id` = " . $lineId . " AND `type` = " . WEB_R_WRITE . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right, $write)) {
                                    $dbObject->execute("DELETE FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_WRITE . ";");
                                }
                            }
                            foreach ($write as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_WRITE . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `article_line_right`(`line_id`, `gid`, `type`) VALUES (" . $lineId . ", " . $right . ", " . WEB_R_WRITE . ");");
                                }
                            }
                        }

                        if (count($delete) != 0) {
                            $dbR = $dbObject->fetchAll('SELECT `gid` FROM `article_line_right` WHERE `article_line_right`.`line_id` = ' . $lineId . ' AND `type` = ' . WEB_R_DELETE . ';');
                            foreach ($dbR as $right) {
                                if (!in_array($right, $delete)) {
                                    $dbObject->execute("DELETE FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_DELETE . ";");
                                }
                            }
                            foreach ($delete as $right) {
                                $row = $dbObject->fetchAll('SELECT `gid` FROM `article_line_right` WHERE `line_id` = ' . $lineId . ' AND `type` = ' . WEB_R_DELETE . ' AND `gid` = ' . $right . ';');
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `article_line_right`(`line_id`, `gid`, `type`) VALUES (" . $lineId . ", " . $right . ", " . WEB_R_DELETE . ");");
                                }
                            }
                        }
                    } else {
                        if ($urlOk) {
                            $return .= parent::getError($rb->get('lines.invalidname'));
                        } else {
                            $return .= parent::getError($rb->get('lines.invalidurl'));
                        }
                    }
                }

                if ($submitPageId != false) {
                    $actionUrl = $webObject->composeUrl($submitPageId);
                }

                // Ziskat prava ....
                $show = array('read' => true, 'write' => true, 'delete' => false);
                $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_READ . ";");
                $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_WRITE . ";");
                $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `article_line_right` WHERE `line_id` = " . $lineId . " AND `type` = " . WEB_R_DELETE . ";");
                $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE `group`.`gid` IN (' . implode(',', RoleHelper::getCurrentRoles()) . ') ORDER BY `value`;');
                $groupSelectR = '<select id="article-right-edit-groups-r" name="article-right-edit-groups-r[]" multiple="multiple" size="5">';
                $groupSelectW = '<select id="article-right-edit-groups-w" name="article-right-edit-groups-w[]" multiple="multiple" size="5">';
                $groupSelectD = '<select id="article-right-edit-groups-d" name="article-right-edit-groups-d[]" multiple="multiple" size="5">';
                foreach ($allGroups as $group) {
                    $selectedR = false;
                    $selectedW = false;
                    $selectedD = false;
                    foreach ($groupsR as $gp) {
                        if ($gp['gid'] == $group['gid']) {
                            $selectedR = true;
                            $show['read'] = true;
                        }
                    }
                    foreach ($groupsW as $gp) {
                        if ($gp['gid'] == $group['gid']) {
                            $selectedW = true;
                            $show['write'] = true;
                        }
                    }
                    foreach ($groupsD as $gp) {
                        if ($gp['gid'] == $group['gid']) {
                            $selectedD = true;
                            $show['delete'] = true;
                        }
                    }
                    $groupSelectR .= '<option' . (($selectedR) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                    $groupSelectW .= '<option' . (($selectedW) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                    $groupSelectD .= '<option' . (($selectedD) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                }
                $groupSelectR .= '</select>';
                $groupSelectW .= '</select>';
                $groupSelectD .= '</select>';

                $labelList = '';
                $labels = parent::db()->fetchAll('select `id`, `name` from `article_label` order by `name`');
                $usedLabels = parent::db()->fetchAll('select `label_id` from `article_line_label` where `line_id` = ' . $lineId . ';');
                foreach ($labels as $label) {
                    $used = false;
                    foreach ($usedLabels as $ul) {
                        if ($label['id'] == $ul['label_id']) {
                            $used = true;
                            break;
                        }
                    }
                    $labelList .= ''
                            . '<input type="checkbox" name="article-line-labels[]" id="article-line-labels-' . $label['id'] . '" ' . ($used ? ' checked="checked"' : '') . ' value="' . $label['id'] . '" />'
                            . '<label for="article-line-labels-' . $label['id'] . '">' . $label['name'] . '</label> ';
                }

                $line = $dbObject->fetchAll('SELECT `name`, `url`, `parentdirectory_id` FROM `article_line` WHERE `id` = ' . $lineId . ';');
                if (count($line) != 0 || $lineId == 0) {
                    $return .= ''
                            . '<div class="article-line-edit">'
                            . '<form nam="article-line-edit" method="post" action="' . $actionUrl . '">'
                            . '<div class="gray-box-float">'
                            . '<label for="article-line-edit-name" class="w60">' . $rb->get('lines.name') . ':</label> '
                            . '<input type="text" id="article-line-edit-name" name="article-line-edit-name" value="' . $line[0]['name'] . '" class="w300" />'
                            . '</div>'
                            . '<div class="clear"></div>'
                            . '<div class="article-url gray-box-float">'
                            . '<label for="article-line-edit-url" class="w60">' . $rb->get('articles.url') . ':</label> '
                            . '<input type="text" id="article-line-edit-url" name="article-line-edit-url" value="' . $line[0]['url'] . '" class="w300" />'
                            . '</div>'
                            . '<div class="clear"></div>'
                            . '<div class="article-directory gray-box-float">'
                            . '<label for="article-line-edit-parentdirectoryid" title="' . $rb->get('lines.parentdirectoryid-title') . '" class="w60">' . $rb->get('lines.parentdirectoryid') . ':</label> '
                            . '<input type="text" id="article-line-edit-parentdirectoryid" name="article-line-edit-parentdirectoryid" value="' . $line[0]['parentdirectory_id'] . '" class="w60" />'
                            . '</div>'
                            . '<div class="clear"></div>'
                            . '<div class="article-line-rights">'
                            . (($show['read']) ? ''
                                    . '<div class="article-line-r-r">'
                                    . '<label for="article-right-edit-groups-r">' . $rb->get('lines.permread') . ':</label>'
                                    . $groupSelectR
                                    . '</div>' : '')
                            . (($show['write']) ? ''
                                    . '<div class="article-line-r-w">'
                                    . '<label for="article-right-edit-groups-w">' . $rb->get('lines.permwrite') . ':</label>'
                                    . $groupSelectW
                                    . '</div>' : '')
                            . (($show['delete']) ? ''
                                    . '<div class="article-line-r-d">'
                                    . '<label for="article-right-edit-groups-d">' . $rb->get('lines.permdelete') . ':</label>'
                                    . $groupSelectD
                                    . '</div>' : '')
                            . '</div>'
                            . '<div class="clear"></div>'
                            . '<div class="gray-box">'
                            . '<span class="w120">' . $rb->get('lines.availablelabels') . ':</span>'
                            . '<div>'
                            . $labelList
                            . '</div>'
                            . '</div>'
                            . '<div class="article-line-edit-submit">'
                            . '<input type="hidden" name="article-line-edit-id" value="' . $lineId . '" />'
                            . '<input type="submit" name="article-line-edit-submit" value="' . $rb->get('lines.save') . '" />'
                            . '</div>'
                            . '</form>'
                            . '</div>';
                } else {
                    $return .= '<h4 class="error">' . $rb->get('lines.notoedit') . '</h4>';
                }
            }

            if ($useFrames != "false") {
                return parent::getFrame($rb->get('lines.edittitle3'), $return, '');
            } else {
                return $return;
            }
        }

        /**
         *
         * 	Generates editable label list
         *
         */
        public function showEditLabels($useFrames = false) {
            $return = '';
            $actionUrl = $_SERVER['REQUEST_URI'];
            $rb = self::rb();

            $labels = parent::db()->fetchAll('select `id`, `name`, `url`, `order` from `article_label` order by `order`;');

            if ($_POST['label-delete'] == $rb->get('label.delete')) {
                $labelId = $_POST['label-id'];
                parent::db()->execute('delete from `article_label` where `id` = ' . $labelId . ';');
                parent::db()->execute('delete from `article_line_label` where `label_id` = ' . $labelId . ';');
                parent::db()->execute('delete from `article_attached_label` where `label_id` = ' . $labelId . ';');
                parent::db()->execute('delete from `article_label_language` where `label_id` = ' . $labelId . ';');
                $return .= parent::getSuccess($rb->get('label.deleted'));
            } else {
                Order::tryUpdate($labels, 'label', 'article_label', 'id', 'order');
            }

            $labels = parent::db()->fetchAll('select `id`, `name`, `url` from `article_label` order by `order`;');
            if (count($labels) > 0) {
                foreach ($labels as $key => $label) {
                    $labels[$key]['form'] = ''
                        . Order::upForm($actionUrl, 'label', $label['id'], $rb->get('label.moveup-title'))
                        . Order::downForm($actionUrl, 'label', $label['id'], $rb->get('label.movedown-title'))
                        . '<form name="label-edit" method="post" action="' . $actionUrl . '">'
                            . '<input type="hidden" name="label-id" value="' . $label['id'] . '" />'
                            . '<input type="hidden" name="label-edit" value="' . $rb->get('label.edit') . '" />'
                            . '<input type="image" src="~/images/page_edi.png" name="label-edit" value="' . $rb->get('label.edit') . '" title="' . $rb->get('label.edittitle2') . ', id=' . $label['id'] . '" />'
                        . '</form> '
                        . '<form name="label-delete" method="post" action="' . $actionUrl . '">'
                            . '<input type="hidden" name="label-id" value="' . $label['id'] . '" />'
                            . '<input type="hidden" name="label-delete" value="' . $rb->get('label.delete') . '" />'
                            . '<input class="confirm" type="image" src="~/images/page_del.png" name="label-delete" value="' . $rb->get('label.delete') . '" title="' . $rb->get('label.deletetitle') . ', id=' . $label['id'] . '" />'
                        . '</form>';
                }
                $grid = new BaseGrid();
                $grid->setHeader(array('id' => $rb->get('label.id'), 'name' => $rb->get('label.name'), 'url' => $rb->get('label.url'), 'form' => ''));
                $grid->addRows($labels);
                $grid->addClass('clickable');

                $return .= $grid->render();
            } else {
                $return .= parent::getWarning($rb->get('label.nolabels'));
            }

            $return .= ''
                    . '<hr />'
                    . '<div class="gray-box">'
                    . '<form name="label-new" method="post" action="' . $artionUrl . '">'
                    . '<input type="submit" name="label-new" value="' . $rb->get('label.new') . '" title="' . $rb->get('label.newtitle') . '" />'
                    . '</form>'
                    . '</div>';

            if ($useFrames != "false") {
                return parent::getFrame($rb->get('label.edittitle'), $return, '');
            } else {
                return $return;
            }
        }

        /**
         *
         * 	Generates edit form label
         *
         */
        public function showEditLabelForm($useFrames = false) {
            $return = '';
            $actionUrl = $_SERVER['REQUEST_URI'];
            $label = array();
            $ok = true;
            $rb = self::rb();

            if ($_POST['label-edit-save'] == $rb->get('label.save')) {
                $label['id'] = $_POST['label-edit-id'];
                $label['name'] = $_POST['label-edit-name'];
                $label['url'] = $_POST['label-edit-url'];
                $label['seturl'] = $_POST['label-edit-seturl'];
                $lineIds = $_POST['label-edit-lines'];

                foreach($label['url'] as $key => $value) {
                    if (strlen($value) == 0 && $label['seturl'][$key] == 'on') {
                        $value = $label['name'][$key];
                    }
                    $label['url'][$key] = strtolower(parent::convertToValidUrl($value));
                }
                
                if (strlen($label['name']['null']) < 2) {
                    $ok = false;
                    $return .= parent::getError($rb->get('label.namelength'));
                }
                $idSql = '';
                if ($label['id'] != '') {
                    $idSql = ' and `id` != ' . $label['id'];
                }

                $labels = parent::db()->fetchAll('select `id` from `article_label` where `url` = "' . $label['url']['null'] . '"' . $idSql . ';');
                if (count($labels) != 0) {
                    $ok = false;
                    $return .= parent::getError($rb->get('label.uniqueurlandlength'));
                }

                if ($ok) {
                    if ($label['id'] != '') {
                        parent::db()->execute('update `article_label` set `name` = "' . $label['name']['null'] . '", `url` = "' . $label['url']['null'] . '" where `id` = ' . $label['id'] . ';');
                        $return .= parent::getSuccess($rb->get('label.saved'));
                    } else {
                        parent::db()->execute('insert into `article_label`(`name`, `url`) values("' . $label['name']['null'] . '", "' . $label['url']['null'] . '");');
                        $label['id'] = $_POST['label-id'] = parent::db()->getLastId();
                        parent::db()->execute('update `article_label` set `order` = ' . $label['id'] . ' where `id` = ' . $label['id'] . ';');
                        $return .= parent::getSuccess($rb->get('label.updated'));
                    }
                    
                    parent::db()->execute('delete from `article_line_label` where `label_id` = ' . $label['id'] . ';');
                    foreach ($lineIds as $lineId) {
                        parent::db()->execute('insert into `article_line_label`(`line_id`, `label_id`) values (' . $lineId . ', ' . $label['id'] . ');');
                    }

                    foreach($label['name'] as $languageId => $name) {
                        if($languageId != 'null') {
                            if(strlen($name) > 0) {
                                $existing = parent::db()->fetchSingle('select count(`label_id`) as `count` from `article_label_language` where `label_id` = ' . $label['id'] . ' and `language_id` = ' . $languageId . ';');
                                if($existing['count'] > 0) {
                                    parent::db()->execute('update `article_label_language` set `name` = "' . $label['name'][$languageId] . '", `url` = "' . $label['url'][$languageId] . '" where `label_id` = ' . $label['id'] . ' and `language_id` = ' . $languageId . ';');
                                } else {
                                    parent::db()->execute('insert into `article_label_language`(`label_id`, `language_id`, `name`, `url`) values(' . $label['id'] . ', ' . $languageId . ', "' . $label['name'][$languageId] . '", "' . $label['url'][$languageId] . '");');
                                }
                            } else {
                                $existing = parent::db()->fetchSingle('delete from `article_label_language` where `label_id` = ' . $label['id'] . ' and `language_id` = ' . $languageId . ';');
                            }
                        }
                    }
                }
            }

            if ($_POST['label-edit'] == $rb->get('label.edit') || $_POST['label-new'] == $rb->get('label.new') || $ok == false) {
                $usedLines = array();

                if ($_POST['label-id'] != '') {
                    $labelId = $_POST['label-id'];
                    $label = parent::db()->fetchSingle('select `id`, `name`, `url` from `article_label` where `id` = ' . $labelId . ';');
                    $label['name'] = array('null' => $label['name']);
                    $label['url'] = array('null' => $label['url']);

                    $usedLines = parent::db()->fetchAll('select `line_id` from `article_line_label` where `label_id` = ' . $labelId . ';');
                }

                $languageFormHtml = '';
                $languages = parent::dao('Language')->getList();
                if($labelId != '') {
                    $rawData = parent::dao('ArticleLabelLanguage')->getList(parent::select()->where('label_id', '=', $labelId));
                    foreach($rawData as $item) {
                        $label['name'][$item['language_id']] = $item['name'];
                        $label['url'][$item['language_id']] = $item['url'];
                    }
                }

                foreach ($languages as $language) {
                    $languageFormHtml .= ''
                        . '<div class="article-label-language-' . $language['id'] . '">'
                            . '<strong>' . (strlen($language['language']) == 0 ? $rb->get('label.language-default') : $language['language']) . '</strong>'
                            . '<div class="gray-box">'
                                . '<label for="label-edit-name-' . $language['id'] . '" class="w60">' . $rb->get('label.name') . '</label>'
                                . '<input type="text" class="w200" name="label-edit-name[' . $language['id'] . ']" id="label-edit-name-' . $language['id'] . '" value="' . $label['name'][$language['id']] . '" />'
                            . '</div>'
                            . '<div class="gray-box">'
                                . '<label for="label-edit-url-' . $language['id'] . '" class="w60">' . $rb->get('label.url') . '</label>'
                                . '<input type="text" class="w200" name="label-edit-url[' . $language['id'] . ']" id="label-edit-url-' . $language['id'] . '" value="' . $label['url'][$language['id']] . '" />'
                                . '<input type="checkbox" name="label-edit-seturl[' . $language['id'] . ']" id="label-edit-seturl-' . $language['id'] . '"' . (strlen($label['name'][$language['id']]) > 0 && strlen($label['url'][$language['id']]) == 0 ? '' : ' checked="checked"') . ' />'
                                . '<label for="label-edit-seturl-' . $language['id'] . '">' . $rb->get('label.seturl') . '</label>'
                            . '</div>'
                        . '</div>';
                }
                
                $lines = self::getLinesWithWriteRight();
                $lineList = '';
                foreach ($lines as $line) {
                    $used = false;
                    foreach ($usedLines as $ul) {
                        if ($line['id'] == $ul['line_id']) {
                            $used = true;
                            break;
                        }
                    }

                    $lineList .= ''
                        . '<input type="checkbox" name="label-edit-lines[]" id="label-edit-lines-' . $line['id'] . '" ' . ($used ? ' checked="checked"' : '') . ' value="' . $line['id'] . '" />'
                        . '<label for="label-edit-lines-' . $line['id'] . '">' . $line['name'] . '</label> ';
                }

                $return .= ''
                    . '<form name="label-edit-form" method="post" action="' . $artionUrl . '">'
                        . '<div class="gray-box">'
                            . '<label for="label-edit-name" class="w60">' . $rb->get('label.name') . '</label>'
                            . '<input type="text" class="w200" name="label-edit-name[null]" id="label-edit-name" value="' . $label['name']['null'] . '" />'
                        . '</div>'
                        . '<div class="gray-box">'
                            . '<label for="label-edit-url" class="w60">' . $rb->get('label.url') . '</label>'
                            . '<input type="text" class="w200" name="label-edit-url[null]" id="label-edit-url" value="' . $label['url']['null'] . '" />'
                            . '<input type="checkbox" name="label-edit-seturl[null]" id="label-edit-seturl"' . (strlen($label['name']['null']) > 0 && strlen($label['url']['null']) == 0 ? '' : ' checked="checked"') . ' />'
                            . '<label for="label-edit-seturl">' . $rb->get('label.seturl') . '</label>'
                        . '</div>'
                        . '<div class="gray-box">'
                            . '<span class="w180">' . $rb->get('label.availablelines') . ':</span>'
                            . '<div>'
                                . $lineList
                            . '</div>'
                        . '</div>'
                        . $languageFormHtml
                        . '<div class="gray-box">'
                            . '<input type="hidden" name="label-edit-id" value="' . $label['id'] . '" />'
                            . '<input type="submit" name="label-edit-save" value="' . $rb->get('label.save') . '" title="' . $rb->get('label.savetitle') . '" />'
                        . '</div>'
                    . '</form>';

                if ($useFrames != "false") {
                    return parent::getFrame($rb->get('label.edittitle3'), $return, '');
                } else {
                    return $return;
                }
            }
        }

        // =============== ARTICLE DETAIL ==================================

        public function showId() {
            return parent::request()->get('id', 'current-article');
        }

        public function showDate($format = 'd.m.Y', $type = '') {
            if ($format == '') {
                $format = 'd.m.Y';
            }
            if ($type == 'datetime') {
                return parent::request()->get('datetime', 'current-article');
            }
            return date($format, parent::request()->get('date', 'current-article'));
        }

        public function showTime($format = 'H:i:s') {
            if ($format == '') {
                $format = 'H:i:s';
            }
            return parent::request()->get('time', 'current-article');
        }

        public function showName() {
            return parent::request()->get('name', 'current-article');
        }

        public function showKeywords() {
            return parent::request()->get('keywords', 'current-article');
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

        public function showVisible() {
            return parent::request()->get('visible', 'current-article');
        }

        // =============== PROPERTIES ======================================

        public function setArticleId($id) {
            //echo 'set ID: '.$id.'<br />';
            parent::request()->set('article-id', $id);
            return $id;
        }

        public function getArticleId() {
            //echo 'get ID: '.parent::request()->get('article-id').'<br />';
            return parent::request()->get('article-id');
        }
        
        public function setIsActiveArticle($value) {
            parent::request()->set('article-is-active', $value);
            return $labelId;
        }

        public function getIsActiveArticle() {
            return parent::request()->get('article-is-active');
        }
        
        public function setArticleDirectoryId($directoryId) {
            parent::request()->set('article-directory-id', $directoryId);
            return $directoryId;
        }

        public function getArticleDirectoryId() {
            return parent::request()->get('article-directory-id');
        }
        
        public function setArticleLanguageId($value) {
            parent::request()->set('article-language-id', $value);
            return $labelId;
        }

        public function getArticleLanguageId() {
            return parent::request()->get('article-language-id');
        }

        public function setUrl($url) {
            if (strpos($url, '://') === false) {
                $article = parent::db()->fetchSingle('select `article_id` from `article_content` where `url` = "' . $url . '";');
                if ($article != array()) {
                    self::setArticleId($article['article_id']);
                    parent::request()->set('article-url', $url);
                    return $url;
                } else {
                    return 'false.false';
                }
            }
        }

        public function getUrl() {
            if (!self::getArticleId()) {
                return 'false.false';
            }

            $article = parent::db()->fetchSingle('select `url` from `article_content` where `article_id` = ' . self::getArticleId() . ';');
            $url = $article['url'];
            return $url;
        }

        public function setLineUrl($url) {
            parent::request()->set('line-url', $url);
            return $url;
        }

        public function getLineUrl() {
            return parent::request()->get('line-url');
        }
        
        public function setLabelId($labelId) {
            parent::request()->set('label-id', $labelId);
            return $labelId;
        }

        public function getLabelId() {
            return parent::request()->get('label-id');
        }
        
        public function setIsActiveLabel($value) {
            parent::request()->set('label-is-active', $value);
            return $labelId;
        }

        public function getIsActiveLabel() {
            return parent::request()->get('label-is-active');
        }
            
        public function setHasContent($hasContent) {
            parent::request()->set('article-has-content', $hasContent ? 'true' : 'false');
            return $hasContent;
        }

        public function getHasContent() {
            return parent::request()->get('article-has-content');
        }
        
        public function setHasHead($hasHead) {
            parent::request()->set('article-has-head', $hasHead ? 'true' : 'false');
            return $hasHead;
        }

        public function getHasHead() {
            return parent::request()->get('article-has-head');
        }
        
        public function setIsExternalUrl($value) {
            parent::request()->set('article-is-external-url', $value ? 'true' : 'false');
            return $value;
        }

        public function getIsExternalUrl() {
            return parent::request()->get('article-is-external-url');
        }

        public function setLabelUrl($url) {
            $languageId = parent::web()->getLanguageIdWhenParsing();
            if(!is_null($languageId)) {
                $label = parent::db()->fetchSingle('select `label_id` as `id` from `article_label_language` where `url` = "' . $url . '" and `language_id` = ' . $languageId . ';');
                if ($label != array()) {
                    self::setLabelId($label['id']);
                    return $url;
                }
            }

            $label = parent::db()->fetchSingle('select `id` from `article_label` where `url` = "' . $url . '" and not exists(select * from `article_label_language` where `label_id` = `label_id` and `language_id` = ' . parent::web()->LanguageId . ');');
            if ($label != array()) {
                self::setLabelId($label['id']);
                return $url;
            }

            return 'false.false';
        }

        public function getLabelUrl() {
            $label = parent::db()->fetchSingle('select `url` from `article_label_language` where `label_id` = "' . self::getLabelId() . '" and `language_id` = ' . parent::web()->LanguageId . ';');
            if ($label != array()) {
                return $label['url'];
            }

            $label = parent::db()->fetchSingle('select `url` from `article_label` where `id` = "' . self::getLabelId() . '";');
            return $label['url'];
        }

    }

?>
