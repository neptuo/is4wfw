-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Poèítaè: localhost
-- Vygenerováno: Støeda 21. ledna 2009, 15:55
-- Verze MySQL: 5.0.41
-- Verze PHP: 5.2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Databáze: `josie_maran`
-- 

-- --------------------------------------------------------

-- 
-- Struktura tabulky `content`
-- 

CREATE TABLE `content` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `tag_lib_start` text collate latin1_general_ci NOT NULL,
  `tag_lib_end` text collate latin1_general_ci NOT NULL,
  `head` text collate latin1_general_ci,
  `content` text collate latin1_general_ci,
  PRIMARY KEY  (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `content`
-- 

INSERT INTO `content` VALUES (2, 1, '<login:init group="web-admins" />', '', '', '<web:content />');
INSERT INTO `content` VALUES (3, 1, '', '', '', '<login:redirectWhenNotLogged pageId="4" /><login:redirectWhenLogged pageId="6" />');
INSERT INTO `content` VALUES (4, 1, '', '', '', '<div class="login">\r\n  <div class="login-head"></div>\r\n  <div class="login-in">\r\n    <login:form group="web-admins" pageId="6" />\r\n  </div>\r\n</div>');
INSERT INTO `content` VALUES (5, 1, '', '', '', '<div class="cms">\r\n	<div class="head">\r\n		<login:logout group="web-admins" pageId="4" />\r\n		<login:info />\r\n		<div class="cms-menu">\r\n			<span class="menu-root">Menu</span>\r\n			<web:menu parentId="5" />\r\n		</div>\r\n	</div>\r\n	<div class="body">\r\n		<web:content />\r\n	</div>\r\n</div>');
INSERT INTO `content` VALUES (6, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showList editable="true" />');
INSERT INTO `content` VALUES (7, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showFiles editable="true" />');
INSERT INTO `content` VALUES (8, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<p><fl:showUploadForm /></p><p><fl:showNewDirectoryForm /></p><p><fl:showDirectory /></p>');
INSERT INTO `content` VALUES (9, 1, '<php:register tagPrefix="user" classPath="php.libs.User" />', '<php:unregister tagPrefix="user" />', NULL, '<p>\r\n	<user:management />\r\n</p>');
INSERT INTO `content` VALUES (10, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', NULL, '<web:content />');
INSERT INTO `content` VALUES (11, 1, '', '', NULL, '<fl:get />');
INSERT INTO `content` VALUES (12, 1, '', '', NULL, '<web:content />');
INSERT INTO `content` VALUES (13, 1, '', '', NULL, '<web:textFile type="css" />');
INSERT INTO `content` VALUES (14, 1, '', '', NULL, '<web:content />');
INSERT INTO `content` VALUES (15, 1, '', '', NULL, '<web:textFile type="js" />');
INSERT INTO `content` VALUES (1, 1, '', '', NULL, '<h2>Welcome</h2>\r\n<p>\r\nSorry ... but this web site is currently underconstruction. Thanks\r\n</p>');

-- --------------------------------------------------------

-- 
-- Struktura tabulky `directory`
-- 

CREATE TABLE `directory` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `directory`
-- 


-- --------------------------------------------------------

-- 
-- Struktura tabulky `file`
-- 

CREATE TABLE `file` (
  `id` int(11) NOT NULL auto_increment,
  `dir_id` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `title` tinytext collate latin1_general_ci NOT NULL,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `file`
-- 


-- --------------------------------------------------------

-- 
-- Struktura tabulky `group`
-- 

CREATE TABLE `group` (
  `gid` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY  (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `group`
-- 

INSERT INTO `group` VALUES (1, 'admins', 1);
INSERT INTO `group` VALUES (2, 'web-admins', 50);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `info`
-- 

CREATE TABLE `info` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `in_title` int(11) NOT NULL default '1',
  `href` tinytext collate latin1_general_ci NOT NULL,
  `in_menu` int(11) NOT NULL,
  `is_visible` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `info`
-- 

INSERT INTO `info` VALUES (1, 1, 'Welcome', 0, '', 0, 1, 1232545364);
INSERT INTO `info` VALUES (2, 1, 'CMS', 1, 'cms', 0, 1, 1232545364);
INSERT INTO `info` VALUES (3, 1, 'Index', 0, '', 0, 1, 1232545364);
INSERT INTO `info` VALUES (4, 1, 'Login', 1, 'login', 0, 1, 1232545364);
INSERT INTO `info` VALUES (5, 1, 'in', 1, 'in', 0, 1, 1232545364);
INSERT INTO `info` VALUES (6, 1, 'Page Manager', 1, 'page-manager', 1, 1, 1232545364);
INSERT INTO `info` VALUES (7, 1, 'Text File Manager', 1, 'text-file-manager', 1, 1, 1232545364);
INSERT INTO `info` VALUES (8, 1, 'File Manager', 1, 'file-manager', 1, 1, 1232549063);
INSERT INTO `info` VALUES (9, 1, 'User Manager', 1, 'user-manager', 1, 1, 1232546438);
INSERT INTO `info` VALUES (10, 1, 'File', 1, 'file', 0, 1, 1232546438);
INSERT INTO `info` VALUES (11, 1, 'Get File', 1, '<fl:compose />', 0, 1, 1232546438);
INSERT INTO `info` VALUES (12, 1, 'CSS', 1, 'css', 0, 1, 1232546438);
INSERT INTO `info` VALUES (13, 1, 'Get CSS', 1, '<web:composeTextFileUrl />', 0, 1, 1232546438);
INSERT INTO `info` VALUES (14, 1, 'JS', 1, 'js', 0, 1, 1232546438);
INSERT INTO `info` VALUES (15, 1, 'Get JS', 1, '<web:composeTextFileUrl />', 0, 1, 1232546438);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `language`
-- 

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `language` tinytext collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `language`
-- 

INSERT INTO `language` VALUES (1, '');
INSERT INTO `language` VALUES (2, 'cs');
INSERT INTO `language` VALUES (3, 'en');

-- --------------------------------------------------------

-- 
-- Struktura tabulky `page`
-- 

CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `page`
-- 

INSERT INTO `page` VALUES (1, 0);
INSERT INTO `page` VALUES (2, 0);
INSERT INTO `page` VALUES (3, 2);
INSERT INTO `page` VALUES (4, 2);
INSERT INTO `page` VALUES (5, 2);
INSERT INTO `page` VALUES (6, 5);
INSERT INTO `page` VALUES (7, 5);
INSERT INTO `page` VALUES (8, 5);
INSERT INTO `page` VALUES (9, 5);
INSERT INTO `page` VALUES (10, 0);
INSERT INTO `page` VALUES (11, 10);
INSERT INTO `page` VALUES (12, 0);
INSERT INTO `page` VALUES (13, 12);
INSERT INTO `page` VALUES (14, 0);
INSERT INTO `page` VALUES (15, 14);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `page_file`
-- 

CREATE TABLE `page_file` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `content` text collate latin1_general_ci NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=9 ;

-- 
-- Vypisuji data pro tabulku `page_file`
-- 

INSERT INTO `page_file` VALUES (1, 'CMS', '/* CSS Document */\r\n\r\nbody {\r\n  margin: 0 auto;\r\n}\r\n\r\nform {\r\n  margin: 0;\r\n  display: inline;\r\n}\r\n\r\ntextarea {\r\n  width: 100%;\r\n}\r\n\r\ninput[type=image] {\r\n	margin-bottom: -4px;\r\n}\r\n\r\n.closed .frame-body {\r\n	background: white;\r\n	display: none;\r\n}\r\n\r\n.pages-list td {\r\n  padding: 2px 10px;\r\n}\r\n\r\ntable.pages-table {\r\n  border-collapse: collapse;\r\n}\r\n/*\r\n.inn-1 {\r\n  background: #eeeeee;\r\n}\r\n\r\n.inn-2 {\r\n  background: #dddddd;\r\n}\r\n\r\n.inn-3 {\r\n  background: #cccccc;\r\n}\r\n\r\n.inn-4 {\r\n  background: #bbbbbb\r\n}\r\n*/\r\n.page-item {\r\n  clear: both;\r\n}\r\n\r\n.page-edit, .page-edit form {\r\n  text-align: left;\r\n}\r\n\r\n.float-right {\r\n  width: 400px;\r\n  float: right;\r\n}\r\n\r\n.clear {\r\n  clear: both;\r\n}\r\n\r\n.page-list span {\r\n  padding: 2px 5px;\r\n}\r\n\r\n.page-language-version {\r\n  display: inline;\r\n}\r\n\r\n.page-list li {\r\n  padding: 4px 0 0 0;\r\n}\r\n\r\n.page-id-col {\r\n  color: #888888;\r\n}\r\n\r\n.page-name {\r\n  font-weight: bold;\r\n}\r\n\r\n.page-file-list {\r\n  width: 100%;\r\n  border-top: 1px solid #cccccc;\r\n  border-collapse: collapse;\r\n}\r\n\r\n.file-name {\r\n  width: 130px;\r\n  font-weight: bold;\r\n}\r\n\r\n.file-content {\r\n  color: #777777;\r\n}\r\n\r\n.file-content-in {\r\n  width: 560px;\r\n  overflow: hidden;\r\n}\r\n\r\n.file-content-in .foo {\r\n  width: 1000px;\r\n}\r\n\r\n.file-tr td {\r\n  overflow: hidden;\r\n  padding: 2px 5px;\r\n  border-bottom: 1px solid #cccccc;\r\n}\r\n\r\n.frame-cover {\r\n  margin-top: 15px;\r\n}\r\n\r\ndiv.login {\r\n  width: 256px;\r\n  margin: 100px 0 0 100px;\r\n}\r\n  	\r\ndiv.login-head {\r\n  width: 100%;\r\n  height: 18px;\r\n  background: url(''/images/cms/login/login-head.png'') no-repeat;\r\n}\r\n  	\r\ndiv.login-in {\r\n  padding-bottom: 12px;\r\n  background: url(''/images/cms/login/login-body.png'') no-repeat left bottom;\r\n}\r\n\r\ndiv.login-form {\r\n  margin: 0 10px;\r\n  padding: 0 10px;\r\n}\r\n\r\ndiv.login-form form {\r\n  margin: 0\r\n}\r\n\r\ndiv.login-form p.login-head, p.login-message {\r\n  margin-top: 0;\r\n}\r\n\r\n.dir-list .dir-name input[type=submit] {\r\n  width: 100%;\r\n  text-align: left;\r\n  font-weight: bold;\r\n  font-family: Times;\r\n  font-size: 16px;\r\n  padding: 0;\r\n  background: white;\r\n  border: none;\r\n}\r\n\r\n.frame-cover {\r\n	border: 1px solid #04601C;\r\n}\r\n\r\n.frame-head {\r\n	background: #04601C;\r\n}\r\n\r\n.frame-head .frame-label {\r\n	color: white;\r\n	font-weight: bold;\r\n	float: left;\r\n	padding: 1px 0 1px 5px;\r\n}\r\n\r\n.frame-head .frame-close {\r\n	float: right;\r\n}\r\n\r\n.frame-body {\r\n	padding: 5px;\r\n	background: white;\r\n}\r\n\r\n.frames-used .frame-body {\r\n	display: none;\r\n}\r\n\r\nth.file-head-th {\r\n	background: #cccccc;\r\n}\r\n\r\n.dir-list {\r\n	width: 100%;\r\n	border-collapse: collapse;\r\n}\r\n\r\n.dir-list td, .dir-list th {\r\n	text-align: left;\r\n	padding: 2px 4px;\r\n}\r\n\r\n.dir-list .file-edit, .dir-list .dir-edit {\r\n	width: 60px;\r\n}\r\n\r\n.dir-list .file-icon, .dir-list .file-id, .dir-list .file-type, .dir-list .dir-icon, .dir-list .dir-id, .dir-list .dir-type {\r\n	width: 20px;\r\n}\r\n\r\n.dir-list .file-name, .dir-list .dir-name {\r\n	width: 300px;\r\n}\r\n\r\n.dir-list tr.even, .dir-list tr.even .dir-name input {\r\n	background: #eeeeee;\r\n}\r\n\r\n.frame-body .error {\r\n	margin: 2px 0;\r\n	padding: 0 0 1px 18px;\r\n	color: white;\r\n	background: url(''/images/error.png'') #bd2828 no-repeat 1px 3px;\r\n}\r\n\r\nbody {\r\n	overflow: scroll;\r\n	background: #cccccc;\r\n}\r\n\r\n.cms .head {\r\n	height: 50px;\r\n	padding: 1px;\r\n	background: url(''/images/cms/design/head-line.png'') repeat-x;\r\n}\r\n\r\n.cms .head .logout-form {\r\n	margin-right: 20px;\r\n	float: right;\r\n}\r\n\r\n.cms .head .logout-form form {\r\n	margin: 0;\r\n}\r\n\r\n.cms .head .logout-form p {\r\n	margin: 2px 0 9px 0;\r\n}\r\n\r\n.cms .head .logout-form input[type=submit] {\r\n	width: 30px;\r\n	height: 31px;\r\n	color: transparent;\r\n	background: url(''/images/cms/design/logout-icon.png'') no-repeat;\r\n	border: none;\r\n}\r\n\r\n.cms .head .user-info {\r\n	font-size: 11px;\r\n	float: right;\r\n	margin: 2px 20px 0 2px;\r\n	padding: 2px;\r\n	background: url(''/images/cms/design/dot-transparent.png'');\r\n	border: 1px solid #011D09;\r\n}\r\n\r\n.cms .head .user-info .user-group {\r\n	margin-left: 10px;\r\n	float: left;\r\n}\r\n\r\n.cms .head .user-info .user-name {\r\n	float: left;\r\n	font-weight: bold;\r\n}\r\n\r\n.cms .head .cms-menu {\r\n	position: absolute;\r\n	margin: 10px 0pt 0pt 15px;\r\n}\r\n\r\n.cms .head .cms-menu .menu-root {\r\n	width: 170px;\r\n	display: block;\r\n	font-weight: bold;\r\n	color: white;\r\n	cursor: Pointer;\r\n}\r\n\r\n.cms .head .cms-menu .menu {\r\n	width: 170px;\r\n	margin: 11px 0 0;\r\n	padding: 2px 10px 10px;\r\n	display: none;\r\n	padding: 2px 0 10px 0;\r\n	background: #eeeeee;\r\n	border: 1px solid #011D09;\r\n	border-top: none;\r\n}\r\n\r\n\r\n.cms .head .cms-menu .menu .menu-item a {\r\n	width: 150px;\r\n	padding: 1px 10px;\r\n	display: block;\r\n	color: #04601C;\r\n	text-decoration: none;\r\n}\r\n\r\n.cms .head .cms-menu .menu .menu-item a:hover {\r\n	color: white;\r\n	background: #04601C;\r\n}\r\n\r\n.cms .head .cms-menu:hover .menu {\r\n	display: block;\r\n}\r\n\r\n.cms .head .cms-menu ul {\r\n	margin: 0;\r\n	padding: 0;\r\n	list-style: none;\r\n}\r\n\r\n.cms .body {\r\n	margin: 0 100px 30px 100px;\r\n}', 1);
INSERT INTO `page_file` VALUES (2, 'Confirm', 'function addEvent (obj, ev, func, b) {\r\n  if(obj.addEventListener) {\r\n    obj.addEventListener(ev, func, b);\r\n  } else {\r\n    obj.attachEvent("on" + ev, func);\r\n  }\r\n}\r\n\r\naddEvent(window, "load", initConfirm, false);\r\n\r\nfunction initConfirm(event) {\r\n  confs = document.getElementsByTagName(''input'');\r\n  var cofs = new Array();\r\n  for(var i = 0; i < confs.length; i ++) {\r\n    if(confs[i].className == "confirm") {\r\n      cofs[cofs.length] = confs[i];\r\n      confs[i].onclick = function(event) { return confirm("Opravdu?"); }\r\n    }\r\n  }\r\n}', 2);
INSERT INTO `page_file` VALUES (3, 'Editor', '// JavaScript Document\r\n\r\nfunction Editor(ta) {\r\n  var Own = this;\r\n  var TextArea = ta;\r\n  var Controls = new Object();\r\n  var FindPosition = -1;\r\n  \r\n  Controls.Form = document.createElement(''form'');\r\n  Controls.Form.name = "editor-controls-form";\r\n  Controls.Form.method = "post";\r\n  Controls.Form.action = "";\r\n  \r\n  Controls.Cover = document.createElement(''div'');\r\n  Controls.Cover.className = "editor-panel";\r\n  \r\n  Controls.ElementsCover = document.createElement(''div'');\r\n  Controls.ElementsCover.className = "editor-elements-panel";\r\n  \r\n  Controls.HTMLText = document.createElement(''span'');\r\n  Controls.HTMLText.innerHTML = "HTML";\r\n  \r\n  Controls.FindCover = document.createElement(''div'');\r\n  Controls.FindCover.className = "editor-find-panel";\r\n  \r\n  Controls.FindText = document.createElement(''span'');\r\n  Controls.FindText.innerHTML = "Find";\r\n  \r\n  Controls.RowsCover = document.createElement(''div'');\r\n  Controls.RowsCover.className = "editor-find-panel";\r\n  \r\n  Controls.RowsText = document.createElement(''span'');\r\n  Controls.RowsText.innerHTML = "Rows";\r\n  \r\n  Controls.ShowHideCover = document.createElement(''div'');\r\n  Controls.ShowHideCover.className = "editor-find-panel";\r\n  \r\n  Controls.ShowHideText = document.createElement(''span'');\r\n  Controls.ShowHideText.innerHTML = "";\r\n  \r\n  Controls.H1 = document.createElement(''input'');\r\n  Controls.H1.type = "button";\r\n  Controls.H1.name = "h1";\r\n  Controls.H1.value = "H1";\r\n  Controls.H1.title = "Insert headline level 1.";\r\n  \r\n  Controls.H2 = document.createElement(''input'');\r\n  Controls.H2.type = "button";\r\n  Controls.H2.name = "h2";\r\n  Controls.H2.value = "H2";\r\n  Controls.H2.title = "Insert headline level 2.";\r\n  \r\n  Controls.H3 = document.createElement(''input'');\r\n  Controls.H3.type = "button";\r\n  Controls.H3.name = "h3";\r\n  Controls.H3.value = "H3";\r\n  Controls.H3.title = "Insert headline level 3.";\r\n  \r\n  Controls.H4 = document.createElement(''input'');\r\n  Controls.H4.type = "button";\r\n  Controls.H4.name = "h4";\r\n  Controls.H4.value = "H4";\r\n  Controls.H4.title = "Insert headline level 4.";\r\n  \r\n  Controls.P = document.createElement(''input'');\r\n  Controls.P.type = "button";\r\n  Controls.P.name = "P";\r\n  Controls.P.value = "P";\r\n  Controls.P.title = "Insert paragraph.";\r\n  \r\n  Controls.IMG = document.createElement(''input'');\r\n  Controls.IMG.type = "button";\r\n  Controls.IMG.name = "IMG";\r\n  Controls.IMG.value = "IMG";\r\n  Controls.IMG.title = "Insert image.";\r\n  \r\n  Controls.Find = document.createElement(''input'');\r\n  Controls.Find.type = "text";\r\n  Controls.Find.name = "find";\r\n  \r\n  Controls.Next = document.createElement(''input'');\r\n  Controls.Next.type = "button";\r\n  Controls.Next.name = "next";\r\n  Controls.Next.value = "Next";\r\n  Controls.Next.title = "Find in text.";\r\n  \r\n  Controls.RowAdd = document.createElement(''input'');\r\n  Controls.RowAdd.type = "button";\r\n  Controls.RowAdd.name = "row-add";\r\n  Controls.RowAdd.value = "+";\r\n  Controls.RowAdd.title = "Add row";\r\n  \r\n  Controls.RowRem = document.createElement(''input'');\r\n  Controls.RowRem.type = "button";\r\n  Controls.RowRem.name = "row-rem";\r\n  Controls.RowRem.value = "-";\r\n  Controls.RowRem.title = "Remove row";\r\n  \r\n  Controls.ShowHide = document.createElement(''input'');\r\n  Controls.ShowHide.type = "button";\r\n  Controls.ShowHide.name = "show-hide";\r\n  Controls.ShowHide.value = "Show / Hide";\r\n  Controls.ShowHide.title = "Show/Hide text field";\r\n  \r\n  Controls.ElementsCover.appendChild(Controls.HTMLText);\r\n  Controls.ElementsCover.appendChild(Controls.H1);\r\n  Controls.ElementsCover.appendChild(Controls.H2);\r\n  Controls.ElementsCover.appendChild(Controls.H3);\r\n  Controls.ElementsCover.appendChild(Controls.H4);\r\n  Controls.ElementsCover.appendChild(Controls.P);\r\n  Controls.ElementsCover.appendChild(Controls.IMG);\r\n  \r\n  Controls.FindCover.appendChild(Controls.FindText);\r\n  Controls.FindCover.appendChild(Controls.Find);\r\n  Controls.FindCover.appendChild(Controls.Next);\r\n  \r\n  Controls.RowsCover.appendChild(Controls.RowsText);\r\n  Controls.RowsCover.appendChild(Controls.RowAdd);\r\n  Controls.RowsCover.appendChild(Controls.RowRem);\r\n  \r\n  Controls.ShowHideCover.appendChild(Controls.ShowHideText);\r\n  Controls.ShowHideCover.appendChild(Controls.ShowHide);\r\n\r\n\r\n  \r\n  Controls.Form.appendChild(Controls.ElementsCover);\r\n  Controls.Form.appendChild(Controls.FindCover);\r\n  Controls.Form.appendChild(Controls.RowsCover);\r\n  Controls.Form.appendChild(Controls.ShowHideCover);\r\n  Controls.Cover.appendChild(Controls.Form);\r\n  TextArea.parentNode.insertBefore(Controls.Cover, TextArea);\r\n  \r\n  this.init = function() {\r\n    this.addEvent(Controls.H1, "click", Own.h1Click, false);\r\n    this.addEvent(Controls.H2, "click", Own.h2Click, false);\r\n    this.addEvent(Controls.H3, "click", Own.h3Click, false);\r\n    this.addEvent(Controls.H4, "click", Own.h4Click, false);\r\n    this.addEvent(Controls.P, "click", Own.pClick, false);\r\n    this.addEvent(Controls.IMG, "click", Own.imgClick, false);\r\n    \r\n    this.addEvent(Controls.Find, "focus", Own.findFocus, false);\r\n    this.addEvent(Controls.Next, "click", Own.nextClick, false);\r\n    \r\n    this.addEvent(Controls.RowAdd, "click", Own.rowAddClick, false);\r\n    this.addEvent(Controls.RowRem, "click", Own.rowRemClick, false);\r\n    \r\n    this.addEvent(Controls.ShowHide, "click", Own.showHideClick, false);\r\n    \r\n    this.addEvent(TextArea, "keypress", Own.textAreaKeyPress, false);\r\n    \r\n    this.addEvent(Controls.Form, "submit", Own.formSubmit, false);\r\n\r\n    if(TextArea.className.indexOf(''editor-closed'') != -1) {\r\n      Own.showHideClick(null);\r\n    }\r\n  }\r\n  \r\n  this.h1Click = function (event) {\r\n    Own.insertText(TextArea, "<h1>" + window.prompt("Zadejte nadpis H1: ") + "</h1>\\n");\r\n  }\r\n  \r\n  this.h2Click = function (event) {\r\n    Own.insertText(TextArea, "<h2>" + window.prompt("Zadejte nadpis H2: ") + "</h2>\\n");\r\n  }\r\n  \r\n  this.h3Click = function (event) {\r\n    Own.insertText(TextArea, "<h3>" + window.prompt("Zadejte nadpis H3: ") + "</h3>\\n");\r\n  }\r\n  \r\n  this.h4Click = function (event) {\r\n    Own.insertText(TextArea, "<h4>" + window.prompt("Zadejte nadpis H4: ") + "</h4>\\n");\r\n  }\r\n  \r\n  this.pClick = function (event) {\r\n    Own.insertText(TextArea, "<p>\\n\\n</p>\\n", 4);\r\n  }\r\n  \r\n  this.imgClick = function (event) {\r\n    Own.insertText(TextArea, "<img src=\\"" + window.prompt("Zadejte cestu k obrazku: ") + "\\" />\\n");\r\n  }\r\n  \r\n  this.findFocus = function (event) {\r\n    FindPosition = TextArea.selectionStart;\r\n  }\r\n  \r\n  this.nextClick = function (event) {\r\n    if(Controls.Find.value.length > 0) {\r\n      FindPosition = TextArea.value.indexOf(Controls.Find.value, FindPosition + 1);\r\n      if(FindPosition == -1) {\r\n        FindPosition = -1;\r\n        //Own.nextClick(event);\r\n        alert(" ''"+ Controls.Find.value + "'' not found!");\r\n      } else {\r\n        TextArea.setSelectionRange(FindPosition, (FindPosition + Controls.Find.value.length));\r\n        TextArea.focus();\r\n      }\r\n    }\r\n  }\r\n  \r\n  this.rowAddClick = function (event) {\r\n    TextArea.rows += 2; \r\n  }\r\n  \r\n  this.rowRemClick = function (event) {\r\n    if(TextArea.rows > 2) {\r\n      TextArea.rows -= 2;\r\n    }\r\n  }\r\n  \r\n  this.showHideClick = function (event) {\r\n    if(TextArea.style.display == "") {\r\n      TextArea.style.display = "none";\r\n    } else {\r\n      TextArea.style.display = "";\r\n    }\r\n  }\r\n  \r\n  this.textAreaKeyPress = function (event) {\r\n  	if(event.keyCode == 9 && !event.ctrlKey) {\r\n  		Own.insertText(TextArea, ''\\t'');\r\n  		TextArea.focus();\r\n  		Own.stopEvent(event);\r\n  	}\r\n  }\r\n  \r\n  this.formSubmit = function (event) {\r\n  	Own.stopEvent(event);\r\n  }\r\n  \r\n  this.insertText = function (textBox, strNewText, pos){\r\n  	var tb = textBox;\r\n  	var first = tb.value.slice(0, tb.selectionStart);\r\n  	var second = tb.value.slice(tb.selectionEnd);\r\n  	var sta = tb.selectionStart + strNewText.length;\r\n  	tb.value = first + strNewText + second;\r\n  	if(pos != null) {\r\n      sta = sta - strNewText.length + pos;\r\n    }\r\n  	if(tb.setSelectionRange) {\r\n      tb.setSelectionRange(sta,sta);\r\n    }\r\n  	tb.focus();\r\n  }\r\n\r\n  this.addEvent = function (obj, ev, func, b) {\r\n		if(obj.addEventListener) {\r\n			obj.addEventListener(ev, func, b);\r\n		} else {\r\n			obj.attachEvent("on" + ev, func);\r\n		}\r\n	}\r\n	\r\n	this.stopEvent = function (event) {\r\n    if(navigator.appName != "Microsoft Internet Explorer") {\r\n      event.stopPropagation();\r\n      event.preventDefault();\r\n    } else {\r\n      event.cancelBubble = true;\r\n      event.returnValue = false;\r\n    }\r\n  }\r\n	\r\n  this.init();\r\n}\r\n', 2);
INSERT INTO `page_file` VALUES (4, 'Closer', '// JavaScript Document\r\n\r\nfunction Closer(frameCover) {\r\n  var Own = this;\r\n  var FrameCover = frameCover;\r\n  var Anchor;\r\n  var Head;\r\n  var Content;\r\n  \r\n  this.init = function() {\r\n    var elms = frameCover.getElementsByTagName(''*'');\r\n    for(var i = 0; i < elms.length; i ++) {\r\n      elm = elms[i];\r\n      if(elm.className.indexOf("frame-head") != -1) {\r\n        Head = elm;\r\n      } else if(elm.className.indexOf("frame-body") != -1) {\r\n        Content = elm;\r\n      } else if(elm.className.indexOf("click-able-roll") != -1) {\r\n        Anchor = elm;\r\n      }\r\n    }\r\n    \r\n    Own.addEvent(Head, "click", Own.headClick, false);\r\n  }\r\n  \r\n  this.headClick = function(event) {\r\n    if(Content.style.display == "none") {\r\n      Content.style.display = "block";\r\n    } else {\r\n      Content.style.display = "none";\r\n    }\r\n    Own.stopEvent(event);\r\n  }\r\n\r\n  this.addEvent = function (obj, ev, func, b) {\r\n		if(obj.addEventListener) {\r\n			obj.addEventListener(ev, func, b);\r\n		} else {\r\n			obj.attachEvent("on" + ev, func);\r\n		}\r\n	}\r\n	\r\n	this.stopEvent = function (event) {\r\n    if(navigator.appName != "Microsoft Internet Explorer") {\r\n      event.stopPropagation();\r\n      event.preventDefault();\r\n    } else {\r\n      event.cancelBubble = true;\r\n      event.returnValue = false;\r\n    }\r\n  }\r\n  \r\n  this.init();\r\n}\r\n', 2);
INSERT INTO `page_file` VALUES (5, 'FileName', '// JavaScript Document\r\n\r\nfunction FileName(inputFile, inputText) {\r\n  if(inputFile.type != "file") {\r\n    alert("Passed object isn''t input[type=file]!");\r\n    return;\r\n  }\r\n  \r\n  var Own = this;\r\n  var InputFile = inputFile;\r\n  var InputText = inputText;\r\n  \r\n  this.init = function () {\r\n    Own.addEvent(InputFile, "change", Own.inputFileOnChange, false);\r\n  }\r\n  \r\n  this.inputFileOnChange = function (event) {\r\n    if(InputFile.value.indexOf(''\\\\'') != -1 ) {\r\n      var pos2 = -1;\r\n      do {\r\n        pos = pos2;\r\n        pos2 = InputFile.value.indexOf(''\\\\'', pos + 1);\r\n      } while(pos2 != -1);\r\n      InputText.value = InputFile.value.substring(pos + 1);\r\n    } else {\r\n      InputText.value = InputFile.value;\r\n    }\r\n  }\r\n\r\n  this.addEvent = function (obj, ev, func, b) {\r\n		if(obj.addEventListener) {\r\n			obj.addEventListener(ev, func, b);\r\n		} else {\r\n			obj.attachEvent("on" + ev, func);\r\n		}\r\n	}\r\n	\r\n	this.stopEvent = function (event) {\r\n    if(navigator.appName != "Microsoft Internet Explorer") {\r\n      event.stopPropagation();\r\n      event.preventDefault();\r\n    } else {\r\n      event.cancelBubble = true;\r\n      event.returnValue = false;\r\n    }\r\n  }\r\n  \r\n  Own.init();\r\n}\r\n', 2);
INSERT INTO `page_file` VALUES (6, 'CMS Init', 'function addEvent (obj, ev, func, b) {\r\n  if(obj.addEventListener) {\r\n    obj.addEventListener(ev, func, b);\r\n  } else {\r\n    obj.attachEvent("on" + ev, func);\r\n  }\r\n}\r\n\r\naddEvent(window, "load", initEditors, false);\r\n\r\nfunction initEditors(event) {\r\n  var tas = document.getElementsByTagName(''textarea'');\r\n\r\n  for(var i = 0; i < tas.length; i ++) {\r\n    new Editor(tas[i]);\r\n  }\r\n}\r\n\r\naddEvent(window, "load", initClosers, false);\r\n\r\nfunction initClosers() {\r\n  var divs = document.getElementsByTagName(''div'');\r\n  for(var i = 0; i < divs.length; i ++) {\r\n    if(divs[i].className.indexOf(''frame frame-cover'') != -1) {\r\n      new Closer(divs[i]);\r\n    }\r\n  }\r\n}\r\n\r\naddEvent(window, "load", fileNameInit);\r\n\r\nfunction fileNameInit(event) {\r\n	var inpts = document.getElementsByTagName(''input'');\r\n	var textInput = null;\r\n	var fileInput = null;\r\n	for(var i = 0; i < inpts.length; i ++) {\r\n		if(inpts[i].name == "file-name") {\r\n			textInput = inpts[i];\r\n		}\r\n		if(inpts[i].name == "file-rs") {\r\n			fileInput = inpts[i];\r\n		}\r\n		if(textInput != null && fileInput != null) {\r\n			new FileName(fileInput, textInput);\r\n			textInput = null;\r\n			fileInput = null;\r\n		}\r\n	}\r\n}', 2);
INSERT INTO `page_file` VALUES (7, 'Editor css', '.editor-panel {\r\n  background: #cccccc;\r\n  border: 1px solid black;\r\n}\r\n\r\n.editor-panel input[type=button] {\r\n  background: white;\r\n  border: 1px solid black;\r\n}\r\n\r\n.editor-panel input[type=button], .editor-panel span {\r\n  margin: 3px 1px;\r\n}\r\n\r\n.editor-panel div {\r\n  display: inline-block;\r\n  padding: 0 5px;\r\n  border-left: 1px solid black;\r\n}\r\n\r\n.editor-panel div.editor-elements-panel {\r\n  border: none;\r\n}\r\n\r\n.editor-panel .editor-find-panel input[type=text] {\r\n  width: 182px;\r\n}', 1);
INSERT INTO `page_file` VALUES (8, 'page', '.edit-prop div, .text-file-name, .text-file-type {\r\n  float: left;\r\n  padding: 6px 8px;\r\n  margin: 2px;\r\n  background: #eeeeee;\r\n}\r\n\r\n.edit-prop div.clear {\r\n  float: none;\r\n  padding: 0;\r\n  margin: 0;\r\n}\r\n.edit-tl-start, .edit-tl-end, .edit-content .edit-head, .edit-content .edit-content, .text-file-content {\r\n  padding: 6px 8px;\r\n  margin: 2px;\r\n  background: #eeeeee;\r\n}\r\n\r\n.edit-submit, .text-file-submit {\r\n  padding: 6px 8px;\r\n  margin: 2px;\r\n  text-align: right;\r\n  background: #eeeeee;\r\n}', 1);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `page_file_inc`
-- 

CREATE TABLE `page_file_inc` (
  `file_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY  (`file_id`,`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `page_file_inc`
-- 

INSERT INTO `page_file_inc` VALUES (1, 2, 1);
INSERT INTO `page_file_inc` VALUES (2, 5, 1);
INSERT INTO `page_file_inc` VALUES (3, 5, 1);
INSERT INTO `page_file_inc` VALUES (4, 5, 1);
INSERT INTO `page_file_inc` VALUES (5, 5, 1);
INSERT INTO `page_file_inc` VALUES (6, 5, 1);
INSERT INTO `page_file_inc` VALUES (7, 5, 1);
INSERT INTO `page_file_inc` VALUES (8, 6, 1);
INSERT INTO `page_file_inc` VALUES (8, 7, 1);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `user`
-- 

CREATE TABLE `user` (
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `surname` tinytext collate latin1_general_ci NOT NULL,
  `login` tinytext collate latin1_general_ci NOT NULL,
  `password` tinytext collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `user`
-- 

INSERT INTO `user` VALUES (1, 1, 'admin', 'admin', 'admin', '8ba18c4a810f6787a603eda15ecad4241e80cad8');

-- --------------------------------------------------------

-- 
-- Struktura tabulky `user_in_group`
-- 

CREATE TABLE `user_in_group` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  PRIMARY KEY  (`uid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `user_in_group`
-- 

INSERT INTO `user_in_group` VALUES (1, 1);
INSERT INTO `user_in_group` VALUES (1, 2);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `user_log`
-- 

CREATE TABLE `user_log` (
  `id` int(11) NOT NULL auto_increment,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `login_timestamp` int(11) NOT NULL,
  `logout_timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

-- 
-- Vypisuji data pro tabulku `user_log`
-- 

INSERT INTO `user_log` VALUES (1, 1638996, 1, 1232549541, 1232549019, 1232549547);
INSERT INTO `user_log` VALUES (2, 1823209, 1, 1232549600, 1232549567, 1232549602);
INSERT INTO `user_log` VALUES (3, 1058292, 1, 1232549623, 1232549622, 0);
