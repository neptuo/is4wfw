-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2010 at 05:45 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tmp_wfw_wp_100817`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

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

CREATE TABLE IF NOT EXISTS `article_content` (
  `article_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
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

-- --------------------------------------------------------

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
-- Dumping data for table `content`
--


-- --------------------------------------------------------

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
-- Dumping data for table `counter`
--


-- --------------------------------------------------------

--
-- Table structure for table `customform`
--

CREATE TABLE IF NOT EXISTS `customform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `fields` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `customform`
--


-- --------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `db_connection`
--


-- --------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `directory`
--


-- --------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `file`
--


-- --------------------------------------------------------

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

-- --------------------------------------------------------

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
-- Dumping data for table `form_order1`
--


-- --------------------------------------------------------

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
-- Dumping data for table `form_order2`
--


-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_gid` int(11) NOT NULL DEFAULT '1',
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=17 ;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`gid`, `parent_gid`, `name`, `value`) VALUES
(1, 0, 'admins', 1),
(2, 1, 'web-admins', 50),
(3, 2, 'web', 254),
(4, 1, 'web-projects', 60),
(5, 1, 'cms-access', 2),
(6, 1, 'floorball-access', 2);

-- --------------------------------------------------------

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
-- Dumping data for table `guestbook`
--


-- --------------------------------------------------------

--
-- Table structure for table `info`
--

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


-- --------------------------------------------------------

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

-- --------------------------------------------------------

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
-- Dumping data for table `page`
--


-- --------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `page_file`
--


-- --------------------------------------------------------

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
-- Dumping data for table `page_file_inc`
--


-- --------------------------------------------------------

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
(0, 3, 101),
(228, 2, 102),
(228, 2, 103),
(228, 3, 101),
(229, 2, 102),
(229, 2, 103),
(229, 3, 101),
(230, 2, 102),
(230, 2, 103),
(230, 3, 101),
(231, 1, 102),
(231, 1, 103),
(231, 2, 101),
(232, 2, 102),
(232, 2, 103),
(232, 3, 101),
(233, 2, 102),
(233, 2, 103),
(233, 3, 101),
(234, 2, 102),
(234, 2, 103),
(234, 3, 101),
(235, 2, 102),
(235, 2, 103),
(235, 3, 101),
(236, 2, 102),
(236, 2, 103),
(236, 3, 101),
(237, 2, 102),
(237, 2, 103),
(237, 3, 101),
(238, 2, 102),
(238, 2, 103),
(238, 3, 101);

-- --------------------------------------------------------

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
-- Dumping data for table `pair_uid_property`
--


-- --------------------------------------------------------

--
-- Table structure for table `personal_note`
--

CREATE TABLE IF NOT EXISTS `personal_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` mediumtext COLLATE utf8_czech_ci NOT NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=31 ;

--
-- Dumping data for table `personal_note`
--

INSERT INTO `personal_note` (`id`, `value`, `type`, `user_id`) VALUES
(2, 'Skript na inicializaci EditArei v detailu strÃ¡nky', 1, 1),
(3, 'Po ulozeni nove stranky, se znovu ovetre okno, kde jiz je editace teto stranky, puvodni se vsak nezavre!', 3, 1),
(4, 'Vylepsit desktop REFRESH, mohl by se aktualizovat sam pri pridani System note.', 1, 1),
(5, 'Admin heslo pro localhost je 111111, zrusit autoLogin pro ostrou verzi.', 2, 1),
(7, 'Klavesove zkratky: Na Shift + O, otevrit radek za zadani adresy noveho okna v systemu, naseptavac ...', 1, 1),
(8, 'Klavesove zkratky: SHIFT + F12 -> Web Ajax Log, SHIFT + x -> Zavre okno, SHIFT + z -> Minimalizuje okno ( pokud jiz minimalizovane je, pak ho obnovi ), SHIFT + d -> zobrazi plochu.', 1, 1),
(9, 'Klavesove zkratky: Upravit ... pro rychle psani, ci vkladani textu -> pomale!!!', 2, 1),
(10, 'Klavesove zkratky: SHIFT + Tab -> prohazovani oken ... zobrazovat panel jako ve Win ;)', 1, 1),
(11, 'Klavesove zkratky: Esc na form element -> ztrata focusu.', 1, 1),
(12, 'Klavesove zkratky: Pokud zadny prvek mit fokus nebude, na Esc se priradi poslednimu, nebo prvnimu z aktiniho okna', 1, 1),
(13, '!!!! - Strankovani v tabulce -> Nefunguje Ajax', 3, 1),
(14, 'Pridat do Database lib tag pro zmenu database connection!', 5, 1),
(26, 'SPORT: Pridat tag pro zobrazeni vysledku tymu, se zadanim tableId pro vysledky z dane tabulky, seasonId pro vysledky z dane sezony, nebo bez pro vsechny vysledky. Jako SUM z hodnot z w_sport_table', 1, 1),
(27, 'SPORT: Dokoncit tagy od sport:rounds', 5, 1),
(28, 'Otestovat QueryStorage ...', 5, 1),
(29, 'Dodelat a Otestovat ukladani zapasu s pouzitim QueryStorage', 5, 1),
(30, 'BUG: Otevrit nejakou detail stranku v projektu Floorball na webu bez predchoziho vybrani projektu -> v URL ''Array'' => Stava se pri otevreni s vypisu stranek v CMS!! (zrejme nelze odstranit)', 3, 1);

-- --------------------------------------------------------

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=24 ;

--
-- Dumping data for table `personal_property`
--

INSERT INTO `personal_property` (`id`, `user_id`, `name`, `value`, `type`) VALUES
(23, 1, 'WebProject.defaultProjectId', '25', 1),
(6, 1, 'Frame.systemproperties', 'false', 1),
(22, 1, 'System.cms.windowsstyle', 'false', 1),
(12, 1, 'Frame.newfile', 'false', 1),
(13, 1, 'Frame.newdirectory', 'false', 1),
(21, 1, 'Page.editors', 'edit_area', 1),
(15, 1, 'Page.editAreaTLStartRows', '20', 1),
(16, 1, 'Page.editAreaTLEndRows', '24', 1),
(17, 1, 'Page.editAreaHeadRows', '24', 1),
(18, 1, 'Page.editAreaContentRows', '24', 1),
(19, 1, 'Login.session', '30', 1),
(20, 1, 'Article.editors', 'edit_area', 1);

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
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

-- --------------------------------------------------------

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
-- Dumping data for table `universal_permission`
--


-- --------------------------------------------------------

--
-- Table structure for table `urlcache`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `urlcache`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

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
(1, 4),
(1, 5),
(1, 6);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`id`, `session_id`, `user_id`, `timestamp`, `login_timestamp`, `logout_timestamp`, `used_group`) VALUES
(5, 619299, 1, 1281274872, 1281274657, 0, 'web-admins'),
(6, 933825, 1, 1282059903, 1282058445, 0, 'web-admins');

-- --------------------------------------------------------

--
-- Table structure for table `web_alias`
--

CREATE TABLE IF NOT EXISTS `web_alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  `http` int(11) NOT NULL,
  `https` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `web_alias`
--


-- --------------------------------------------------------

--
-- Table structure for table `web_project`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `web_project`
--


-- --------------------------------------------------------

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

-- --------------------------------------------------------

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=55 ;

--
-- Dumping data for table `window_properties`
--

INSERT INTO `window_properties` (`id`, `frame_id`, `user_id`, `left`, `top`, `width`, `height`, `maximized`) VALUES
(1, 'Frame.systemproperties', 1, 0, 0, 1902, 893, 1),
(4, 'Frame.editace', 1, 0, 0, 500, 300, 1),
(6, 'Frame.pages', 1, 50, 50, 945, 606, 1),
(7, 'Frame.newpage', 1, 0, 0, 408, 30, 0),
(8, 'Frame.editation', 1, 638, 31, 998, 705, 1),
(9, 'Frame.textfiles', 1, 390, 51, 909, 364, 0),
(10, 'Frame.editfile', 1, 0, 0, 500, 300, 1),
(11, 'Frame.newtextfile', 1, 0, 0, 408, 26, 0),
(12, 'Frame.nï¿½povï¿½dapro', 1, 0, 0, 797, 511, 0),
(13, 'Frame.nï¿½povï¿½da(vï¿½bï¿½r)pro', 1, 0, 0, 400, 61, 0),
(14, 'Frame.selecthelp', 1, 284, 78, 400, 82, 0),
(15, 'Frame.webprojects', 1, 923, 32, 705, 438, 0),
(16, 'Frame.editwebproject', 1, 40, 164, 850, 537, 0),
(17, 'Frame.userlist', 1, 399, 49, 847, 467, 0),
(18, 'Frame.newuser', 1, 0, 0, 145, 33, 0),
(19, 'Frame.edituser', 1, 315, 368, 691, 343, 0),
(20, 'Frame.sezï¿½ny', 1, 10, 10, 408, 142, 0),
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
(39, 'Frame.systemnotes', 1, 465, 141, 1098, 598, 0),
(40, 'Frame.addlanguage', 1, 458, 155, 408, 65, 0),
(41, 'Frame.languages', 1, 32, 156, 408, 209, 0),
(42, 'Frame.managekeywords', 1, 36, 26, 860, 70, 0),
(43, 'Frame.userlog', 1, 61, 75, 715, 380, 0),
(44, 'Frame.tabulky', 1, 50, 50, 408, 72, 0),
(45, 'Frame.tï¿½my', 1, 30, 30, 408, 81, 0),
(46, 'Frame.webbrowser', 1, 82, 101, 500, 300, 0),
(47, 'Frame.edit', 1, 0, 0, 500, 300, 1),
(48, 'Frame.truncateuserlog', 1, 929, 9, 408, 63, 0),
(49, 'Frame.vï¿½bï¿½rprojektu', 1, 50, 50, 500, 300, 0),
(50, 'Frame.databaseconnections', 1, 458, 106, 500, 300, 0),
(51, 'Frame.editdatabaseconnection', 1, 673, 129, 500, 300, 0),
(52, 'Frame.listdatabaseconnections', 1, 111, 124, 500, 300, 0),
(53, 'Frame.listdatabaseconnection', 1, 792, 45, 648, 310, 0),
(54, 'Frame.tabulka', 1, 70, 70, 500, 300, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wp_wysiwyg_file`
--

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
  `datetime` tinytext COLLATE utf8_czech_ci NOT NULL,
  `refs` tinytext COLLATE utf8_czech_ci NOT NULL,
  `place` tinytext COLLATE utf8_czech_ci NOT NULL,
  `notplayed` int(11) NOT NULL DEFAULT '0',
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
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`season`,`team`),
  KEY `season` (`season`),
  KEY `team` (`team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_player`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_project`
--

CREATE TABLE IF NOT EXISTS `w_sport_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `url` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_project`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_round`
--

CREATE TABLE IF NOT EXISTS `w_sport_round` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `season_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_round`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_season`
--

CREATE TABLE IF NOT EXISTS `w_sport_season` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_year` int(10) unsigned NOT NULL,
  `end_year` int(10) unsigned NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_season`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_stats`
--

CREATE TABLE IF NOT EXISTS `w_sport_stats` (
  `pid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL,
  `goals` tinyint(3) unsigned NOT NULL,
  `assists` tinyint(3) unsigned NOT NULL,
  `penalty` tinyint(3) unsigned NOT NULL,
  `shoots` tinyint(3) unsigned NOT NULL,
  `season` tinyint(3) unsigned NOT NULL,
  `table_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1',
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
-- Dumping data for table `w_sport_table`
--


-- --------------------------------------------------------

--
-- Table structure for table `w_sport_tables`
--

CREATE TABLE IF NOT EXISTS `w_sport_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_tables`
--


-- --------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `w_sport_team`
--

