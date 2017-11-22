-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2017 at 08:13 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phpwfw`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '2'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_attached_label`
--

CREATE TABLE `article_attached_label` (
  `article_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_content`
--

CREATE TABLE `article_content` (
  `article_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `keywords` tinytext COLLATE utf8_czech_ci NOT NULL,
  `head` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci,
  `author` tinytext COLLATE utf8_czech_ci,
  `timestamp` int(11) NOT NULL,
  `datetime` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_label`
--

CREATE TABLE `article_label` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_line`
--

CREATE TABLE `article_line` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_line_label`
--

CREATE TABLE `article_line_label` (
  `line_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_line_right`
--

CREATE TABLE `article_line_right` (
  `line_id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;

--
-- Dumping data for table `article_line_right`
--

INSERT INTO `article_line_right` (`line_id`, `gid`, `type`) VALUES
(0, 1, 102),
(0, 1, 103),
(0, 3, 101);

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `tag_lib_start` text COLLATE utf8_czech_ci NOT NULL,
  `tag_lib_end` text COLLATE utf8_czech_ci NOT NULL,
  `head` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counter`
--

CREATE TABLE `counter` (
  `ip` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customform`
--

CREATE TABLE `customform` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `fields` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `db_connection`
--

CREATE TABLE `db_connection` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `hostname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `user` tinytext COLLATE utf8_czech_ci NOT NULL,
  `password` tinytext COLLATE utf8_czech_ci NOT NULL,
  `database` tinytext COLLATE utf8_czech_ci NOT NULL,
  `fs_root` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `directory`
--

CREATE TABLE `directory` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `directory_right`
--

CREATE TABLE `directory_right` (
  `did` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `directory_right`
--

INSERT INTO `directory_right` (`did`, `gid`, `type`) VALUES
(0, 1, 101),
(0, 1, 102),
(0, 3, 103);

-- --------------------------------------------------------

--
-- Table structure for table `embedded_resource`
--

CREATE TABLE `embedded_resource` (
  `id` int(11) NOT NULL,
  `type` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `rid` int(11) DEFAULT NULL,
  `cache` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `dir_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `title` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_right`
--

CREATE TABLE `file_right` (
  `fid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL
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

CREATE TABLE `form_order1` (
  `id` int(11) NOT NULL,
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
  `ip` varchar(16) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_order2`
--

CREATE TABLE `form_order2` (
  `id` int(11) NOT NULL,
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
  `ip` varchar(16) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE `group` (
  `gid` int(11) NOT NULL,
  `parent_gid` int(11) NOT NULL DEFAULT '1',
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

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
-- Table structure for table `group_perms`
--

CREATE TABLE `group_perms` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `group_perms`
--

INSERT INTO `group_perms` (`id`, `group_id`, `name`, `value`, `type`) VALUES
(1, 1, 'CMS.Web.Pages', 'true', 'bool'),
(2, 1, 'CMS.Web', 'true', 'bool'),
(3, 1, 'CMS.Web.TextFiles', 'true', 'bool'),
(4, 1, 'CMS.Web.Templates', 'true', 'bool'),
(5, 1, 'CMS.Web.Articles', 'true', 'bool'),
(6, 1, 'CMS.Web.Guestbooks', 'true', 'bool'),
(7, 1, 'CMS.Web.EmbeddedResources', 'true', 'bool'),
(8, 1, 'CMS.Web.WebForwards', 'true', 'bool'),
(9, 1, 'CMS.Web.WebProjects', 'true', 'bool'),
(10, 1, 'CMS.Web.FileManager', 'true', 'bool'),
(11, 1, 'CMS.Web.CustomForms', 'true', 'bool'),
(12, 1, 'CMS.Hint', 'true', 'bool'),
(13, 1, 'CMS.HintProperties', 'true', 'bool'),
(14, 1, 'CMS.HintPerms', 'true', 'bool'),
(15, 1, 'CMS.Web.Floorball', 'true', 'bool'),
(16, 1, 'CMS.Floorball.Projects', 'true', 'bool'),
(17, 1, 'CMS.Floorball.Seasons', 'true', 'bool'),
(18, 1, 'CMS.Floorball.Rounds', 'true', 'bool'),
(19, 1, 'CMS.Floorball.Tables', 'true', 'bool'),
(20, 1, 'CMS.Floorball.Teams', 'true', 'bool'),
(21, 1, 'CMS.Floorball.Players', 'true', 'bool'),
(22, 1, 'CMS.Floorball.Matches', 'true', 'bool'),
(23, 1, 'CMS.Floorball.TablesContent', 'true', 'bool'),
(24, 1, 'CMS.Web.Settings', 'true', 'bool'),
(25, 1, 'CMS.Settings.Users', 'true', 'bool'),
(26, 1, 'CMS.Settings.UrlCache', 'true', 'bool'),
(27, 1, 'CMS.Settings.UserLog', 'true', 'bool'),
(28, 1, 'CMS.Settings.Languages', 'true', 'bool'),
(29, 1, 'CMS.Settings.Keywords', 'true', 'bool'),
(30, 1, 'CMS.Settings.ApplicationLog', 'true', 'bool'),
(31, 1, 'CMS.Settings.DatabaseConnections', 'true', 'bool'),
(32, 1, 'CMS.Settings.PersonalProperties', 'true', 'bool'),
(33, 1, 'CMS.Settings.PersonalNotes', 'true', 'bool'),
(34, 1, 'CMS.Settings.Groups', 'true', 'bool'),
(35, 1, 'CMS.Settings', 'true', 'bool'),
(36, 1, 'CMS.Floorball', 'true', 'bool'),
(37, 1, 'CMS.Web.ArticleLabels', 'true', 'bool'),
(38, 1, 'CMS.Web.ArticleLines', 'true', 'bool'),
(39, 1, 'CMS.Web.Pages', 'true', 'bool'),
(40, 1, 'CMS.Web', 'true', 'bool'),
(41, 1, 'CMS.Web.TextFiles', 'true', 'bool'),
(42, 1, 'CMS.Web.Templates', 'true', 'bool'),
(43, 1, 'CMS.Web.Articles', 'true', 'bool'),
(44, 1, 'CMS.Web.Guestbooks', 'true', 'bool'),
(45, 1, 'CMS.Web.EmbeddedResources', 'true', 'bool'),
(46, 1, 'CMS.Web.WebForwards', 'true', 'bool'),
(47, 1, 'CMS.Web.WebProjects', 'true', 'bool'),
(48, 1, 'CMS.Web.FileManager', 'true', 'bool'),
(49, 1, 'CMS.Web.CustomForms', 'true', 'bool'),
(50, 1, 'CMS.Hint', 'true', 'bool'),
(51, 1, 'CMS.HintProperties', 'true', 'bool'),
(52, 1, 'CMS.HintPerms', 'true', 'bool'),
(53, 1, 'CMS.Web.Floorball', 'true', 'bool'),
(54, 1, 'CMS.Floorball.Projects', 'true', 'bool'),
(55, 1, 'CMS.Floorball.Seasons', 'true', 'bool'),
(56, 1, 'CMS.Floorball.Rounds', 'true', 'bool'),
(57, 1, 'CMS.Floorball.Tables', 'true', 'bool'),
(58, 1, 'CMS.Floorball.Teams', 'true', 'bool'),
(59, 1, 'CMS.Floorball.Players', 'true', 'bool'),
(60, 1, 'CMS.Floorball.Matches', 'true', 'bool'),
(61, 1, 'CMS.Floorball.TablesContent', 'true', 'bool'),
(62, 1, 'CMS.Web.Settings', 'true', 'bool'),
(63, 1, 'CMS.Settings.Users', 'true', 'bool'),
(64, 1, 'CMS.Settings.UrlCache', 'true', 'bool'),
(65, 1, 'CMS.Settings.UserLog', 'true', 'bool'),
(66, 1, 'CMS.Settings.Languages', 'true', 'bool'),
(67, 1, 'CMS.Settings.Keywords', 'true', 'bool'),
(68, 1, 'CMS.Settings.ApplicationLog', 'true', 'bool'),
(69, 1, 'CMS.Settings.DatabaseConnections', 'true', 'bool'),
(70, 1, 'CMS.Settings.PersonalProperties', 'true', 'bool'),
(71, 1, 'CMS.Settings.PersonalNotes', 'true', 'bool'),
(72, 1, 'CMS.Settings.Groups', 'true', 'bool'),
(73, 1, 'CMS.Settings', 'true', 'bool'),
(74, 1, 'CMS.Floorball', 'true', 'bool'),
(75, 1, 'CMS.Web.ArticleLabels', 'true', 'bool'),
(76, 1, 'CMS.Web.ArticleLines', 'true', 'bool'),
(77, 1, 'CMS.Settings.AdminMenu', 'true', 'bool'),
(78, 1, 'CMS.AdminMenu', 'true', 'bool'),
(79, 1, 'CMS.Settings.AdminMenu', 'true', 'bool'),
(80, 1, 'CMS.AdminMenu', 'true', 'bool');

-- --------------------------------------------------------

--
-- Table structure for table `guestbook`
--

CREATE TABLE `guestbook` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `guestbook_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE `info` (
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `title` tinytext COLLATE utf8_czech_ci NOT NULL,
  `in_title` int(11) NOT NULL DEFAULT '1',
  `href` tinytext COLLATE utf8_czech_ci NOT NULL,
  `in_menu` int(11) NOT NULL,
  `page_pos` int(11) NOT NULL,
  `is_visible` int(11) NOT NULL,
  `keywords` tinytext COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL DEFAULT '-1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry`
--

CREATE TABLE `inquiry` (
  `id` int(11) NOT NULL,
  `question` tinytext COLLATE utf8_czech_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `allow_multiple` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_answer`
--

CREATE TABLE `inquiry_answer` (
  `id` int(11) NOT NULL,
  `inquiry_id` int(11) NOT NULL,
  `answer` tinytext COLLATE utf8_czech_ci NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_vote`
--

CREATE TABLE `inquiry_vote` (
  `id` int(11) NOT NULL,
  `inquiry_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `ip_address` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `language` tinytext COLLATE utf8_czech_ci NOT NULL
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

CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_file`
--

CREATE TABLE `page_file` (
  `id` int(11) NOT NULL,
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
  `wp` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_file_inc`
--

CREATE TABLE `page_file_inc` (
  `file_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_property_value`
--

CREATE TABLE `page_property_value` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_right`
--

CREATE TABLE `page_right` (
  `pid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `page_right`
--

INSERT INTO `page_right` (`pid`, `gid`, `type`) VALUES
(0, 2, 102),
(0, 2, 103),
(0, 2, 104),
(0, 3, 101);

-- --------------------------------------------------------

--
-- Table structure for table `pair_uid_property`
--

CREATE TABLE `pair_uid_property` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `property_name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `property_value` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_note`
--

CREATE TABLE `personal_note` (
  `id` int(11) NOT NULL,
  `value` mediumtext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_property`
--

CREATE TABLE `personal_property` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `personal_property`
--

INSERT INTO `personal_property` (`id`, `user_id`, `name`, `value`, `type`) VALUES
(1, 1, 'WebProject.defaultProjectId', '1', 1),
(2, 1, 'Frame.systemproperties', 'false', 1),
(3, 1, 'System.cms.windowsstyle', 'false', 1),
(4, 1, 'Frame.newfile', 'false', 1),
(5, 1, 'Frame.newdirectory', 'false', 1),
(6, 1, 'Page.editors', 'edit_area', 1),
(7, 1, 'Page.editAreaTLStartRows', '20', 1),
(8, 1, 'Page.editAreaTLEndRows', '24', 1),
(9, 1, 'Page.editAreaHeadRows', '24', 1),
(10, 1, 'Page.editAreaContentRows', '30', 1),
(11, 1, 'Login.session', '30', 1),
(12, 1, 'Article.editors', 'tiny', 1),
(13, 1, 'Article.editAreaHeadRows', '30', 1),
(14, 1, 'Article.languageId', '2', 1),
(15, 1, 'Admin.Language', 'cs', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rolecache`
--

CREATE TABLE `rolecache` (
  `id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_adminmenu`
--

CREATE TABLE `system_adminmenu` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `page_id` tinytext COLLATE utf8_czech_ci NOT NULL,
  `icon` tinytext COLLATE utf8_czech_ci NOT NULL,
  `perm` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_property`
--

CREATE TABLE `system_property` (
  `id` int(11) NOT NULL,
  `key` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `system_property`
--

INSERT INTO `system_property` (`id`, `key`, `value`) VALUES
(1, 'db_version', '330'),
(4, 'FileAdmin.fileSystemTransformed', '1');

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE `template` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `template_right`
--

CREATE TABLE `template_right` (
  `tid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `system_property`
--

INSERT INTO `template_right` (`tid`, `gid`, `type`) VALUES 
(0, 1, 101), 
(0, 1, 102), 
(0, 3, 103);

-- --------------------------------------------------------

--
-- Table structure for table `universal_permission`
--

CREATE TABLE `universal_permission` (
  `discriminator` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `object_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `urlcache`
--

CREATE TABLE `urlcache` (
  `id` int(11) NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `domain_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `root_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `virtual_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `http` int(11) NOT NULL,
  `https` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `pages_id` tinytext COLLATE utf8_czech_ci NOT NULL,
  `language_id` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL,
  `lastcache` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `surname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `login` tinytext COLLATE utf8_czech_ci NOT NULL,
  `password` tinytext COLLATE utf8_czech_ci NOT NULL,
  `enable` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `group_id`, `name`, `surname`, `login`, `password`, `enable`) VALUES
(1, 1, 'admin', 'admin', 'admin', 'b49a387e1143eccc5d6cb585d49290c2e2a85145', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_in_group`
--

CREATE TABLE `user_in_group` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL
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

CREATE TABLE `user_log` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `login_timestamp` int(11) NOT NULL,
  `logout_timestamp` int(11) NOT NULL,
  `used_group` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`id`, `session_id`, `user_id`, `timestamp`, `login_timestamp`, `logout_timestamp`, `used_group`) VALUES
(1, 222808, 1, 1510595023, 1510594955, 1510595923, 'web-admins');

-- --------------------------------------------------------

--
-- Table structure for table `web_forward`
--

CREATE TABLE `web_forward` (
  `id` int(11) NOT NULL,
  `type` tinytext COLLATE utf8_czech_ci NOT NULL,
  `rule` tinytext COLLATE utf8_czech_ci NOT NULL,
  `condition` tinytext COLLATE utf8_czech_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_project`
--

CREATE TABLE `web_project` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `error_all_pid` int(11) NOT NULL,
  `error_404_pid` int(11) NOT NULL,
  `error_403_pid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_project_right`
--

CREATE TABLE `web_project_right` (
  `wp` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `web_project_right`
--

INSERT INTO `web_project_right` (`wp`, `gid`, `type`) VALUES
(0, 1, 102),
(0, 1, 103),
(0, 3, 101);

-- --------------------------------------------------------

--
-- Table structure for table `web_url`
--

CREATE TABLE `web_url` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `domain_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `root_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `virtual_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `http` int(11) NOT NULL,
  `https` int(11) NOT NULL,
  `default` int(11) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `window_properties`
--

CREATE TABLE `window_properties` (
  `id` int(11) NOT NULL,
  `frame_id` tinytext COLLATE utf8_czech_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `left` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `maximized` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `wp_wysiwyg_file`
--

CREATE TABLE `wp_wysiwyg_file` (
  `wp` int(11) NOT NULL,
  `tf_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_projection`
--

CREATE TABLE `w_projection` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `subname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_reference`
--

CREATE TABLE `w_reference` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `subname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `visible` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_match`
--

CREATE TABLE `w_sport_match` (
  `id` int(10) UNSIGNED NOT NULL,
  `h_team` int(10) UNSIGNED NOT NULL,
  `a_team` int(10) UNSIGNED NOT NULL,
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
  `project_id` int(11) NOT NULL DEFAULT '1',
  `date` tinytext COLLATE utf8_czech_ci NOT NULL,
  `time` tinytext COLLATE utf8_czech_ci NOT NULL,
  `refs` tinytext COLLATE utf8_czech_ci NOT NULL,
  `refs2` tinytext COLLATE utf8_czech_ci NOT NULL,
  `place` tinytext COLLATE utf8_czech_ci NOT NULL,
  `main_stuff` tinytext COLLATE utf8_czech_ci NOT NULL,
  `stuff` tinytext COLLATE utf8_czech_ci NOT NULL,
  `stuff2` tinytext COLLATE utf8_czech_ci NOT NULL,
  `notplayed` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_player`
--

CREATE TABLE `w_sport_player` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `surname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `birthyear` int(3) UNSIGNED NOT NULL,
  `number` int(3) UNSIGNED NOT NULL,
  `position` int(3) UNSIGNED NOT NULL,
  `photo` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season` int(10) UNSIGNED NOT NULL,
  `team` int(10) UNSIGNED NOT NULL,
  `on_loan` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_project`
--

CREATE TABLE `w_sport_project` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_round`
--

CREATE TABLE `w_sport_round` (
  `id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_season`
--

CREATE TABLE `w_sport_season` (
  `id` int(10) UNSIGNED NOT NULL,
  `start_year` int(10) UNSIGNED NOT NULL,
  `end_year` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_stats`
--

CREATE TABLE `w_sport_stats` (
  `pid` int(10) UNSIGNED NOT NULL,
  `pos` int(11) NOT NULL,
  `mid` int(10) UNSIGNED NOT NULL,
  `goals` tinyint(3) UNSIGNED NOT NULL,
  `assists` tinyint(3) UNSIGNED NOT NULL,
  `penalty` tinyint(3) UNSIGNED NOT NULL,
  `shoots` tinyint(3) UNSIGNED NOT NULL,
  `season` tinyint(3) UNSIGNED NOT NULL,
  `table_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_table`
--

CREATE TABLE `w_sport_table` (
  `team` int(10) UNSIGNED NOT NULL,
  `matches` int(10) UNSIGNED NOT NULL,
  `wins` tinyint(3) UNSIGNED DEFAULT NULL,
  `draws` tinyint(3) UNSIGNED DEFAULT NULL,
  `loses` tinyint(3) UNSIGNED DEFAULT NULL,
  `s_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `r_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `season` tinyint(3) UNSIGNED NOT NULL,
  `positionfix` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_tables`
--

CREATE TABLE `w_sport_tables` (
  `id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_team`
--

CREATE TABLE `w_sport_team` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `logo` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `article_content`
--
ALTER TABLE `article_content`
  ADD PRIMARY KEY (`article_id`,`language_id`);

--
-- Indexes for table `article_label`
--
ALTER TABLE `article_label`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `article_line`
--
ALTER TABLE `article_line`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `article_line_right`
--
ALTER TABLE `article_line_right`
  ADD PRIMARY KEY (`line_id`,`gid`,`type`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`page_id`,`language_id`);

--
-- Indexes for table `counter`
--
ALTER TABLE `counter`
  ADD PRIMARY KEY (`counter_id`,`ip`);

--
-- Indexes for table `customform`
--
ALTER TABLE `customform`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_connection`
--
ALTER TABLE `db_connection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `directory`
--
ALTER TABLE `directory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `directory_right`
--
ALTER TABLE `directory_right`
  ADD PRIMARY KEY (`did`,`gid`,`type`);

--
-- Indexes for table `embedded_resource`
--
ALTER TABLE `embedded_resource`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file_right`
--
ALTER TABLE `file_right`
  ADD PRIMARY KEY (`fid`,`gid`,`type`);

--
-- Indexes for table `form_order1`
--
ALTER TABLE `form_order1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_order2`
--
ALTER TABLE `form_order2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`gid`);

--
-- Indexes for table `group_perms`
--
ALTER TABLE `group_perms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guestbook`
--
ALTER TABLE `guestbook`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`page_id`,`language_id`);

--
-- Indexes for table `inquiry`
--
ALTER TABLE `inquiry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiry_answer`
--
ALTER TABLE `inquiry_answer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiry_vote`
--
ALTER TABLE `inquiry_vote`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_file`
--
ALTER TABLE `page_file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_file_inc`
--
ALTER TABLE `page_file_inc`
  ADD PRIMARY KEY (`file_id`,`page_id`,`language_id`);

--
-- Indexes for table `page_property_value`
--
ALTER TABLE `page_property_value`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_right`
--
ALTER TABLE `page_right`
  ADD PRIMARY KEY (`pid`,`gid`,`type`);

--
-- Indexes for table `pair_uid_property`
--
ALTER TABLE `pair_uid_property`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_note`
--
ALTER TABLE `personal_note`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_property`
--
ALTER TABLE `personal_property`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rolecache`
--
ALTER TABLE `rolecache`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_adminmenu`
--
ALTER TABLE `system_adminmenu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_property`
--
ALTER TABLE `system_property`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template`
--
ALTER TABLE `template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_right`
--
ALTER TABLE `template_right`
  ADD PRIMARY KEY (`tid`,`gid`,`type`);

--
-- Indexes for table `universal_permission`
--
ALTER TABLE `universal_permission`
  ADD PRIMARY KEY (`discriminator`,`object_id`,`group_id`,`type`);

--
-- Indexes for table `urlcache`
--
ALTER TABLE `urlcache`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `user_in_group`
--
ALTER TABLE `user_in_group`
  ADD PRIMARY KEY (`uid`,`gid`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`);

--
-- Indexes for table `web_forward`
--
ALTER TABLE `web_forward`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_project`
--
ALTER TABLE `web_project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_project_right`
--
ALTER TABLE `web_project_right`
  ADD PRIMARY KEY (`wp`,`gid`,`type`);

--
-- Indexes for table `web_url`
--
ALTER TABLE `web_url`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `window_properties`
--
ALTER TABLE `window_properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `w_projection`
--
ALTER TABLE `w_projection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `w_reference`
--
ALTER TABLE `w_reference`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `w_sport_match`
--
ALTER TABLE `w_sport_match`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`,`season`,`a_team`,`h_team`),
  ADD KEY `h_team` (`h_team`),
  ADD KEY `a_team` (`a_team`);

--
-- Indexes for table `w_sport_player`
--
ALTER TABLE `w_sport_player`
  ADD PRIMARY KEY (`id`,`position`,`season`,`team`),
  ADD KEY `season` (`season`),
  ADD KEY `team` (`team`);

--
-- Indexes for table `w_sport_project`
--
ALTER TABLE `w_sport_project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `w_sport_round`
--
ALTER TABLE `w_sport_round`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `w_sport_season`
--
ALTER TABLE `w_sport_season`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `w_sport_stats`
--
ALTER TABLE `w_sport_stats`
  ADD PRIMARY KEY (`pid`,`pos`,`mid`,`season`,`table_id`),
  ADD KEY `season` (`season`);

--
-- Indexes for table `w_sport_table`
--
ALTER TABLE `w_sport_table`
  ADD PRIMARY KEY (`team`,`season`,`table_id`),
  ADD KEY `season` (`season`);

--
-- Indexes for table `w_sport_tables`
--
ALTER TABLE `w_sport_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `w_sport_team`
--
ALTER TABLE `w_sport_team`
  ADD PRIMARY KEY (`id`,`season`),
  ADD KEY `season` (`season`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article_label`
--
ALTER TABLE `article_label`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `article_line`
--
ALTER TABLE `article_line`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customform`
--
ALTER TABLE `customform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_connection`
--
ALTER TABLE `db_connection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `directory`
--
ALTER TABLE `directory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `embedded_resource`
--
ALTER TABLE `embedded_resource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_order1`
--
ALTER TABLE `form_order1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_order2`
--
ALTER TABLE `form_order2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `group_perms`
--
ALTER TABLE `group_perms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `guestbook`
--
ALTER TABLE `guestbook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry`
--
ALTER TABLE `inquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry_answer`
--
ALTER TABLE `inquiry_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry_vote`
--
ALTER TABLE `inquiry_vote`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_file`
--
ALTER TABLE `page_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_property_value`
--
ALTER TABLE `page_property_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pair_uid_property`
--
ALTER TABLE `pair_uid_property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_note`
--
ALTER TABLE `personal_note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_property`
--
ALTER TABLE `personal_property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rolecache`
--
ALTER TABLE `rolecache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_adminmenu`
--
ALTER TABLE `system_adminmenu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_property`
--
ALTER TABLE `system_property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `template`
--
ALTER TABLE `template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `urlcache`
--
ALTER TABLE `urlcache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `web_forward`
--
ALTER TABLE `web_forward`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_project`
--
ALTER TABLE `web_project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_url`
--
ALTER TABLE `web_url`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `window_properties`
--
ALTER TABLE `window_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `w_projection`
--
ALTER TABLE `w_projection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `w_reference`
--
ALTER TABLE `w_reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `w_sport_match`
--
ALTER TABLE `w_sport_match`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `w_sport_player`
--
ALTER TABLE `w_sport_player`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `w_sport_project`
--
ALTER TABLE `w_sport_project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `w_sport_round`
--
ALTER TABLE `w_sport_round`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `w_sport_season`
--
ALTER TABLE `w_sport_season`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `w_sport_tables`
--
ALTER TABLE `w_sport_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `w_sport_team`
--
ALTER TABLE `w_sport_team`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
