-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Poèítaè: localhost
-- Vygenerováno: Nedìle 17. kvìtna 2009, 23:33
-- Verze MySQL: 5.1.30
-- Verze PHP: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Databáze: `tmp_wfw_wp`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `article_content`
--

CREATE TABLE IF NOT EXISTS `article_content` (
  `article_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `head` text COLLATE latin1_general_ci,
  `content` text COLLATE latin1_general_ci,
  `author` tinytext COLLATE latin1_general_ci,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`article_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `article_line`
--

CREATE TABLE IF NOT EXISTS `article_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `article_line_right`
--

CREATE TABLE IF NOT EXISTS `article_line_right` (
  `line_id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`line_id`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Struktura tabulky `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `tag_lib_start` text COLLATE latin1_general_ci NOT NULL,
  `tag_lib_end` text COLLATE latin1_general_ci NOT NULL,
  `head` text COLLATE latin1_general_ci,
  `content` text COLLATE latin1_general_ci,
  PRIMARY KEY (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `content`
--

INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES
(2, 1, '<login:init group="web-admins" />', '', '<link rel="stylesheet" href="~/css/cms.css" type="text/css" />', '<web:content />'),
(3, 1, '', '', '', '<login:redirectWhenNotLogged pageId="4" /><login:redirectWhenLogged pageId="6" />'),
(4, 1, '', '', '', '<div class="login">\r\n  <div class="login-head"></div>\r\n  <div class="login-in">\r\n    <login:form group="web-admins" pageId="6" />\r\n  </div>\r\n</div>'),
(5, 1, '<php:register tagPrefix="wp" classPath="php.libs.WebProject" />', '<php:unregister tagPrefix="wp" />', '<link rel="stylesheet" href="~/css/editor.css" type="text/css" />\r\n<script type="text/javascript" src="~/js/domready.js"></script>\r\n<script type="text/javascript" src="~/js/Closer.js"></script>\r\n<script type="text/javascript" src="~/js/Confirm.js"></script>\r\n<script type="text/javascript" src="~/js/Editor.js"></script>\r\n<script type="text/javascript" src="~/js/FileName.js"></script>\r\n<script type="text/javascript" src="~/js/CountDown.js"></script>\r\n<script type="text/javascript" src="~/js/init.js"></script>\r\n<script type="text/javascript" src="~/tiny-mce/tiny_mce.js"></script>\r\n<script type="text/javascript" src="~/file.php?path=~/scripts/js/initTiny.js"></script>', '<div class="cms">\r\n  <div class="head">\r\n    <login:logout group="web-admins" pageId="4" />\r\n    <div id="logon-count-down" class="logon-count-down">\r\n      <div class="count-down-cover">\r\n        <span class="count-down-label">Login session <br/>expires in: </span>\r\n        <span class="count-down-counter">900s</span>\r\n      </div>\r\n    </div>\r\n    <login:info />\r\n    <wp:selectProject showMsg="false" useFrames="false" />\r\n    <div class="cms-menu">\r\n      <span class="menu-root">Menu</span>\r\n      <web:menu parentId="5" />\r\n    </div>\r\n  </div>\r\n  <div class="body">\r\n    <web:content />\r\n  </div>\r\n</div>'),
(6, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showList editable="true" />'),
(7, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showFiles editable="true" />'),
(8, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<p><fl:showUploadForm /></p><p><fl:showNewDirectoryForm /></p><p><fl:showDirectory /></p>'),
(9, 1, '<php:register tagPrefix="user" classPath="php.libs.User" />', '<php:unregister tagPrefix="user" />', '', '<web:content />'),
(16, 1, '<php:register tagPrefix="artc" classPath="php.libs.Article" />', '<php:unregister tagPrefix="artc" />', '', '<web:content />'),
(40, 1, '', '', '', '<p>\r\n  <a href="&web:page=39">Edit article lines</a>\r\n</p>\r\n<p>\r\n  <artc:setLine method="session" />\r\n</p>\r\n<p>\r\n  <artc:showManagement method="session" detailPageId="41" />\r\n</p>\r\n<p>\r\n  <artc:createArticle detailPageId="41" method="session" />\r\n</p>'),
(17, 1, '<php:register tagPrefix="gb" classPath="php.libs.Guestbook" />', '<php:unregister tagPrefix="gb" />', '', '<gb:show guestbookId="1" editable="true" useFrame="true" />'),
(23, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />\r\n<php:register tagPrefix="user" classPath="php.libs.User" />', '<php:unregister tagPrefix="pg" />\r\n<php:unregister tagPrefix="user" />', '', '<pg:clearUrlCache />\r\n<pg:updateKeywords />\r\n<pg:showLanguages editable="true" />\r\n<user:truncateLog />\r\n<user:showLog />'),
(25, 1, '', '', '', '<web:content />'),
(39, 1, '', '', '', '<p>\r\n  <a href="&web:page=40">Back to articles</a>\r\n</p>\r\n<p>\r\n	<artc:showLines editable="true" detailPageId="42" />\r\n</p>\r\n<p>\r\n	<artc:createLine detailPageId="42" />\r\n</p>'),
(26, 1, '', '', '', '<wp:showProjects detailPageId="27" editable="true" />'),
(27, 1, '', '', '', '<a href="&web:page=26">Back to web project list ...</a>\r\n\r\n<wp:showEditForm />'),
(28, 1, '', '', '', '<wp:selectProject />'),
(41, 1, '', '', '', '<p>\r\n  <a href="&web:page=40">Back to article list ...</a>\r\n</p>\r\n<p>\r\n  <artc:editArticle />\r\n</p>'),
(42, 1, '', '', '', '<a href="&web:page=39">Back to article line list ...</a>\r\n\r\n<p>\r\n  <artc:editLine />\r\n</p>'),
(44, 1, '', '', '', '<web:content />'),
(45, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showTemplates detailPageId="46" />'),
(46, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<p>\r\n  <a href="&web:page=45">Back template list ...</a>\r\n</p>\r\n\r\n<pg:editTemplate />'),
(55, 1, '', '', '', '<h2>Welcome</h2>\r\n<p>\r\nSorry ... but this web site is currently underconstruction. Thanks\r\n</p>'),
(53, 1, '', '', '', '<p>\r\n  <a href="&web:page=52">Groups edit</a>\r\n</p>\r\n<p>\r\n  <user:management />\r\n</p>'),
(52, 1, '', '', '', '<p><a href="&web:page=53">Back to user manager ...</a></p>\r\n<p>\r\n  <user:newGroup />\r\n</p>\r\n<p>\r\n  <user:deleteGroup />\r\n</p>');

-- --------------------------------------------------------

--
-- Struktura tabulky `counter`
--

CREATE TABLE IF NOT EXISTS `counter` (
  `ip` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL,
  PRIMARY KEY (`counter_id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `directory`
--

CREATE TABLE IF NOT EXISTS `directory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `directory_right`
--

CREATE TABLE IF NOT EXISTS `directory_right` (
  `did` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`did`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabulky `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dir_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `title` tinytext COLLATE latin1_general_ci NOT NULL,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `file_right`
--

CREATE TABLE IF NOT EXISTS `file_right` (
  `fid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`fid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Struktura tabulky `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_gid` int(11) NOT NULL DEFAULT '1',
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `group`
--

INSERT INTO `group` (`gid`, `parent_gid`, `name`, `value`) VALUES
(1, 0, 'admins', 1),
(2, 1, 'web-admins', 50),
(3, 2, 'web', 254),
(6, 1, 'web-projects', 60);

-- --------------------------------------------------------

--
-- Struktura tabulky `guestbook`
--

CREATE TABLE IF NOT EXISTS `guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `content` text COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `guestbook_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `guestbook`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `info`
--

CREATE TABLE IF NOT EXISTS `info` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `in_title` int(11) NOT NULL DEFAULT '1',
  `href` tinytext COLLATE latin1_general_ci NOT NULL,
  `in_menu` int(11) NOT NULL,
  `page_pos` int(11) NOT NULL,
  `is_visible` int(11) NOT NULL,
  `keywords` tinytext COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `info`
--

INSERT INTO `info` (`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`) VALUES
(2, 1, 'CMS', 1, '', 0, 43, 1, '', 1241702801),
(3, 1, 'Index', 0, '', 0, 3, 1, '', 1232545364),
(4, 1, 'Login', 1, 'login', 0, 4, 1, '', 1241702693),
(5, 1, 'in', 0, 'in', 1, 5, 1, '', 1242483124),
(6, 1, 'Page Manager', 1, 'page-manager', 1, 8, 1, '', 1234389321),
(7, 1, 'Text File Manager', 1, 'text-file-manager', 1, 9, 1, '', 1233078739),
(8, 1, 'File Manager', 1, 'file-manager', 1, 16, 1, '', 1242071139),
(9, 1, 'User Manager', 1, 'user-manager', 1, 17, 1, '', 1242245797),
(16, 1, 'Article Manager', 1, 'article-manager', 1, 23, 1, '', 1241358620),
(17, 1, 'Guestbook Manager', 1, 'guestbook-manager', 1, 25, 1, '', 1234282979),
(23, 1, 'Web Settings', 1, 'web-settings', 1, 44, 1, '', 1242468311),
(25, 1, 'Web Project Manager', 1, 'web-project-manager', 1, 50, 1, '', 1241994270),
(26, 1, 'List', 1, '', 0, 26, 1, '', 1241950546),
(27, 1, 'Edit', 1, 'edit', 0, 27, 1, '', 1241950560),
(28, 1, 'Select', 1, 'select', 0, 28, 1, '', 1241310063),
(39, 1, 'Lines', 1, 'lines', 0, 40, 1, '', 1241994208),
(55, 1, 'Welcome', 1, '', 0, 54, 1, '', 1242481167),
(45, 1, 'List', 1, '', 0, 45, 1, '', 1241517092),
(46, 1, 'Edit', 1, 'edit', 0, 46, 1, '', 1241519980),
(44, 1, 'Template Manager', 1, 'template-manager', 1, 44, 1, '', 1241516140),
(40, 1, 'List', 1, '', 0, 39, 1, '', 1241464605),
(41, 1, 'Edit Article', 1, 'edit-article', 0, 41, 1, '', 1241387815),
(42, 1, 'Edit Line', 1, 'edit-line', 0, 42, 1, '', 1241369340),
(52, 1, 'Groups', 1, 'groups', 0, 53, 1, '', 1242427728),
(53, 1, 'Users', 1, '', 0, 52, 1, '', 1242245779);

-- --------------------------------------------------------

--
-- Struktura tabulky `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL,
  `language` tinytext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `language`
--

INSERT INTO `language` (`id`, `language`) VALUES
(1, ''),
(2, 'cs'),
(3, 'en');

-- --------------------------------------------------------

--
-- Struktura tabulky `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `page`
--

INSERT INTO `page` (`id`, `parent_id`, `wp`) VALUES
(2, 0, 1),
(3, 2, 1),
(4, 2, 1),
(5, 2, 1),
(6, 5, 1),
(7, 5, 1),
(8, 5, 1),
(9, 5, 1),
(53, 9, 1),
(52, 9, 1),
(16, 5, 1),
(17, 5, 1),
(23, 5, 1),
(25, 5, 1),
(26, 25, 1),
(27, 25, 1),
(28, 25, 1),
(39, 16, 1),
(44, 5, 1),
(45, 44, 1),
(46, 44, 1),
(55, 0, 1),
(40, 16, 1),
(41, 16, 1),
(42, 16, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `page_file`
--

CREATE TABLE IF NOT EXISTS `page_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `content` text COLLATE latin1_general_ci NOT NULL,
  `for_all` int(2) NOT NULL DEFAULT '1',
  `for_msie6` int(2) NOT NULL DEFAULT '0',
  `for_msie7` int(2) NOT NULL DEFAULT '0',
  `for_msie8` int(2) NOT NULL DEFAULT '0',
  `for_firefox` int(2) NOT NULL DEFAULT '0',
  `for_opera` int(2) NOT NULL DEFAULT '0',
  `for_safari` int(2) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `page_file`
--

INSERT INTO `page_file` (`id`, `name`, `content`, `for_all`, `for_msie6`, `for_msie7`, `for_msie8`, `for_firefox`, `for_opera`, `for_safari`, `type`, `wp`) VALUES
(1, 'ajax', 'var ajaxRootElement = null;\r\nvar ajaxLoading = null;\r\n\r\nfunction addEvent (obj, ev, func, b) {\r\n  if(obj.addEventListener) {\r\n    obj.addEventListener(ev, func, b);\r\n  } else {\r\n    obj.attachEvent("on" + ev, func);\r\n  }\r\n}\r\n\r\nfunction stopEvent(event) {\r\n  event.cancelBubble = true;\r\n  event.returnValue = false;\r\n  if(navigator.appName != "Microsoft Internet Explorer") {\r\n    event.preventDefault();\r\n  }\r\n}\r\n\r\naddEvent(window, ''load'', initAjax, false);\r\n\r\nfunction initAjax(event) {\r\n  initDynamicLinks(document);\r\n}\r\n\r\nfunction initDynamicLinks(root) {\r\n  if(root != null) {\r\n    var lis = root.getElementsByTagName(''div'');\r\n    for(var i = 0; i < lis.length; i ++) {\r\n      if(lis[i].className.indexOf(''link'') != -1) {\r\n        if(lis[i].childNodes[0] != null && lis[i].childNodes[0].tagName == "A") {\r\n          addEvent(lis[i].childNodes[0], ''click'', menuLinkClick, false);\r\n        }\r\n      }\r\n    }\r\n    var as = root.getElementsByTagName(''a'');\r\n    for(var i = 0; i < as.length; i ++) {\r\n      if(as[i].rel == "dynamic-link") {\r\n        addEvent(as[i], ''click'', menuLinkClick, false);\r\n      }\r\n    }\r\n  }\r\n}\r\n\r\nfunction menuLinkClick(event) {\r\n  var anchor = (event.srcElement) ? event.srcElement : event.target;\r\n  if(anchor.parentNode != null && anchor.parentNode.tagName == "A") {\r\n    anchor = anchor.parentNode;\r\n    if(ajaxLoading == null) {\r\n      ajaxLoading = document.createElement(''div'');\r\n      ajaxLoading.className = "ajax-loading";\r\n      document.body.appendChild(ajaxLoading);\r\n    }\r\n    ajaxLoading.innerHTML = "Loading ...";\r\n    var xmlhttp = new Rxmlhttp();\r\n    xmlhttp.setAsync(true);\r\n    xmlhttp.setMethod("GET");\r\n    xmlhttp.onSuccess(processRequest);\r\n    xmlhttp.loadPage(anchor.href + "?__START_ID=24");\r\n    stopEvent(event);\r\n  }\r\n}\r\n\r\nfunction processRequest(xmlhttp) {\r\n  var temp = document.createElement(''div'');\r\n  temp.innerHTML = xmlhttp.responseText.replace(''<body'', ''<div '').replace(''</body'', ''</div'');\r\n  var body = temp.getElementsByTagName(''div'');\r\n  body = body[0];\r\n  ajaxRootElement = document.getElementById(''web-content'');\r\n  if(ajaxRootElement == null) {\r\n    ajaxLoading.innerHTML = "ERROR LOADING .. Press F5!";\r\n  } else {\r\n    ajaxRootElement.innerHTML = body.innerHTML;\r\n    ajaxLoading.innerHTML = "";\r\n    initDynamicLinks(ajaxRootElement);\r\n  }\r\n}', 1, 0, 0, 0, 0, 0, 0, 2, 1),
(2, 'tiny', '/* CSS file for Tiny Editor! */', 1, 0, 0, 0, 0, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `page_file_inc`
--

CREATE TABLE IF NOT EXISTS `page_file_inc` (
  `file_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`file_id`,`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `page_file_inc`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `page_right`
--

CREATE TABLE IF NOT EXISTS `page_right` (
  `pid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`pid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `page_right`
--

INSERT INTO `page_right` (`pid`, `gid`, `type`) VALUES
(0, 2, 102),
(0, 2, 103),
(0, 3, 101),
(2, 1, 102),
(2, 1, 103),
(2, 3, 101),
(3, 1, 102),
(3, 1, 103),
(3, 3, 101),
(4, 1, 102),
(4, 1, 103),
(4, 3, 101),
(5, 1, 102),
(5, 1, 103),
(5, 2, 101),
(6, 1, 102),
(6, 1, 103),
(6, 2, 101),
(7, 1, 102),
(7, 1, 103),
(7, 2, 101),
(8, 1, 102),
(8, 1, 103),
(8, 2, 101),
(9, 1, 102),
(9, 1, 103),
(9, 2, 101),
(16, 1, 102),
(16, 1, 103),
(16, 2, 101),
(17, 1, 102),
(17, 1, 103),
(17, 2, 101),
(23, 1, 101),
(23, 1, 102),
(23, 1, 103),
(25, 1, 102),
(25, 1, 103),
(25, 2, 101),
(26, 2, 101),
(26, 2, 102),
(26, 2, 103),
(27, 2, 101),
(27, 2, 102),
(27, 2, 103),
(28, 1, 101),
(28, 1, 102),
(28, 1, 103),
(39, 1, 102),
(39, 1, 103),
(39, 2, 101),
(40, 1, 102),
(40, 1, 103),
(40, 2, 101),
(41, 1, 102),
(41, 1, 103),
(41, 2, 101),
(42, 1, 102),
(42, 1, 103),
(42, 2, 101),
(44, 1, 102),
(44, 1, 103),
(44, 2, 101),
(45, 1, 102),
(45, 1, 103),
(45, 2, 101),
(46, 1, 102),
(46, 1, 103),
(46, 2, 101),
(52, 1, 102),
(52, 1, 103),
(52, 2, 101),
(53, 1, 102),
(53, 1, 103),
(53, 2, 101),
(55, 1, 102),
(55, 1, 103),
(55, 3, 101);

-- --------------------------------------------------------

--
-- Struktura tabulky `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Vypisuji data pro tabulku `template`
--

INSERT INTO `template` (`id`, `content`) VALUES
(1, '<div class="article">\r\n  <div class="article-timestamp">\r\n    <a href="<artc:link />"><artc:time /> - <artc:date /></a>\r\n  </div>\r\n  <div class="article-head">\r\n    <artc:head />\r\n  </div>\r\n</div>'),
(2, '<div class="article">\r\n  <div class="article-timestamp">\r\n    <artc:time /> <strong><artc:date /></strong>\r\n  </div>\r\n  <div class="article-content">\r\n    <artc:content />\r\n  </div>\r\n  <div class="article-author">\r\n    <artc:author />\r\n  </div>\r\n</div>'),
(8, '<div class="article">\r\n  <div class="article-timestamp">\r\n    <a href="<artc:link />"><artc:date /></a>\r\n  </div>\r\n</div>'),
(10, '<style type="text/css">\r\n\r\n.counter {\r\n  width: 200px;\r\n}\r\n\r\n.counter div {\r\n  clear: both;\r\n}\r\n\r\n.counter span.col-name {\r\n  float: left;\r\n}\r\n\r\n.counter span.col-value {\r\n  float: right;\r\n}\r\n\r\n</style>\r\n<div class="counter">\r\n  <div class="counter-all">\r\n    <span class="col-name">\r\n      All:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:all />\r\n    </span>\r\n  </div>\r\n  <div class="counter-user">\r\n    <span class="col-name">\r\n      You:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:user />\r\n    </span>\r\n  </div>\r\n  <div class="counter-visitors">\r\n    <span class="col-name">\r\n      Visitors:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitors />\r\n    </span>\r\n  </div>\r\n  <div class="counter-today">\r\n    <span class="col-name">\r\n      Today:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsToday />\r\n    </span>\r\n  </div>\r\n  <div class="counter-hour">\r\n    <span class="col-name">\r\n      Last hour:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsHour />\r\n    </span>\r\n  </div>\r\n  <div class="counter-online">\r\n    <span class="col-name">\r\n      Online:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsOnline />\r\n    </span>\r\n  </div>\r\n</div>'),
(11, '<div class="article">\r\n  <artc:showDetail defaultArticleId="1" articleLangId="2" templateId="2" showError="false" />\r\n</div>');

-- --------------------------------------------------------

--
-- Struktura tabulky `template_right`
--

CREATE TABLE IF NOT EXISTS `template_right` (
  `tid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`tid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Vypisuji data pro tabulku `template_right`
--

INSERT INTO `template_right` (`tid`, `gid`, `type`) VALUES
(0, 2, 102),
(0, 2, 103),
(0, 3, 101),
(1, 2, 102),
(1, 2, 103),
(1, 3, 101),
(2, 2, 102),
(2, 2, 103),
(2, 3, 101),
(8, 2, 102),
(8, 3, 101),
(8, 6, 103),
(10, 2, 102),
(10, 2, 103),
(10, 3, 101),
(11, 2, 102),
(11, 2, 103),
(11, 3, 101);

-- --------------------------------------------------------

--
-- Struktura tabulky `urlcache`
--

CREATE TABLE IF NOT EXISTS `urlcache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` tinytext COLLATE latin1_general_ci NOT NULL,
  `page-ids` tinytext COLLATE latin1_general_ci NOT NULL,
  `language_id` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `surname` tinytext COLLATE latin1_general_ci NOT NULL,
  `login` tinytext COLLATE latin1_general_ci NOT NULL,
  `password` tinytext COLLATE latin1_general_ci NOT NULL,
  `enable` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`uid`, `group_id`, `name`, `surname`, `login`, `password`, `enable`) VALUES
(1, 1, 'admin', 'admin', 'admin', 'b49a387e1143eccc5d6cb585d49290c2e2a85145', 1),
(2, 0, 'HTML', 'koder', 'htmlkoder', 'e72aef7f14a3e8348ca7930b5f8b008b0ba94d2e', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `user_in_group`
--

CREATE TABLE IF NOT EXISTS `user_in_group` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Vypisuji data pro tabulku `user_in_group`
--

INSERT INTO `user_in_group` (`uid`, `gid`) VALUES
(1, 1),
(1, 2),
(1, 6),
(2, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `user_log`
--

CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `login_timestamp` int(11) NOT NULL,
  `logout_timestamp` int(11) NOT NULL,
  `used_group` tinytext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=228 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `web_alias`
--

CREATE TABLE IF NOT EXISTS `web_alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `url` tinytext NOT NULL,
  `http` int(11) NOT NULL,
  `https` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `web_project`
--

CREATE TABLE IF NOT EXISTS `web_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `url` tinytext NOT NULL,
  `http` int(11) NOT NULL DEFAULT '1',
  `https` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Vypisuji data pro tabulku `web_project`
--

INSERT INTO `web_project` (`id`, `name`, `url`, `http`, `https`) VALUES
(1, 'default', 'papayateam', 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `web_project_right`
--

CREATE TABLE IF NOT EXISTS `web_project_right` (
  `wp` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`wp`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Vypisuji data pro tabulku `web_project_right`
--

INSERT INTO `web_project_right` (`wp`, `gid`, `type`) VALUES
(0, 1, 102),
(0, 1, 103),
(0, 3, 101),
(1, 1, 102),
(1, 1, 103),
(1, 3, 101);

-- --------------------------------------------------------

--
-- Struktura tabulky `wp_wysiwyg_file`
--

CREATE TABLE IF NOT EXISTS `wp_wysiwyg_file` (
  `wp` int(11) NOT NULL,
  `tf_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
