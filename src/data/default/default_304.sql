SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
--
-- Table structure for table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `article_attached_label`
--

CREATE TABLE IF NOT EXISTS `article_attached_label` (
  `article_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `article_content`
--

CREATE TABLE IF NOT EXISTS `article_content` (
  `article_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `keywords` tinytext COLLATE utf8_czech_ci NOT NULL,
  `head` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci,
  `author` tinytext COLLATE utf8_czech_ci,
  `timestamp` int(11) NOT NULL,
  `datetime` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`article_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `article_label`
--

CREATE TABLE IF NOT EXISTS `article_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `article_line`
--

CREATE TABLE IF NOT EXISTS `article_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `article_line_label`
--

CREATE TABLE IF NOT EXISTS `article_line_label` (
  `line_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `article_line_right`
--

CREATE TABLE IF NOT EXISTS `article_line_right` (
  `line_id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`line_id`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;

--
-- Dumping data for table `article_line_right`
--

INSERT INTO `article_line_right` (`line_id`, `gid`, `type`) VALUES
(0, 1, 102),
(0, 1, 103),
(0, 3, 101);

--
-- Table structure for table `content`
--

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
-- Table structure for table `counter`
--

CREATE TABLE IF NOT EXISTS `counter` (
  `ip` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL,
  PRIMARY KEY (`counter_id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `customform`
--

CREATE TABLE IF NOT EXISTS `customform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `fields` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `db_connection`
--

CREATE TABLE IF NOT EXISTS `db_connection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `hostname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `user` tinytext COLLATE utf8_czech_ci NOT NULL,
  `password` tinytext COLLATE utf8_czech_ci NOT NULL,
  `database` tinytext COLLATE utf8_czech_ci NOT NULL,
  `fs_root` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `directory`
--

CREATE TABLE IF NOT EXISTS `directory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `directory_right`
--

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
-- Table structure for table `embedded_resource`
--

CREATE TABLE IF NOT EXISTS `embedded_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `rid` int(11) DEFAULT NULL,
  `cache` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dir_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `title` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `file_right`
--

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

--
-- Table structure for table `form_order1`
--

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
-- Table structure for table `form_order2`
--

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
-- Table structure for table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_gid` int(11) NOT NULL DEFAULT '1',
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`gid`, `parent_gid`, `name`, `value`) VALUES
(1, 0, 'admins', 1),
(2, 1, 'web-admins', 50),
(3, 2, 'web', 254),
(4, 1, 'web-projects', 60);

--
-- Table structure for table `group_perms`
--

CREATE TABLE IF NOT EXISTS `group_perms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

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

--
-- Table structure for table `guestbook`
--

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
-- Table structure for table `info`
--

CREATE TABLE IF NOT EXISTS `info` (
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
  `cachetime` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `language`
--

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

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `page_file`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `page_file_inc`
--

CREATE TABLE IF NOT EXISTS `page_file_inc` (
  `file_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`file_id`,`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `page_right`
--

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
(0, 2, 104),
(0, 3, 101);

--
-- Table structure for table `pair_uid_property`
--

CREATE TABLE IF NOT EXISTS `pair_uid_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `property_name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `property_value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `personal_note`
--

CREATE TABLE IF NOT EXISTS `personal_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` mediumtext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `personal_property`
--

CREATE TABLE IF NOT EXISTS `personal_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

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
-- Table structure for table `system_adminmenu`
--

CREATE TABLE IF NOT EXISTS `system_adminmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `page_id` tinytext COLLATE utf8_czech_ci NOT NULL,
  `icon` tinytext COLLATE utf8_czech_ci NOT NULL,
  `perm` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `system_property`
--

CREATE TABLE IF NOT EXISTS `system_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `system_property`
--

INSERT INTO `system_property` (`id`, `key`, `value`) VALUES
(1, 'db_version', '304');

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `template_right`
--

CREATE TABLE IF NOT EXISTS `template_right` (
  `tid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`tid`,`gid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `template_right`
--

INSERT INTO `template_right` (`tid`, `gid`, `type`) VALUES
(0, 1, 102),
(0, 1, 103),
(0, 3, 101);

--
-- Table structure for table `universal_permission`
--

CREATE TABLE IF NOT EXISTS `universal_permission` (
  `discriminator` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `object_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`discriminator`,`object_id`,`group_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `urlcache`
--

CREATE TABLE IF NOT EXISTS `urlcache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `lastcache` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `surname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `login` tinytext COLLATE utf8_czech_ci NOT NULL,
  `password` tinytext COLLATE utf8_czech_ci NOT NULL,
  `enable` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `group_id`, `name`, `surname`, `login`, `password`, `enable`) VALUES
(1, 1, 'admin', 'admin', 'admin', 'b49a387e1143eccc5d6cb585d49290c2e2a85145', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_in_group`
--

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
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `web_forward`
--

CREATE TABLE IF NOT EXISTS `web_forward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinytext COLLATE utf8_czech_ci NOT NULL,
  `rule` tinytext COLLATE utf8_czech_ci NOT NULL,
  `condition` tinytext COLLATE utf8_czech_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `web_project`
--

CREATE TABLE IF NOT EXISTS `web_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `error_all_pid` int(11) NOT NULL,
  `error_404_pid` int(11) NOT NULL,
  `error_403_pid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `web_project_right`
--

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
(0, 3, 101);

--
-- Table structure for table `web_url`
--

CREATE TABLE IF NOT EXISTS `web_url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `domain_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `root_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `virtual_url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `http` int(11) NOT NULL,
  `https` int(11) NOT NULL,
  `default` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `window_properties`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Table structure for table `wp_wysiwyg_file`
--

CREATE TABLE IF NOT EXISTS `wp_wysiwyg_file` (
  `wp` int(11) NOT NULL,
  `tf_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `w_projection`
--

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
-- Table structure for table `w_reference`
--

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
-- Table structure for table `w_sport_match`
--

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
  `project_id` int(11) NOT NULL DEFAULT '1',
  `date` tinytext COLLATE utf8_czech_ci NOT NULL,
  `time` tinytext COLLATE utf8_czech_ci NOT NULL,
  `refs` tinytext COLLATE utf8_czech_ci NOT NULL,
  `refs2` tinytext COLLATE utf8_czech_ci NOT NULL,
  `place` tinytext COLLATE utf8_czech_ci NOT NULL,
  `main_stuff` tinytext COLLATE utf8_czech_ci NOT NULL,
  `stuff` tinytext COLLATE utf8_czech_ci NOT NULL,
  `stuff2` tinytext COLLATE utf8_czech_ci NOT NULL,
  `notplayed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`season`,`a_team`,`h_team`),
  KEY `h_team` (`h_team`),
  KEY `a_team` (`a_team`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `w_sport_player`
--

CREATE TABLE IF NOT EXISTS `w_sport_player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `surname` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `birthyear` int(3) unsigned NOT NULL,
  `number` int(3) unsigned NOT NULL,
  `position` int(3) unsigned NOT NULL,
  `photo` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season` int(10) unsigned NOT NULL,
  `team` int(10) unsigned NOT NULL,
  `on_loan` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`position`,`season`,`team`),
  KEY `season` (`season`),
  KEY `team` (`team`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `w_sport_project`
--

CREATE TABLE IF NOT EXISTS `w_sport_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `w_sport_round`
--

CREATE TABLE IF NOT EXISTS `w_sport_round` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `w_sport_season`
--

CREATE TABLE IF NOT EXISTS `w_sport_season` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_year` int(10) unsigned NOT NULL,
  `end_year` int(10) unsigned NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `w_sport_stats`
--

CREATE TABLE IF NOT EXISTS `w_sport_stats` (
  `pid` int(10) unsigned NOT NULL,
  `pos` int(11) NOT NULL,
  `mid` int(10) unsigned NOT NULL,
  `goals` tinyint(3) unsigned NOT NULL,
  `assists` tinyint(3) unsigned NOT NULL,
  `penalty` tinyint(3) unsigned NOT NULL,
  `shoots` tinyint(3) unsigned NOT NULL,
  `season` tinyint(3) unsigned NOT NULL,
  `table_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pid`,`pos`,`mid`,`season`,`table_id`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `w_sport_table`
--

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
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`team`,`season`,`table_id`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Table structure for table `w_sport_tables`
--

CREATE TABLE IF NOT EXISTS `w_sport_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Table structure for table `w_sport_team`
--

CREATE TABLE IF NOT EXISTS `w_sport_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `logo` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season` int(10) unsigned NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`season`),
  KEY `season` (`season`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;
