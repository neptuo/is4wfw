-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2010 at 02:21 PM
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
(56, 1, '<php:register tagPrefix="sys" classPath="php.libs.System" />', '<php:unregister tagPrefix="sys" />', '', '<div id="home-desktop" class="home-cover">\n    <strong>Kam dÃ¡le?</strong>\n    <hr />\n    <web:menu parentId="5" inner="1" />\n    <hr />\n    <strong>TODO & Notes:</strong>\n    <sys:printNotes useFrames="false" showMsg="false" />\n</div>'),
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=61 ;

--
-- Dumping data for table `directory`
--

INSERT INTO `directory` (`id`, `parent_id`, `name`, `url`, `timestamp`, `wp`) VALUES
(15, 0, 'Rhona', '', 1242069372, 1),
(16, 0, 'Rhona1', '', 1242070474, 1),
(14, 0, 'ProWeb1', '', 1242032524, 1),
(59, 0, 'File edit testing', 'file-edit-testing', 1258286235, 1),
(20, 0, 'Tester', '', 1243954522, 1),
(24, 0, 'Megan', '', 1244007361, 1),
(25, 0, 'Adriana', '', 1244007731, 1),
(26, 0, 'Papaya', '', 1246721023, 1),
(27, 26, 'Menu', '', 1246721370, 1),
(28, 26, 'Banners', '', 1246732755, 1),
(29, 26, 'Players', '', 1248072571, 1),
(30, 0, 'Galerie Upload', '', 1249572212, 1),
(50, 0, 'Pokus', '', 1256212412, 1),
(51, 0, 'GalleryTest', '', 1257780720, 1),
(52, 51, 'Alessandra Ambrosio', '', 1257784463, 1),
(53, 51, 'Catherine Bell', '', 1257784414, 1),
(54, 51, 'Alicia Machado', '', 1257784440, 1),
(55, 51, 'Jennifer Lamiraqui', '', 1257784394, 1),
(57, 0, 'TestUrl', 'test-url-2', 1258201716, 1);

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
(0, 1, 103),
(14, 1, 103),
(14, 4, 101),
(14, 4, 102),
(15, 1, 101),
(15, 1, 102),
(15, 1, 103),
(16, 1, 102),
(16, 1, 103),
(16, 3, 101),
(17, 4, 101),
(17, 4, 102),
(17, 4, 103),
(18, 4, 101),
(18, 4, 102),
(18, 4, 103),
(19, 4, 101),
(19, 4, 102),
(19, 4, 103),
(20, 1, 103),
(20, 10, 101),
(20, 10, 102),
(21, 10, 101),
(21, 10, 102),
(21, 10, 103),
(22, 1, 103),
(22, 10, 101),
(22, 10, 102),
(23, 1, 103),
(23, 10, 101),
(23, 10, 102),
(24, 1, 103),
(24, 2, 101),
(24, 2, 102),
(25, 1, 103),
(25, 2, 101),
(25, 2, 102),
(26, 1, 101),
(26, 1, 102),
(26, 1, 103),
(27, 1, 101),
(27, 1, 102),
(27, 1, 103),
(28, 1, 101),
(28, 1, 102),
(28, 1, 103),
(29, 1, 101),
(29, 1, 102),
(29, 1, 103),
(30, 1, 103),
(30, 3, 101),
(30, 3, 102),
(31, 1, 101),
(31, 1, 102),
(31, 1, 103),
(32, 1, 101),
(32, 1, 102),
(32, 1, 103),
(33, 1, 101),
(33, 1, 102),
(33, 1, 103),
(34, 1, 101),
(34, 1, 102),
(34, 1, 103),
(35, 1, 101),
(35, 1, 102),
(35, 1, 103),
(36, 1, 101),
(36, 1, 102),
(36, 1, 103),
(37, 1, 101),
(37, 1, 102),
(37, 1, 103),
(38, 1, 101),
(38, 1, 102),
(38, 1, 103),
(39, 1, 101),
(39, 1, 102),
(39, 1, 103),
(40, 1, 101),
(40, 1, 102),
(40, 1, 103),
(41, 1, 101),
(41, 1, 102),
(41, 1, 103),
(42, 1, 101),
(42, 1, 102),
(42, 1, 103),
(43, 1, 101),
(43, 1, 102),
(43, 1, 103),
(44, 1, 101),
(44, 1, 102),
(44, 1, 103),
(45, 1, 101),
(45, 1, 102),
(45, 1, 103),
(46, 1, 103),
(46, 10, 101),
(46, 10, 102),
(47, 1, 103),
(47, 10, 101),
(47, 10, 102),
(48, 1, 103),
(48, 10, 101),
(48, 10, 102),
(49, 1, 103),
(49, 10, 101),
(49, 10, 102),
(50, 1, 101),
(50, 1, 102),
(50, 1, 103),
(51, 1, 101),
(51, 1, 102),
(51, 1, 103),
(52, 1, 101),
(52, 1, 102),
(52, 1, 103),
(53, 1, 101),
(53, 1, 102),
(53, 1, 103),
(54, 1, 101),
(54, 1, 102),
(54, 1, 103),
(55, 1, 101),
(55, 1, 102),
(55, 1, 103),
(56, 1, 101),
(56, 1, 102),
(56, 1, 103),
(57, 1, 101),
(57, 1, 102),
(57, 1, 103),
(58, 1, 101),
(58, 1, 102),
(58, 1, 103),
(59, 1, 101),
(59, 1, 102),
(59, 1, 103),
(60, 1, 101),
(60, 1, 102),
(60, 1, 103);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=170 ;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`id`, `dir_id`, `name`, `title`, `type`, `timestamp`, `wp`) VALUES
(159, 20, 'rbullnak300qb', '', 3, 1259269414, 1),
(152, 59, 'Winter Leaves', '', 3, 1258303228, 1),
(157, 30, 'file82605733', '', 3, 1259263589, 1),
(158, 30, 'file92668661', '', 3, 1259263772, 1),
(155, 59, 'Desert Landscape', '', 3, 1258497270, 1),
(10, 16, 'rhonamitra1666qz', 'Rhoooonaaa ;)', 3, 1242070497, 1),
(9, 15, 'rhonamitra1082ht', 'Rhona :)', 3, 1242070033, 1),
(156, 59, 'Forest', '', 3, 1259162347, 1),
(150, 59, 'Dock', '', 3, 1258301762, 1),
(19, 16, 'rhonamitra11rq', '', 3, 1243959327, 1),
(20, 16, 'rhonamitra13thannualeltonjohno', '', 3, 1243959340, 1),
(21, 24, '2copyvd1', '', 3, 1244007375, 1),
(22, 24, '02fv4', '', 3, 1244007384, 1),
(23, 24, '15f99f2165aa8c8l', '', 3, 1244007396, 1),
(24, 24, '91', '', 3, 1244007407, 1),
(25, 25, 'adriana_lima33', '', 3, 1244007750, 1),
(26, 25, 'adrianalima2bo9', '', 3, 1244007760, 1),
(27, 25, 'adrianalima918hw7', '', 3, 1244007769, 1),
(28, 25, 'd8d943984b', '', 3, 1244007777, 1),
(30, 26, 'Logo small', '', 5, 1246721306, 1),
(31, 26, 'Head', '', 5, 1246721323, 1),
(32, 26, 'Logo animation', '', 4, 1246721340, 1),
(33, 27, 'dido-league', '', 4, 1246721382, 1),
(34, 27, 'guestbook', '', 4, 1246721385, 1),
(35, 27, 'home', '', 4, 1246721392, 1),
(36, 27, 'news', '', 4, 1246721400, 1),
(37, 27, 'players', '', 4, 1246721413, 1),
(38, 27, 'sponsors', '', 4, 1246721420, 1),
(39, 28, 'kostal', '', 5, 1246732772, 1),
(40, 28, 'lfp', '', 5, 1246732777, 1),
(41, 26, 'h-background', '', 5, 1246733548, 1),
(42, 26, 'loading', '', 4, 1246743665, 1),
(43, 24, 'Fox_Megan001', '', 3, 1247607562, 1),
(44, 29, 'dvorka', '', 5, 1248072587, 1),
(45, 30, 'file42392709', '', 3, 1249572215, 1),
(140, 54, 'machado71920x1440', '', 3, 1257784174, 1),
(125, 20, 'hotlima', '', 3, 1256125995, 1),
(139, 52, 'ambrosio1011280x960', '', 3, 1257784163, 1),
(131, 50, 'gravel11600x1200', '', 3, 1256212439, 1),
(132, 50, 'jewelstaite1', '', 3, 1256212448, 1),
(133, 50, 'karima391920x1440', '', 3, 1256212469, 1),
(136, 52, 'ambrosio91280x960', '', 3, 1257784151, 1),
(137, 52, 'ambrosio361600x1200', '', 3, 1257784155, 1),
(138, 52, 'ambrosio651600x1200', '', 3, 1257784158, 1),
(123, 20, 'd8d943984b', '', 3, 1256125854, 1),
(121, 20, '54887adrianalima95pz', '', 3, 1256125627, 1),
(122, 20, 'Adriana_Lima33', '', 3, 1256125733, 1),
(141, 54, 'machado141920x1440', '', 3, 1257784193, 1),
(142, 53, 'bellcatherinefhm20020743qe', '', 3, 1257784209, 1),
(143, 53, 'rbullnak300qb', '', 3, 1257784224, 1),
(144, 55, 'lamiraqui11920x1440', '', 3, 1257784290, 1),
(145, 55, 'lamiraqui271920x1440', '', 3, 1257784305, 1),
(146, 55, 'lamiraqui321920x1440', '', 3, 1257784310, 1),
(147, 55, 'lamiraqui121920x1440', '', 3, 1257784314, 1),
(148, 55, 'lamiraqui631920x1440', '', 3, 1257784318, 1),
(149, 55, 'lamiraqui1111920x1440', '', 3, 1257784325, 1);

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
(0, 3, 101),
(9, 1, 101),
(9, 1, 102),
(9, 1, 103),
(10, 1, 102),
(10, 1, 103),
(10, 3, 101),
(18, 1, 103),
(18, 10, 101),
(18, 10, 102),
(19, 1, 102),
(19, 1, 103),
(19, 3, 101),
(20, 1, 102),
(20, 1, 103),
(20, 3, 101),
(21, 1, 103),
(21, 2, 101),
(21, 2, 102),
(22, 1, 103),
(22, 2, 101),
(22, 2, 102),
(23, 1, 103),
(23, 2, 101),
(23, 2, 102),
(24, 1, 103),
(24, 2, 101),
(24, 2, 102),
(25, 1, 103),
(25, 2, 101),
(25, 2, 102),
(26, 1, 103),
(26, 2, 101),
(26, 2, 102),
(27, 1, 103),
(27, 2, 101),
(27, 2, 102),
(28, 1, 103),
(28, 2, 101),
(28, 2, 102),
(29, 1, 101),
(29, 1, 102),
(29, 1, 103),
(30, 1, 101),
(30, 1, 102),
(30, 1, 103),
(31, 1, 101),
(31, 1, 102),
(31, 1, 103),
(32, 1, 101),
(32, 1, 102),
(32, 1, 103),
(33, 1, 101),
(33, 1, 102),
(33, 1, 103),
(34, 1, 101),
(34, 1, 102),
(34, 1, 103),
(35, 1, 101),
(35, 1, 102),
(35, 1, 103),
(36, 1, 101),
(36, 1, 102),
(36, 1, 103),
(37, 1, 101),
(37, 1, 102),
(37, 1, 103),
(38, 1, 101),
(38, 1, 102),
(38, 1, 103),
(39, 1, 101),
(39, 1, 102),
(39, 1, 103),
(40, 1, 101),
(40, 1, 102),
(40, 1, 103),
(41, 1, 101),
(41, 1, 102),
(41, 1, 103),
(42, 1, 101),
(42, 1, 102),
(42, 1, 103),
(43, 1, 103),
(43, 2, 101),
(43, 2, 102),
(44, 1, 101),
(44, 1, 102),
(44, 1, 103),
(45, 1, 103),
(45, 3, 101),
(45, 3, 102),
(46, 1, 101),
(46, 1, 102),
(46, 1, 103),
(47, 1, 101),
(47, 1, 102),
(47, 1, 103),
(48, 1, 101),
(48, 1, 102),
(48, 1, 103),
(49, 1, 101),
(49, 1, 102),
(49, 1, 103),
(50, 1, 101),
(50, 1, 102),
(50, 1, 103),
(51, 1, 101),
(51, 1, 102),
(51, 1, 103),
(52, 1, 101),
(52, 1, 102),
(52, 1, 103),
(53, 1, 101),
(53, 1, 102),
(53, 1, 103),
(54, 1, 101),
(54, 1, 102),
(54, 1, 103),
(55, 1, 101),
(55, 1, 102),
(55, 1, 103),
(56, 1, 101),
(56, 1, 102),
(56, 1, 103),
(57, 1, 101),
(57, 1, 102),
(57, 1, 103),
(58, 1, 101),
(58, 1, 102),
(58, 1, 103),
(59, 1, 101),
(59, 1, 102),
(59, 1, 103),
(60, 1, 101),
(60, 1, 102),
(60, 1, 103),
(61, 1, 101),
(61, 1, 102),
(61, 1, 103),
(62, 1, 101),
(62, 1, 102),
(62, 1, 103),
(63, 1, 101),
(63, 1, 102),
(63, 1, 103),
(64, 1, 101),
(64, 1, 102),
(64, 1, 103),
(65, 1, 101),
(65, 1, 102),
(65, 1, 103),
(66, 1, 101),
(66, 1, 102),
(66, 1, 103),
(67, 1, 101),
(67, 1, 102),
(67, 1, 103),
(68, 1, 101),
(68, 1, 102),
(68, 1, 103),
(69, 1, 101),
(69, 1, 102),
(69, 1, 103),
(70, 1, 101),
(70, 1, 102),
(70, 1, 103),
(71, 1, 101),
(71, 1, 102),
(71, 1, 103),
(72, 1, 101),
(72, 1, 102),
(72, 1, 103),
(73, 1, 101),
(73, 1, 102),
(73, 1, 103),
(74, 1, 101),
(74, 1, 102),
(74, 1, 103),
(75, 1, 101),
(75, 1, 102),
(75, 1, 103),
(76, 1, 101),
(76, 1, 102),
(76, 1, 103),
(77, 1, 101),
(77, 1, 102),
(77, 1, 103),
(78, 1, 101),
(78, 1, 102),
(78, 1, 103),
(79, 1, 101),
(79, 1, 102),
(79, 1, 103),
(80, 1, 101),
(80, 1, 102),
(80, 1, 103),
(81, 1, 101),
(81, 1, 102),
(81, 1, 103),
(82, 1, 103),
(82, 10, 101),
(82, 10, 102),
(83, 1, 103),
(83, 10, 101),
(83, 10, 102),
(84, 1, 103),
(84, 10, 101),
(84, 10, 102),
(85, 1, 103),
(85, 10, 101),
(85, 10, 102),
(86, 1, 103),
(86, 10, 101),
(86, 10, 102),
(87, 1, 103),
(87, 10, 101),
(87, 10, 102),
(88, 1, 103),
(88, 10, 101),
(88, 10, 102),
(89, 1, 103),
(89, 10, 101),
(89, 10, 102),
(90, 1, 103),
(90, 10, 101),
(90, 10, 102),
(91, 1, 103),
(91, 10, 101),
(91, 10, 102),
(92, 1, 103),
(92, 10, 101),
(92, 10, 102),
(93, 1, 103),
(93, 10, 101),
(93, 10, 102),
(94, 1, 103),
(94, 10, 101),
(94, 10, 102),
(95, 1, 103),
(95, 10, 101),
(95, 10, 102),
(96, 1, 103),
(96, 10, 101),
(96, 10, 102),
(97, 1, 103),
(97, 10, 101),
(97, 10, 102),
(98, 1, 103),
(98, 10, 101),
(98, 10, 102),
(99, 1, 103),
(99, 10, 101),
(99, 10, 102),
(100, 1, 103),
(100, 10, 101),
(100, 10, 102),
(101, 1, 103),
(101, 10, 101),
(101, 10, 102),
(102, 1, 103),
(102, 10, 101),
(102, 10, 102),
(103, 1, 103),
(103, 10, 101),
(103, 10, 102),
(104, 1, 103),
(104, 10, 101),
(104, 10, 102),
(105, 1, 103),
(105, 10, 101),
(105, 10, 102),
(106, 1, 103),
(106, 10, 101),
(106, 10, 102),
(107, 1, 103),
(107, 10, 101),
(107, 10, 102),
(108, 1, 103),
(108, 10, 101),
(108, 10, 102),
(109, 1, 103),
(109, 10, 101),
(109, 10, 102),
(110, 1, 103),
(110, 10, 101),
(110, 10, 102),
(111, 1, 103),
(111, 10, 101),
(111, 10, 102),
(112, 1, 103),
(112, 10, 101),
(112, 10, 102),
(113, 1, 103),
(113, 10, 101),
(113, 10, 102),
(114, 1, 103),
(114, 10, 101),
(114, 10, 102),
(115, 1, 103),
(115, 10, 101),
(115, 10, 102),
(116, 1, 103),
(116, 10, 101),
(116, 10, 102),
(117, 1, 103),
(117, 10, 101),
(117, 10, 102),
(118, 1, 103),
(118, 10, 101),
(118, 10, 102),
(119, 1, 103),
(119, 10, 101),
(119, 10, 102),
(120, 1, 103),
(120, 10, 101),
(120, 10, 102),
(121, 1, 103),
(121, 10, 101),
(121, 10, 102),
(122, 1, 103),
(122, 10, 101),
(122, 10, 102),
(123, 1, 103),
(123, 10, 101),
(123, 10, 102),
(124, 1, 103),
(124, 10, 101),
(124, 10, 102),
(125, 1, 103),
(125, 10, 101),
(125, 10, 102),
(126, 1, 103),
(126, 10, 101),
(126, 10, 102),
(127, 1, 103),
(127, 10, 101),
(127, 10, 102),
(128, 1, 103),
(128, 10, 101),
(128, 10, 102),
(129, 1, 103),
(129, 10, 101),
(129, 10, 102),
(130, 1, 103),
(130, 10, 101),
(130, 10, 102),
(131, 1, 101),
(131, 1, 102),
(131, 1, 103),
(132, 1, 101),
(132, 1, 102),
(132, 1, 103),
(133, 1, 101),
(133, 1, 102),
(133, 1, 103),
(134, 1, 101),
(134, 1, 102),
(134, 1, 103),
(135, 1, 101),
(135, 1, 102),
(135, 1, 103),
(136, 1, 101),
(136, 1, 102),
(136, 1, 103),
(137, 1, 101),
(137, 1, 102),
(137, 1, 103),
(138, 1, 101),
(138, 1, 102),
(138, 1, 103),
(139, 1, 101),
(139, 1, 102),
(139, 1, 103),
(140, 1, 101),
(140, 1, 102),
(140, 1, 103),
(141, 1, 101),
(141, 1, 102),
(141, 1, 103),
(142, 1, 101),
(142, 1, 102),
(142, 1, 103),
(143, 1, 101),
(143, 1, 102),
(143, 1, 103),
(144, 1, 101),
(144, 1, 102),
(144, 1, 103),
(145, 1, 101),
(145, 1, 102),
(145, 1, 103),
(146, 1, 101),
(146, 1, 102),
(146, 1, 103),
(147, 1, 101),
(147, 1, 102),
(147, 1, 103),
(148, 1, 101),
(148, 1, 102),
(148, 1, 103),
(149, 1, 101),
(149, 1, 102),
(149, 1, 103),
(150, 1, 103),
(150, 10, 101),
(150, 10, 102),
(151, 1, 101),
(151, 1, 102),
(151, 1, 103),
(152, 1, 101),
(152, 1, 102),
(152, 1, 103),
(153, 1, 101),
(153, 1, 102),
(153, 1, 103),
(154, 1, 101),
(154, 1, 102),
(154, 1, 103),
(155, 1, 101),
(155, 1, 102),
(155, 1, 103),
(156, 1, 101),
(156, 1, 102),
(156, 1, 103),
(157, 1, 103),
(157, 3, 101),
(157, 3, 102),
(158, 1, 103),
(158, 3, 101),
(158, 3, 102),
(159, 1, 103),
(159, 10, 101),
(159, 10, 102),
(160, 1, 101),
(160, 1, 102),
(160, 1, 103),
(161, 1, 101),
(161, 1, 102),
(161, 1, 103),
(162, 1, 101),
(162, 1, 102),
(162, 1, 103),
(163, 1, 101),
(163, 1, 102),
(163, 1, 103),
(164, 1, 101),
(164, 1, 102),
(164, 1, 103),
(165, 1, 101),
(165, 1, 102),
(165, 1, 103),
(166, 1, 101),
(166, 1, 102),
(166, 1, 103),
(167, 1, 101),
(167, 1, 102),
(167, 1, 103),
(168, 1, 101),
(168, 1, 102),
(168, 1, 103),
(169, 1, 101),
(169, 1, 102),
(169, 1, 103);

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
(2, 'Skript na inicializaci EditArei v detailu strÃ¡nky', 1, 1),
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
(12, 'Frame.nï¿½povï¿½dapro', 1, 0, 0, 797, 511, 0),
(13, 'Frame.nï¿½povï¿½da(vï¿½bï¿½r)pro', 1, 0, 0, 400, 61, 0),
(14, 'Frame.selecthelp', 1, 284, 78, 400, 82, 0),
(15, 'Frame.webprojects', 1, 255, 64, 705, 438, 0),
(16, 'Frame.editwebproject', 1, 292, 21, 850, 537, 0),
(17, 'Frame.userlist', 1, 353, 50, 847, 467, 0),
(18, 'Frame.newuser', 1, 0, 0, 145, 33, 0),
(19, 'Frame.edituser', 1, 315, 368, 691, 343, 0),
(20, 'Frame.sezï¿½ny', 1, 0, 0, 408, 142, 0),
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
(45, 'Frame.tï¿½my', 1, 0, 0, 408, 81, 0),
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

