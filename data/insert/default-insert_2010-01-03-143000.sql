-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2010 at 02:29 PM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `tmp_wfw_wp`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `article`
--


-- --------------------------------------------------------

--
-- Table structure for table `article_content`
--

DROP TABLE IF EXISTS `article_content`;
CREATE TABLE IF NOT EXISTS `article_content` (
  `article_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `head` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci,
  `author` tinytext COLLATE utf8_czech_ci,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`article_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `article_content`
--


-- --------------------------------------------------------

--
-- Table structure for table `article_line`
--

DROP TABLE IF EXISTS `article_line`;
CREATE TABLE IF NOT EXISTS `article_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `article_line`
--


-- --------------------------------------------------------

--
-- Table structure for table `article_line_right`
--

DROP TABLE IF EXISTS `article_line_right`;
CREATE TABLE IF NOT EXISTS `article_line_right` (
  `line_id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`line_id`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;

--
-- Dumping data for table `article_line_right`
--


-- --------------------------------------------------------

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `tag_lib_start` text COLLATE utf8_czech_ci NOT NULL,
  `tag_lib_end` text COLLATE utf8_czech_ci NOT NULL,
  `head` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES
(2, 1, '<login:init group="web-admins" />', '', '<link rel="stylesheet" href="~/css/cms.css" type="text/css" />', '<web:content />'),
(3, 1, '', '', '', '<login:redirectWhenNotLogged pageId="4" />\r\n<login:redirectWhenLogged pageId="56" />'),
(4, 1, '', '', '<script type="text/javascript" src="~/js/domready.js"></script>\n<script type="text/javascript" src="~/js/formFieldEffect.js"></script>\n<script type="text/javascript" src="~/js/initLogin.js"></script>', '<div class="login-icons">\n  <img src="~/images/icons/service/rssmm_wfw.png" width="80" height="15" />\n  <img src="~/images/icons/service/ctags_php.png" width="80" height="15" />\n  <hr />\n  <img src="~/images/icons/service/valid_xhtml.png" width="80" height="15" />\n  <img src="~/images/icons/service/valid_css.png" width="80" height="15" />\n  <hr />\n  <img src="~/images/icons/service/firefox_copy2.gif" width="80" height="15" />\n  <img src="~/images/icons/service/opera.gif" width="80" height="15" />\n  <img src="~/images/icons/service/safari_copy2.gif" width="80" height="15" />\n  <hr />\n  <img src="~/images/icons/service/1024768.gif" width="80" height="15" />\n  <img src="~/images/icons/service/12801024.gif" width="80" height="15" />\n  <img src="~/images/icons/service/16001200.gif" width="80" height="15" />\n</div>\n<web:incTemplate browser="MSIE" templateId="34" />\n<div class="login">\n  <div class="login-head"></div>\n  <div class="login-in">\n    <login:form group="web-admins" pageId="56" autoLoginUserName="admin" autoLoginPassword="111111" />\n  </div>\n</div>'),
(5, 1, '<php:register tagPrefix="wp" classPath="php.libs.WebProject" />\n<login:init group="web-admins" />\n<wp:selectProject showMsg="false" useFrames="false" />', '<php:unregister tagPrefix="wp" />', '', '<div style="display: none">\n    <login:logout group="web-admins" pageId="4" />\n</div>\n<web:content />'),
(6, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showEditPage />\r\n\r\n<pg:showList editable="true" />'),
(7, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showEditFile />\r\n\r\n<pg:showFiles editable="true" />'),
(8, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<p><fl:showUploadForm /></p><p><fl:showNewDirectoryForm /></p><p><fl:showDirectory /></p>'),
(9, 1, '<php:register tagPrefix="user" classPath="php.libs.User" />', '<php:unregister tagPrefix="user" />', '', '<web:content />'),
(16, 1, '<php:register tagPrefix="artc" classPath="php.libs.Article" />', '<php:unregister tagPrefix="artc" />', '', '<web:content />'),
(40, 1, '', '', '', '<p>\r\n  <a href="&web:page=39">Edit article lines</a>\r\n</p>\r\n<p>\r\n  <artc:setLine method="session" />\r\n</p>\r\n<p>\r\n  <artc:showManagement method="session" detailPageId="41" />\r\n</p>\r\n<p>\r\n  <artc:createArticle detailPageId="41" method="session" />\r\n</p>'),
(17, 1, '<php:register tagPrefix="gb" classPath="php.libs.Guestbook" />', '<php:unregister tagPrefix="gb" />', '', '<gb:show guestbookId="1" editable="true" useFrame="true" />'),
(23, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />\n<php:register tagPrefix="user" classPath="php.libs.User" />', '<php:unregister tagPrefix="pg" />\n<php:unregister tagPrefix="user" />', '', '<web:content />'),
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
(56, 1, '<php:register tagPrefix="sys" classPath="php.libs.System" />', '<php:unregister tagPrefix="sys" />', '', '<div id="home-desktop" class="home-cover">\n    <strong>Kam dále?</strong>\n    <hr />\n    <web:menu parentId="5" inner="1" />\n    <hr />\n    <strong>TODO & Notes:</strong>\n    <sys:printNotes useFrames="false" showMsg="false" />\n</div>'),
(53, 1, '', '', '', '<p>\r\n  <a href="&web:page=52">Groups edit</a>\r\n</p>\r\n<p>\r\n  <user:management />\r\n</p>'),
(52, 1, '', '', '', '<p><a href="&web:page=53">Back to user manager ...</a></p>\r\n<p>\r\n  <user:newGroup />\r\n</p>\r\n<p>\r\n  <user:deleteGroup />\r\n</p>'),
(178, 1, '', '', '', ''),
(175, 1, '', '', '', '<pg:updateKeywords />'),
(176, 1, '', '', '', '<pg:showLanguages editable="true" />'),
(105, 1, '<php:register tagPrefix="sport" classPath="php.libs.Sport" />', '<php:unregister tagPrefix="sport" />', '', '<sport:selectSeason />\r\n<sport:selectTeam />\r\n<sport:selectTable />\r\n<web:content />'),
(106, 1, '', '', '', '<p>Select from menu above.</p>'),
(107, 1, '', '', '', '<p>\r\n  <sport:editSeasonForm />\r\n</p>\r\n<p>\r\n  <sport:editSeasons />\r\n</p>'),
(108, 1, '', '', '', '<p>\r\n  <sport:editTeamForm />\r\n</p>\r\n<p>\r\n  <sport:editTeams />\r\n</p>'),
(148, 1, '', '', '', '<web:menu parentId="147" inner="1" />'),
(109, 1, '', '', '', '<p>\r\n  <sport:editPlayerForm />\r\n</p>\r\n<p>\r\n  <sport:editPlayers />\r\n</p>'),
(147, 1, '<php:register tagPrefix="sys" classPath="php.libs.System" />', '<php:unregister tagPrefix="sys" />', '', '<web:content />'),
(110, 1, '', '', '', '<p>\r\n  <sport:editStatsForm />\r\n</p>\r\n<p>\r\n  <sport:editMatchForm />\r\n</p>\r\n<p>\r\n  <sport:editMatches />\r\n</p>'),
(111, 1, '', '', '', '<sport:table />'),
(125, 1, '<php:register tagPrefix="hint" classPath="php.libs.Hint" />', '<php:unregister tagPrefix="hint" />', '', '<web:content />'),
(126, 1, '', '', '', '<hint:selectLib />\r\n\r\n<hint:lib classPath="hint:classPath" />'),
(149, 1, '', '', '', '<sys:manageProperties />'),
(150, 1, '<login:init group="web-admins" />', '', '<link rel="stylesheet" href="~/css/editor.css" type="text/css" />\n<link rel="stylesheet" href="~/css/edit-area.css" type="text/css" />\n<link rel="stylesheet" href="~/css/window.css" type="text/css" />\n<link rel="stylesheet" href="~/css/jquery-autocomplete.css" type="text/css" />\n<link rel="stylesheet" href="~/css/jquery-wysiwyg.css" type="text/css" />\n<link rel="stylesheet" href="~/css/demo_table.css" type="text/css" />\n<script type="text/javascript" src="~/edit_area/edit_area_full.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-autocomplete-pack.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-blockui.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-dataTables-min.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-wysiwyg.js"></script>\n<script type="text/javascript" src="~/js/functions.js"></script>\n<script type="text/javascript" src="~/js/window.js"></script>\n<script type="text/javascript" src="~/js/domready.js"></script>\n<script type="text/javascript" src="~/js/rxmlhttp.js"></script>\n<script type="text/javascript" src="~/js/links.js"></script>\n<script type="text/javascript" src="~/js/processform.js"></script>\n<script type="text/javascript" src="~/js/domready.js"></script>\n<script type="text/javascript" src="~/js/Closer.js"></script>\n<script type="text/javascript" src="~/js/Confirm.js"></script>\n<script type="text/javascript" src="~/js/Editor.js"></script>\n<script type="text/javascript" src="~/js/FileName.js"></script>\n<script type="text/javascript" src="~/js/CountDown.js"></script>\n<script type="text/javascript" src="~/js/formFieldEffect.js"></script>\n<script type="text/javascript" src="~/js/init.js"></script>\n<script type="text/javascript" src="~/tiny-mce/tiny_mce.js"></script>\n<script type="text/javascript" src="~/scripts/js/initTiny.js"></script>', '<div class="cms">\n  <div id="cms-head" class="head">\n    <login:logout group="web-admins" pageId="4" />\n    <div id="logon-count-down" class="logon-count-down">\n      <div class="count-down-cover">\n        <span class="count-down-label">Login session <br/>expires in: </span>\n        <span id="count-down-counter" class="count-down-counter"><web:systemPropertyValue name="Login.session" /></span>\n      </div>\n    </div>\n    <login:info />\n    <php:register tagPrefix="wp" classPath="php.libs.WebProject" />\n    <wp:selectProject showMsg="false" useFrames="false" />\n    <php:unregister tagPrefix="wp" />\n    <div class="web-version">\n      <div class="label">CMS version</div>\n      <div class="value">\n        <web:cmsVersion />\n      </div>\n    </div>\n    <div class="web-version">\n      <div class="label">Web version</div>\n      <div class="value">\n        <web:version />\n      </div>\n    </div>\n    <div id="loading" class="web-version loading">\n      Loading ...\n    </div>\n    <div id="cms-menus">\n        <div class="cms-menu">\n        <span class="menu-root"><a href="&web:page=56">Web</a></span>\n          <web:menu parentId="5" inner="1" />\n      </div>\n        <div class="cms-menu cms-menu-2">\n          <span class="menu-root"><a href="&web:page=105">Floorball</a></span>\n        <web:menu parentId="105" inner="1" />\n        </div>\n        <div class="cms-menu cms-menu-3">\n          <span class="menu-root"><a href="&web:page=125">Hint</a></span>\n        </div>\n        <div class="cms-menu cms-menu-4">\n          <span class="menu-root"><a href="&web:page=148">System setup</a></span>\n          <web:menu parentId="147" inner="1" />\n        </div>\n        <div class="cms-menu cms-menu-5">\n          <span class="menu-root"><a href="&web:page=23">Web settings</a></span>\n          <web:menu parentId="23" inner="1" />\n        </div>\n    </div>\n  </div>\n  <div class="dock-bar">\n    <div class="dock-in">\n      <div id="dock-left" class="dock-left">\n      </div>\n      <div id="dock" class="dock-mid">\n      </div>\n      <div id="dock-right" class="dock-right">\n        <div id="web-ajax-log-cover" class="web-ajax-log-cover">\n\n        </div>\n        <div id="clock" class="clock">\n          <div id="hours" class="clock-hours">\n          --\n          </div>:<div id="minutes" class="clock-minutes">\n          --\n          </div>:<div id="seconds" class="clock-seconds">\n          --\n          </div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div id="cms-body" class="body">\n    <web:content />\n  </div>\n</div>'),
(172, 1, '', '', '', '<sys:manageNotes />'),
(173, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showEditPage />'),
(174, 1, '', '', '', '<pg:manageUrlCache />'),
(177, 1, '', '', '', '<user:truncateLog />\n<user:showLog />');

-- --------------------------------------------------------

--
-- Table structure for table `counter`
--

DROP TABLE IF EXISTS `counter`;
CREATE TABLE IF NOT EXISTS `counter` (
  `ip` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL,
  PRIMARY KEY (`counter_id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `counter`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory`
--

DROP TABLE IF EXISTS `directory`;
CREATE TABLE IF NOT EXISTS `directory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory`
--


-- --------------------------------------------------------

--
-- Table structure for table `directory_right`
--

DROP TABLE IF EXISTS `directory_right`;
CREATE TABLE IF NOT EXISTS `directory_right` (
  `did` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`did`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `directory_right`
--

INSERT INTO `directory_right` (`did`, `gid`, `type`) VALUES
(0, 1, 101),
(0, 1, 102),
(0, 1, 103);

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
CREATE TABLE IF NOT EXISTS `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dir_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `title` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `file`
--


-- --------------------------------------------------------

--
-- Table structure for table `file_right`
--

DROP TABLE IF EXISTS `file_right`;
CREATE TABLE IF NOT EXISTS `file_right` (
  `fid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`fid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;

--
-- Dumping data for table `file_right`
--

INSERT INTO `file_right` (`fid`, `gid`, `type`) VALUES
(0, 1, 102),
(0, 1, 103),
(0, 3, 101);

-- --------------------------------------------------------

--
-- Table structure for table `form_order1`
--

DROP TABLE IF EXISTS `form_order1`;
CREATE TABLE IF NOT EXISTS `form_order1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comp_name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_person` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_email` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_phone` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_address` tinytext COLLATE utf8_czech_ci NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `door_type` tinyint(4) NOT NULL,
  `cover` tinyint(4) NOT NULL,
  `fill_in` tinyint(4) NOT NULL,
  `comment` text COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `ip` varchar(16) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `form_order1`
--


-- --------------------------------------------------------

--
-- Table structure for table `form_order2`
--

DROP TABLE IF EXISTS `form_order2`;
CREATE TABLE IF NOT EXISTS `form_order2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comp_name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_person` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_email` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_phone` tinytext COLLATE utf8_czech_ci NOT NULL,
  `cont_address` tinytext COLLATE utf8_czech_ci NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `fixture` tinyint(4) NOT NULL,
  `draught` tinyint(11) NOT NULL,
  `transit` tinyint(11) NOT NULL,
  `heating` tinyint(11) NOT NULL,
  `gripping_1` tinyint(11) NOT NULL,
  `gripping_2` tinyint(11) NOT NULL,
  `comment` text COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `ip` varchar(16) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `form_order2`
--


-- --------------------------------------------------------

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_gid` int(11) NOT NULL DEFAULT '1',
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`gid`, `parent_gid`, `name`, `value`) VALUES
(1, 0, 'admins', 1),
(2, 1, 'web-admins', 50),
(3, 2, 'web', 254),
(4, 1, 'web-projects', 60);

-- --------------------------------------------------------

--
-- Table structure for table `guestbook`
--

DROP TABLE IF EXISTS `guestbook`;
CREATE TABLE IF NOT EXISTS `guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `guestbook_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `guestbook`
--


-- --------------------------------------------------------

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
CREATE TABLE IF NOT EXISTS `info` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `in_title` int(11) NOT NULL DEFAULT '1',
  `href` tinytext COLLATE utf8_czech_ci NOT NULL,
  `in_menu` int(11) NOT NULL,
  `page_pos` int(11) NOT NULL,
  `is_visible` int(11) NOT NULL,
  `keywords` tinytext COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES
(2, 1, 'CMS', 1, '', 0, 43, 1, '', 1248480300, -1),
(3, 1, 'Index', 0, '', 0, 3, 1, '', 1256931278, -1),
(4, 1, 'Login', 1, 'login', 0, 4, 1, '', 1259498355, -1),
(5, 1, 'in', 0, '', 1, 5, 1, '', 1256686914, -1),
(6, 1, 'Page Manager', 1, 'page-manager', 1, 8, 1, '', 1243352751, -1),
(7, 1, 'Text File Manager', 1, 'text-file-manager', 1, 16, 1, '', 1255186798, -1),
(8, 1, 'File Manager', 1, 'file-manager', 1, 23, 1, '', 1244011043, -1),
(9, 1, 'User Manager', 1, 'user-manager', 1, 50, 1, '', 1242245797, -1),
(16, 1, 'Article Manager', 1, 'article-manager', 1, 25, 1, '', 1241358620, -1),
(17, 1, 'Guestbook Manager', 1, 'guestbook-manager', 1, 44, 1, '', 1234282979, -1),
(23, 1, 'Web Settings', 1, 'web-settings', 0, 105, 1, '', 1261013526, -1),
(25, 1, 'Web Project Manager', 1, 'web-project-manager', 1, 56, 1, '', 1241994270, -1),
(26, 1, 'List', 1, '', 0, 26, 1, '', 1241950546, -1),
(27, 1, 'Edit', 1, 'edit', 0, 27, 1, '', 1241950560, -1),
(28, 1, 'Select', 1, 'select', 0, 28, 1, '', 1241310063, -1),
(39, 1, 'Lines', 1, 'lines', 0, 40, 1, '', 1241994208, -1),
(45, 1, 'List', 1, '', 0, 45, 1, '', 1241517092, -1),
(46, 1, 'Edit', 1, 'edit', 0, 46, 1, '', 1241519980, -1),
(44, 1, 'Template Manager', 1, 'template-manager', 1, 17, 1, '', 1241516140, -1),
(40, 1, 'List', 1, '', 0, 39, 1, '', 1241464605, -1),
(41, 1, 'Edit Article', 1, 'edit-article', 0, 41, 1, '', 1241387815, -1),
(42, 1, 'Edit Line', 1, 'edit-line', 0, 42, 1, '', 1241369340, -1),
(56, 1, 'Home', 1, '', 0, 7, 1, '', 1261172872, -1),
(177, 1, 'Show & Truncate log', 1, 'show-and-truncate-log', 1, 177, 1, '', 1261013385, -1),
(178, 1, 'Index', 1, '', 0, 178, 1, '', 1261013427, -1),
(52, 1, 'Groups', 1, 'groups', 0, 53, 1, '', 1242427728, -1),
(53, 1, 'Users', 1, '', 0, 52, 1, '', 1242245779, -1),
(175, 1, 'Keywords', 1, 'keywords', 1, 175, 1, '', 1261013318, -1),
(176, 1, 'Languages', 1, 'languages', 1, 176, 1, '', 1261013348, -1),
(174, 1, 'Url cache', 1, 'url-cache', 1, 174, 1, '', 1261013053, -1),
(173, 1, 'Page Manager - Edit only', 1, 'page-manager-edit-only', 0, 9, 1, '', 1260630356, -1),
(172, 1, 'System notes', 1, 'system-notes', 1, 172, 1, '', 1259345724, -1),
(105, 1, 'Sport', 1, 'sport', 0, 125, 1, '', 1254921169, -1),
(106, 1, 'Index', 1, '', 0, 106, 1, '', 1247405260, -1),
(107, 1, 'Seasons', 1, 'seasons', 1, 107, 1, '', 1247855669, -1),
(108, 1, 'Teams', 1, 'teams', 1, 108, 1, '', 1247855678, -1),
(109, 1, 'Players', 1, 'players', 1, 109, 1, '', 1247855688, -1),
(110, 1, 'Matches', 1, 'matches', 1, 110, 1, '', 1247855700, -1),
(111, 1, 'Table', 1, 'table', 1, 111, 1, '', 1247855709, -1),
(125, 1, 'Hint', 1, 'hint', 0, 147, 1, '', 1255428865, -1),
(126, 1, 'Hint for lib', 1, '', 0, 126, 1, '', 1255428884, -1),
(147, 1, 'System setup', 1, 'system-setup', 0, 173, 1, '', 1255281598, -1),
(148, 1, 'Index', 1, '', 0, 148, 1, '', 1255280622, -1),
(149, 1, 'System properties', 1, 'system-properties', 1, 149, 1, '', 1255281625, -1),
(150, 1, 'Content', 0, 'in', 0, 150, 1, '', 1261170703, -1);

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL,
  `language` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `language`) VALUES
(1, ''),
(2, 'cs'),
(3, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`id`, `parent_id`, `wp`) VALUES
(2, 0, 1),
(3, 2, 1),
(4, 2, 1),
(5, 150, 1),
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
(178, 23, 1),
(56, 5, 1),
(40, 16, 1),
(41, 16, 1),
(42, 16, 1),
(150, 2, 1),
(149, 147, 1),
(148, 147, 1),
(147, 5, 1),
(125, 5, 1),
(126, 125, 1),
(111, 105, 1),
(110, 105, 1),
(109, 105, 1),
(108, 105, 1),
(107, 105, 1),
(106, 105, 1),
(105, 5, 1),
(172, 147, 1),
(173, 5, 1),
(177, 23, 1),
(176, 23, 1),
(175, 23, 1),
(174, 23, 1);

-- --------------------------------------------------------

--
-- Table structure for table `page_file`
--

DROP TABLE IF EXISTS `page_file`;
CREATE TABLE IF NOT EXISTS `page_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `page_file`
--


-- --------------------------------------------------------

--
-- Table structure for table `page_file_inc`
--

DROP TABLE IF EXISTS `page_file_inc`;
CREATE TABLE IF NOT EXISTS `page_file_inc` (
  `file_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`file_id`,`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `page_file_inc`
--


-- --------------------------------------------------------

--
-- Table structure for table `page_right`
--

DROP TABLE IF EXISTS `page_right`;
CREATE TABLE IF NOT EXISTS `page_right` (
  `pid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`pid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `page_right`
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
(5, 3, 101),
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
(56, 1, 102),
(56, 1, 103),
(56, 2, 101),
(105, 1, 102),
(105, 1, 103),
(105, 2, 101),
(106, 1, 102),
(106, 1, 103),
(106, 2, 101),
(107, 1, 102),
(107, 1, 103),
(107, 2, 101),
(108, 1, 102),
(108, 1, 103),
(108, 2, 101),
(109, 1, 102),
(109, 1, 103),
(109, 2, 101),
(110, 1, 102),
(110, 1, 103),
(110, 2, 101),
(111, 1, 102),
(111, 1, 103),
(111, 2, 101),
(125, 1, 102),
(125, 1, 103),
(125, 2, 101),
(126, 1, 102),
(126, 1, 103),
(126, 2, 101),
(147, 1, 101),
(147, 1, 102),
(147, 1, 103),
(148, 1, 101),
(148, 1, 102),
(148, 1, 103),
(149, 1, 101),
(149, 1, 102),
(149, 1, 103),
(150, 1, 102),
(150, 1, 103),
(150, 3, 101),
(159, 2, 102),
(159, 2, 103),
(159, 3, 101),
(160, 2, 102),
(160, 2, 103),
(160, 3, 101),
(161, 2, 102),
(161, 2, 103),
(161, 3, 101),
(162, 2, 102),
(162, 2, 103),
(162, 3, 101),
(163, 2, 102),
(163, 2, 103),
(163, 3, 101),
(172, 1, 102),
(172, 1, 103),
(172, 2, 101),
(173, 1, 102),
(173, 1, 103),
(173, 2, 101),
(174, 1, 101),
(174, 1, 102),
(174, 1, 103),
(175, 1, 101),
(175, 1, 102),
(175, 1, 103),
(176, 1, 101),
(176, 1, 102),
(176, 1, 103),
(177, 1, 101),
(177, 1, 102),
(177, 1, 103),
(178, 1, 101),
(178, 1, 102),
(178, 1, 103);

-- --------------------------------------------------------

--
-- Table structure for table `pair_uid_property`
--

DROP TABLE IF EXISTS `pair_uid_property`;
CREATE TABLE IF NOT EXISTS `pair_uid_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `property_name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `property_value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pair_uid_property`
--


-- --------------------------------------------------------

--
-- Table structure for table `personal_note`
--

DROP TABLE IF EXISTS `personal_note`;
CREATE TABLE IF NOT EXISTS `personal_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` mediumtext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `personal_note`
--

INSERT INTO `personal_note` (`id`, `value`, `type`, `user_id`) VALUES
(2, 'Skript na inicializaci EditArei v detailu stránky', 1, 1),
(3, 'Po ulozeni nove stranky, se znovu ovetre okno, kde jiz je editace teto stranky, puvodni se vsak nezavre!', 1, 1),
(4, 'Vylepsit desktop REFRESH, mohl by se aktualizovat sam pri pridani System note.', 1, 1),
(5, 'Admin heslo pro localhost je 111111, zrusit autoLogin pro ostrou verzi.', 1, 1),
(7, 'Klavesove zkratky: Na Shift + O, otevrit radek za zadani adresy noveho okna v systemu, naseptavac ...', 1, 1),
(8, 'Klavesove zkratky: SHIFT + F12 -> Web Ajax Log, SHIFT + x -> Zavre okno, SHIFT + z -> Minimalizuje okno ( pokud jiz minimalizovane je, pak ho obnovi ), SHIFT + d -> zobrazi plochu.', 1, 1),
(9, 'Klavesove zkratky: Upravit ... pro rychle psani, ci vkladani textu -> pomale!!!', 1, 1),
(10, 'Klavesove zkratky: SHIFT + Tab -> prohazovani oken ... zobrazovat panel jako ve Win ;)', 1, 1),
(11, 'Klavesove zkratky: Esc na form element -> ztrata focusu.', 1, 1),
(12, 'Klavesove zkratky: Pokud zadny prvek mit fokus nebude, na Esc se priradi poslednimu, nebo prvnimu z aktiniho okna', 1, 1),
(13, '!!!! - Strankovani v tabulce -> Nefunguje Ajax', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_property`
--

DROP TABLE IF EXISTS `personal_property`;
CREATE TABLE IF NOT EXISTS `personal_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=22 ;

--
-- Dumping data for table `personal_property`
--

INSERT INTO `personal_property` (`id`, `user_id`, `name`, `value`, `type`) VALUES
(9, 1, 'Frame.addlanguage', 'true', 1),
(8, 1, 'Frame.languages', 'true', 1),
(6, 1, 'Frame.systemproperties', 'false', 1),
(10, 1, 'Frame.managekeywords', 'true', 1),
(11, 1, 'Frame.userlog', 'true', 1),
(12, 1, 'Frame.newfile', 'true', 1),
(13, 1, 'Frame.newdirectory', 'true', 1),
(21, 1, 'Page.editors', 'edit_area', 1),
(15, 1, 'Page.editAreaTLStartRows', '20', 1),
(16, 1, 'Page.editAreaTLEndRows', '24', 1),
(17, 1, 'Page.editAreaHeadRows', '24', 1),
(18, 1, 'Page.editAreaContentRows', '24', 1),
(19, 1, 'Login.session', '20', 1),
(20, 1, 'Article.editors', 'edit_area', 1);

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

DROP TABLE IF EXISTS `template`;
CREATE TABLE IF NOT EXISTS `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `template`
--


-- --------------------------------------------------------

--
-- Table structure for table `template_right`
--

DROP TABLE IF EXISTS `template_right`;
CREATE TABLE IF NOT EXISTS `template_right` (
  `tid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`tid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `template_right`
--


-- --------------------------------------------------------

--
-- Table structure for table `urlcache`
--

DROP TABLE IF EXISTS `urlcache`;
CREATE TABLE IF NOT EXISTS `urlcache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_url_def` tinytext COLLATE utf8_czech_ci NOT NULL,
  `project_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url_def` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `page-ids` tinytext COLLATE utf8_czech_ci NOT NULL,
  `language_id` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL DEFAULT '-1',
  `lastcache` int(11) NOT NULL DEFAULT '0',
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `urlcache`
--

INSERT INTO `urlcache` (`id`, `project_url_def`, `project_url`, `url_def`, `url`, `page-ids`, `language_id`, `cachetime`, `lastcache`, `wp`) VALUES
(1, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-project-manager', 'in/web-project-manager', '2-150-5-25-26', 1, -1, 0, 1),
(2, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in', 'in', '2-150-5-56', 1, -1, 0, 1),
(3, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/page-manager', 'in/page-manager', '2-150-5-6', 1, -1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `surname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `login` tinytext COLLATE utf8_czech_ci NOT NULL,
  `password` tinytext COLLATE utf8_czech_ci NOT NULL,
  `enable` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `group_id`, `name`, `surname`, `login`, `password`, `enable`) VALUES
(1, 1, 'admin', 'admin', 'admin', '434ddd1afcf8ef4834d3900e20fb1bde966839de', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_in_group`
--

DROP TABLE IF EXISTS `user_in_group`;
CREATE TABLE IF NOT EXISTS `user_in_group` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `user_in_group`
--

INSERT INTO `user_in_group` (`uid`, `gid`) VALUES
(1, 1),
(1, 2),
(1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `login_timestamp` int(11) NOT NULL,
  `logout_timestamp` int(11) NOT NULL,
  `used_group` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`id`, `session_id`, `user_id`, `timestamp`, `login_timestamp`, `logout_timestamp`, `used_group`) VALUES
(1, 1533234, 1, 1262181356, 1262181356, 1262523928, 'web-admins'),
(2, 567230, 1, 1262524854, 1262523928, 0, 'web-admins');

-- --------------------------------------------------------

--
-- Table structure for table `web_alias`
--

DROP TABLE IF EXISTS `web_alias`;
CREATE TABLE IF NOT EXISTS `web_alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `http` int(11) NOT NULL,
  `https` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=48 ;

--
-- Dumping data for table `web_alias`
--

INSERT INTO `web_alias` (`id`, `project_id`, `url`, `http`, `https`) VALUES
(22, 1, 'admin.webprojects.localhost', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `web_project`
--

DROP TABLE IF EXISTS `web_project`;
CREATE TABLE IF NOT EXISTS `web_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `http` int(11) NOT NULL DEFAULT '1',
  `https` int(11) NOT NULL DEFAULT '1',
  `error_all_pid` int(11) NOT NULL,
  `error_404_pid` int(11) NOT NULL,
  `error_403_pid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=24 ;

--
-- Dumping data for table `web_project`
--

INSERT INTO `web_project` (`id`, `name`, `url`, `http`, `https`, `error_all_pid`, `error_404_pid`, `error_403_pid`) VALUES
(1, 'CMS', 'cms.webprojects.localhost', 1, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `web_project_right`
--

DROP TABLE IF EXISTS `web_project_right`;
CREATE TABLE IF NOT EXISTS `web_project_right` (
  `wp` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`wp`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `web_project_right`
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
-- Table structure for table `window_properties`
--

DROP TABLE IF EXISTS `window_properties`;
CREATE TABLE IF NOT EXISTS `window_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `frame_id` tinytext COLLATE utf8_czech_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `left` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `maximized` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=49 ;

--
-- Dumping data for table `window_properties`
--

INSERT INTO `window_properties` (`id`, `frame_id`, `user_id`, `left`, `top`, `width`, `height`, `maximized`) VALUES
(1, 'Frame.systemproperties', 1, 0, 0, 1902, 893, 1),
(4, 'Frame.editace', 1, 0, 0, 500, 300, 1),
(6, 'Frame.pages', 1, 0, 0, 945, 606, 1),
(7, 'Frame.newpage', 1, 0, 0, 408, 30, 0),
(8, 'Frame.editation', 1, 638, 31, 998, 705, 1),
(9, 'Frame.textfiles', 1, 390, 51, 909, 364, 0),
(10, 'Frame.editfile', 1, 0, 0, 500, 300, 1),
(11, 'Frame.newtextfile', 1, 0, 0, 408, 26, 0),
(12, 'Frame.n�pov�dapro', 1, 0, 0, 797, 511, 0),
(13, 'Frame.n�pov�da(v�b�r)pro', 1, 0, 0, 400, 61, 0),
(14, 'Frame.selecthelp', 1, 284, 78, 400, 82, 0),
(15, 'Frame.webprojects', 1, 255, 64, 705, 438, 0),
(16, 'Frame.editwebproject', 1, 292, 21, 850, 537, 0),
(17, 'Frame.userlist', 1, 353, 50, 847, 467, 0),
(18, 'Frame.newuser', 1, 0, 0, 145, 33, 0),
(19, 'Frame.edituser', 1, 315, 368, 691, 343, 0),
(20, 'Frame.sez�ny', 1, 0, 0, 408, 142, 0),
(21, 'Frame.helpfor', 1, 764, 15, 793, 483, 0),
(22, 'Frame.newfile', 1, 39, 15, 936, 169, 0),
(23, 'Frame.temlateslist', 1, 648, 42, 831, 354, 0),
(24, 'Frame.temlateedit', 1, 0, 0, 500, 300, 1),
(25, 'Frame.filelist', 1, 18, 246, 1142, 493, 0),
(26, 'Frame.newdirectory', 1, 1027, 9, 587, 287, 0),
(27, 'Frame.manageurlcache', 1, 30, 30, 999, 533, 1),
(28, 'Frame.addpage', 1, 0, 0, 1214, 680, 1),
(29, 'Frame.addsubpage', 1, 0, 0, 500, 300, 1),
(30, 'Frame.moveto', 1, 0, 0, 524, 67, 0),
(31, 'Frame.copyto', 1, 574, 88, 546, 79, 0),
(32, 'Frame.addlanguageversion', 1, 0, 0, 500, 300, 1),
(33, 'Frame.articlesinline', 1, 696, 40, 923, 358, 0),
(34, 'Frame.selectline', 1, 83, 490, 536, 61, 0),
(35, 'Frame.newarticle', 1, 0, 0, 606, 499, 1),
(36, 'Frame.createnewarticle', 1, 90, 91, 408, 25, 0),
(37, 'Frame.editarticle', 1, 0, 0, 500, 300, 1),
(38, 'Frame.guestbookmanagement-1', 1, 0, 0, 500, 300, 1),
(39, 'Frame.systemnotes', 1, 542, 17, 1098, 598, 0),
(40, 'Frame.addlanguage', 1, 458, 155, 408, 65, 0),
(41, 'Frame.languages', 1, 32, 156, 408, 209, 0),
(42, 'Frame.managekeywords', 1, 30, 26, 860, 70, 0),
(43, 'Frame.userlog', 1, 60, 75, 715, 380, 0),
(44, 'Frame.tabulky', 1, 0, 0, 408, 72, 0),
(45, 'Frame.t�my', 1, 0, 0, 408, 81, 0),
(46, 'Frame.webbrowser', 1, 90, 90, 500, 300, 0),
(47, 'Frame.edit', 1, 0, 0, 500, 300, 1),
(48, 'Frame.truncateuserlog', 1, 929, 9, 408, 63, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wp_wysiwyg_file`
--

DROP TABLE IF EXISTS `wp_wysiwyg_file`;
CREATE TABLE IF NOT EXISTS `wp_wysiwyg_file` (
  `wp` int(11) NOT NULL,
  `tf_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `wp_wysiwyg_file`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_projection`
--

DROP TABLE IF EXISTS `w_projection`;
CREATE TABLE IF NOT EXISTS `w_projection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `subname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_projection`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_reference`
--

DROP TABLE IF EXISTS `w_reference`;
CREATE TABLE IF NOT EXISTS `w_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `subname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_reference`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_match`
--

DROP TABLE IF EXISTS `w_sport_match`;
CREATE TABLE IF NOT EXISTS `w_sport_match` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `h_team` int(10) unsigned NOT NULL,
  `a_team` int(10) unsigned NOT NULL,
  `h_score` int(10) NOT NULL DEFAULT '0',
  `a_score` int(11) NOT NULL DEFAULT '0',
  `h_shoots` int(11) NOT NULL DEFAULT '0',
  `a_shoots` int(11) NOT NULL DEFAULT '0',
  `h_penalty` int(11) NOT NULL DEFAULT '0',
  `a_penalty` int(11) NOT NULL DEFAULT '0',
  `h_extratime` int(11) NOT NULL DEFAULT '0',
  `a_extratime` int(11) NOT NULL DEFAULT '0',
  `round` int(11) NOT NULL,
  `in_table` int(11) NOT NULL DEFAULT '1',
  `comment` mediumtext COLLATE utf8_czech_ci NOT NULL,
  `season` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`season`,`a_team`,`h_team`),
  KEY `h_team` (`h_team`),
  KEY `a_team` (`a_team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_match`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_player`
--

DROP TABLE IF EXISTS `w_sport_player`;
CREATE TABLE IF NOT EXISTS `w_sport_player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `surname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `birthyear` int(3) unsigned NOT NULL,
  `number` int(3) unsigned NOT NULL,
  `position` int(3) unsigned NOT NULL,
  `photo` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season` int(10) unsigned NOT NULL,
  `team` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`season`,`team`),
  KEY `season` (`season`),
  KEY `team` (`team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_player`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_season`
--

DROP TABLE IF EXISTS `w_sport_season`;
CREATE TABLE IF NOT EXISTS `w_sport_season` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_year` int(10) unsigned NOT NULL,
  `end_year` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_season`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_stats`
--

DROP TABLE IF EXISTS `w_sport_stats`;
CREATE TABLE IF NOT EXISTS `w_sport_stats` (
  `pid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL,
  `goals` tinyint(3) unsigned NOT NULL,
  `assists` tinyint(3) unsigned NOT NULL,
  `penalty` tinyint(3) unsigned NOT NULL,
  `shoots` tinyint(3) unsigned NOT NULL,
  `season` tinyint(3) unsigned NOT NULL,
  `table_id` int(11) NOT NULL,
  PRIMARY KEY (`pid`,`mid`,`season`,`table_id`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `w_sport_stats`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_table`
--

DROP TABLE IF EXISTS `w_sport_table`;
CREATE TABLE IF NOT EXISTS `w_sport_table` (
  `team` int(10) unsigned NOT NULL,
  `matches` int(10) unsigned NOT NULL,
  `wins` tinyint(3) unsigned DEFAULT NULL,
  `draws` tinyint(3) unsigned DEFAULT NULL,
  `loses` tinyint(3) unsigned DEFAULT NULL,
  `s_score` tinyint(3) unsigned DEFAULT NULL,
  `r_score` tinyint(3) unsigned DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `season` tinyint(3) unsigned NOT NULL,
  `table_id` int(11) NOT NULL,
  PRIMARY KEY (`team`,`season`,`table_id`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `w_sport_table`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_tables`
--

DROP TABLE IF EXISTS `w_sport_tables`;
CREATE TABLE IF NOT EXISTS `w_sport_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_tables`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_team`
--

DROP TABLE IF EXISTS `w_sport_team`;
CREATE TABLE IF NOT EXISTS `w_sport_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `logo` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`season`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_team`
--

