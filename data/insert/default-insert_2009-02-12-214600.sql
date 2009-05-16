-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Poèítaè: localhost
-- Vygenerováno: Pátek 13. února 2009, 21:45
-- Verze MySQL: 5.0.41
-- Verze PHP: 5.2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Databáze: `tmp_db_03`
-- 

-- --------------------------------------------------------

-- 
-- Struktura tabulky `article`
-- 

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `article`
-- 


-- --------------------------------------------------------

-- 
-- Struktura tabulky `article_content`
-- 

CREATE TABLE `article_content` (
  `article_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `head` text collate latin1_general_ci,
  `content` text collate latin1_general_ci,
  `author` tinytext collate latin1_general_ci,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`article_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `article_content`
-- 


-- --------------------------------------------------------

-- 
-- Struktura tabulky `article_line`
-- 

CREATE TABLE `article_line` (
  `id` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `article_line`
-- 

INSERT INTO `article_line` VALUES (1, 'News');

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

INSERT INTO `content` VALUES (2, 1, '<login:init group="web-admins" />', '', '<link rel="stylesheet" href="~/css/cms.css" type="text/css" />', '<web:content />');
INSERT INTO `content` VALUES (3, 1, '', '', '', '<login:redirectWhenNotLogged pageId="4" /><login:redirectWhenLogged pageId="6" />');
INSERT INTO `content` VALUES (4, 1, '', '', '', '<div class="login">\r\n  <div class="login-head"></div>\r\n  <div class="login-in">\r\n    <login:form group="web-admins" pageId="6" />\r\n  </div>\r\n</div>');
INSERT INTO `content` VALUES (5, 1, '', '', '<link rel="stylesheet" href="~/css/editor.css" type="text/css" />\r\n<script type="text/javascript" src="~/js/Closer.js"></script>\r\n<script type="text/javascript" src="~/js/Confirm.js"></script>\r\n<script type="text/javascript" src="~/js/Editor.js"></script>\r\n<script type="text/javascript" src="~/js/FileName.js"></script>\r\n<script type="text/javascript" src="~/js/init.js"></script>\r\n<script type="text/javascript" src="~/tiny-mce/tiny_mce.js"></script>\r\n<script type="text/javascript" src="~/js/initTiny.js"></script>', '<div class="cms">\r\n	<div class="head">\r\n		<login:logout group="web-admins" pageId="4" />\r\n		<login:info />\r\n		<div class="cms-menu">\r\n			<span class="menu-root">Menu</span>\r\n			<web:menu parentId="5" />\r\n		</div>\r\n	</div>\r\n	<div class="body">\r\n		<web:content />\r\n	</div>\r\n</div>');
INSERT INTO `content` VALUES (6, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showList editable="true" />');
INSERT INTO `content` VALUES (7, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showFiles editable="true" />');
INSERT INTO `content` VALUES (8, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<p><fl:showUploadForm /></p><p><fl:showNewDirectoryForm /></p><p><fl:showDirectory /></p>');
INSERT INTO `content` VALUES (9, 1, '<php:register tagPrefix="user" classPath="php.libs.User" />', '<php:unregister tagPrefix="user" />', '', '<p>\r\n	<user:management />\r\n</p>');
INSERT INTO `content` VALUES (10, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<web:content />');
INSERT INTO `content` VALUES (11, 1, '', '', '', '<fl:get />');
INSERT INTO `content` VALUES (12, 1, '', '', '', '<web:content />');
INSERT INTO `content` VALUES (13, 1, '', '', '', '<web:textFile type="css" />');
INSERT INTO `content` VALUES (14, 1, '', '', '', '<web:content />');
INSERT INTO `content` VALUES (15, 1, '', '', '', '<web:textFile type="js" />');
INSERT INTO `content` VALUES (1, 1, '', '', '', '<h2>Welcome</h2>\r\n<p>\r\nSorry ... but this web site is currently underconstruction. Thanks\r\n</p>\r\n\r\n<web:redirectTo pageId="27" langId="1" browser="ie" />');
INSERT INTO `content` VALUES (16, 1, '<php:register tagPrefix="artc" classPath="php.libs.Article" />', '<php:unregister tagPrefix="artc" />', '', '<p>\r\n	<artc:process />\r\n</p>\r\n<p>\r\n	<artc:setLine method="get" />\r\n</p>\r\n<p>\r\n	<artc:showManagement />\r\n</p>\r\n<p>\r\n	<artc:showLines editable="true" />\r\n</p>\r\n<p>\r\n	<artc:createLine />\r\n</p>');
INSERT INTO `content` VALUES (17, 1, '<php:register tagPrefix="gb" classPath="php.libs.Guestbook" />', '<php:unregister tagPrefix="gb" />', '', '<gb:show guestbookId="1" editable="true" useFrame="true" />');
INSERT INTO `content` VALUES (1, 0, '', '', NULL, '<h2>Welcome</h2>\r\n<p>\r\nSorry ... but this web site is currently underconstruction. Thanks\r\n</p>');

-- --------------------------------------------------------

-- 
-- Struktura tabulky `counter`
-- 

CREATE TABLE `counter` (
  `ip` varchar(15) collate latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL,
  PRIMARY KEY  (`counter_id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `counter`
-- 


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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;

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
INSERT INTO `group` VALUES (3, 'web', 254);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `guestbook`
-- 

CREATE TABLE `guestbook` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `content` text collate latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `guestbook_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `guestbook`
-- 


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
  `page_pos` int(11) NOT NULL,
  `is_visible` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `info`
-- 

INSERT INTO `info` VALUES (1, 1, 'Welcome', 0, '', 0, 1, 1, 1234531923);
INSERT INTO `info` VALUES (2, 1, 'CMS', 1, 'cms', 0, 2, 1, 1234554118);
INSERT INTO `info` VALUES (3, 1, 'Index', 0, '', 0, 3, 1, 1232545364);
INSERT INTO `info` VALUES (4, 1, 'Login', 1, 'login', 0, 4, 1, 1232545364);
INSERT INTO `info` VALUES (5, 1, 'in', 0, 'in', 1, 5, 1, 1234389400);
INSERT INTO `info` VALUES (6, 1, 'Page Manager', 1, 'page-manager', 1, 6, 1, 1234389321);
INSERT INTO `info` VALUES (7, 1, 'Text File Manager', 1, 'text-file-manager', 1, 7, 1, 1233078739);
INSERT INTO `info` VALUES (8, 1, 'File Manager', 1, 'file-manager', 1, 8, 1, 1233078749);
INSERT INTO `info` VALUES (9, 1, 'User Manager', 1, 'user-manager', 1, 9, 1, 1233078763);
INSERT INTO `info` VALUES (10, 1, 'File', 1, 'file', 0, 10, 1, 1233771181);
INSERT INTO `info` VALUES (11, 1, 'Get File', 1, '<fl:compose />', 0, 11, 1, 1233771197);
INSERT INTO `info` VALUES (12, 1, 'CSS', 1, 'css', 0, 12, 1, 1233771214);
INSERT INTO `info` VALUES (13, 1, 'Get CSS', 1, '<web:composeTextFileUrl />', 0, 13, 1, 1233771224);
INSERT INTO `info` VALUES (14, 1, 'JS', 1, 'js', 0, 14, 1, 1233771232);
INSERT INTO `info` VALUES (15, 1, 'Get JS', 1, '<web:composeTextFileUrl />', 0, 15, 1, 1233771241);
INSERT INTO `info` VALUES (16, 1, 'Article Manager', 1, 'article-manager', 1, 16, 1, 1233078775);
INSERT INTO `info` VALUES (17, 1, 'Guestbook Manager', 1, 'guestbook-manager', 1, 17, 1, 1234282979);

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
INSERT INTO `page` VALUES (16, 5);
INSERT INTO `page` VALUES (17, 5);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `page_file`
-- 

CREATE TABLE `page_file` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext collate latin1_general_ci NOT NULL,
  `content` text collate latin1_general_ci NOT NULL,
  `for_all` int(2) NOT NULL default '1',
  `for_msie6` int(2) NOT NULL default '0',
  `for_msie7` int(2) NOT NULL default '0',
  `for_msie8` int(2) NOT NULL default '0',
  `for_firefox` int(2) NOT NULL default '0',
  `for_opera` int(2) NOT NULL default '0',
  `for_safari` int(2) NOT NULL default '0',
  `type` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=13 ;

-- 
-- Vypisuji data pro tabulku `page_file`
-- 


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


-- --------------------------------------------------------

-- 
-- Struktura tabulky `page_right`
-- 

CREATE TABLE `page_right` (
  `pid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY  (`pid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Vypisuji data pro tabulku `page_right`
-- 

INSERT INTO `page_right` VALUES (0, 2, 102);
INSERT INTO `page_right` VALUES (0, 2, 103);
INSERT INTO `page_right` VALUES (0, 3, 101);
INSERT INTO `page_right` VALUES (2, 1, 102);
INSERT INTO `page_right` VALUES (2, 1, 103);
INSERT INTO `page_right` VALUES (2, 3, 101);
INSERT INTO `page_right` VALUES (3, 1, 102);
INSERT INTO `page_right` VALUES (3, 1, 103);
INSERT INTO `page_right` VALUES (3, 3, 101);
INSERT INTO `page_right` VALUES (4, 1, 102);
INSERT INTO `page_right` VALUES (4, 1, 103);
INSERT INTO `page_right` VALUES (4, 3, 101);
INSERT INTO `page_right` VALUES (5, 1, 102);
INSERT INTO `page_right` VALUES (5, 1, 103);
INSERT INTO `page_right` VALUES (5, 2, 101);
INSERT INTO `page_right` VALUES (6, 1, 102);
INSERT INTO `page_right` VALUES (6, 1, 103);
INSERT INTO `page_right` VALUES (6, 2, 101);
INSERT INTO `page_right` VALUES (7, 1, 102);
INSERT INTO `page_right` VALUES (7, 1, 103);
INSERT INTO `page_right` VALUES (7, 2, 101);
INSERT INTO `page_right` VALUES (8, 1, 102);
INSERT INTO `page_right` VALUES (8, 1, 103);
INSERT INTO `page_right` VALUES (8, 2, 101);
INSERT INTO `page_right` VALUES (9, 1, 102);
INSERT INTO `page_right` VALUES (9, 1, 103);
INSERT INTO `page_right` VALUES (9, 2, 101);
INSERT INTO `page_right` VALUES (10, 1, 102);
INSERT INTO `page_right` VALUES (10, 1, 103);
INSERT INTO `page_right` VALUES (10, 3, 101);
INSERT INTO `page_right` VALUES (11, 1, 102);
INSERT INTO `page_right` VALUES (11, 1, 103);
INSERT INTO `page_right` VALUES (11, 3, 101);
INSERT INTO `page_right` VALUES (12, 1, 102);
INSERT INTO `page_right` VALUES (12, 1, 103);
INSERT INTO `page_right` VALUES (12, 3, 101);
INSERT INTO `page_right` VALUES (13, 1, 102);
INSERT INTO `page_right` VALUES (13, 1, 103);
INSERT INTO `page_right` VALUES (13, 3, 101);
INSERT INTO `page_right` VALUES (14, 1, 102);
INSERT INTO `page_right` VALUES (14, 1, 103);
INSERT INTO `page_right` VALUES (14, 3, 101);
INSERT INTO `page_right` VALUES (15, 1, 102);
INSERT INTO `page_right` VALUES (15, 1, 103);
INSERT INTO `page_right` VALUES (15, 3, 101);
INSERT INTO `page_right` VALUES (16, 1, 102);
INSERT INTO `page_right` VALUES (16, 1, 103);
INSERT INTO `page_right` VALUES (16, 2, 101);
INSERT INTO `page_right` VALUES (17, 1, 102);
INSERT INTO `page_right` VALUES (17, 1, 103);
INSERT INTO `page_right` VALUES (17, 2, 101);

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

INSERT INTO `user` VALUES (1, 1, 'admin', 'admin', 'admin', 'b49a387e1143eccc5d6cb585d49290c2e2a85145');
INSERT INTO `user` VALUES (2, 0, 'HTML', 'koder', 'htmlkoder', 'e72aef7f14a3e8348ca7930b5f8b008b0ba94d2e');

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
INSERT INTO `user_in_group` VALUES (2, 2);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=76 ;
