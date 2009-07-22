--
-- Struktura tabulky `w_sport_match`
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
  `comment` mediumtext NOT NULL,
  `season` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`season`,`a_team`,`h_team`),
  KEY `h_team` (`h_team`),
  KEY `a_team` (`a_team`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `w_sport_match`
--

INSERT INTO `w_sport_match` (`id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `round`, `in_table`, `comment`, `season`) VALUES
(2, 1, 2, 11, 5, 32, 22, 3, 4, 0, 0, 1, 1, 'StrhujÃ­cÃ­ zÃ¡pas ve kterÃ©m nakonec favorit pÅ™ekvapil a vyhrÃ¡l', 5),
(3, 4, 3, 14, 16, 28, 35, 5, 7, 0, 0, 1, 1, '', 5),
(4, 4, 1, 8, 11, 29, 21, 2, 1, 0, 0, 2, 1, '', 5),
(5, 2, 3, 5, 16, 12, 37, 4, 1, 0, 0, 2, 1, '', 5),
(6, 1, 3, 7, 7, 21, 17, 1, 1, 0, 0, 3, 1, '', 5),
(7, 2, 4, 3, 11, 15, 25, 3, 1, 0, 0, 3, 1, '', 5);

-- --------------------------------------------------------

--
-- Struktura tabulky `w_sport_player`
--

CREATE TABLE IF NOT EXISTS `w_sport_player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `surname` tinytext NOT NULL,
  `birthyear` int(3) unsigned NOT NULL,
  `number` int(3) unsigned NOT NULL,
  `position` int(3) unsigned NOT NULL,
  `photo` tinytext NOT NULL,
  `season` int(10) unsigned NOT NULL,
  `team` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`season`,`team`),
  KEY `season` (`season`),
  KEY `team` (`team`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Vypisuji data pro tabulku `w_sport_player`
--

INSERT INTO `w_sport_player` (`id`, `name`, `surname`, `birthyear`, `number`, `position`, `photo`, `season`, `team`) VALUES
(11, 'Ondrej', 'Turek', 1987, 6, 2, '', 5, 2),
(12, 'Jan', 'DvoÅ™Ã¡k', 1975, 69, 2, '', 5, 2),
(13, 'Jakub', 'MalÃ½', 1990, 10, 3, '', 5, 2),
(14, 'Jan', 'BartÅ¯Å¡ek', 1984, 77, 3, '', 5, 3),
(10, 'Martin', 'BeneÅ¡', 1980, 44, 3, '', 5, 4),
(9, 'Jan', 'KovaÅ™Ã­k', 1965, 1, 3, '', 5, 3),
(8, 'Marek', 'FiÅ¡era', 1988, 79, 1, '', 5, 1),
(6, 'Miroslav', 'ZÅ¯na', 1986, 3, 3, '', 5, 1),
(7, 'MiloÅ¡', 'MatÄ›jka', 1970, 6, 2, '', 5, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `w_sport_season`
--

CREATE TABLE IF NOT EXISTS `w_sport_season` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_year` int(10) unsigned NOT NULL,
  `end_year` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `w_sport_season`
--

INSERT INTO `w_sport_season` (`id`, `start_year`, `end_year`) VALUES
(3, 2006, 2007),
(4, 2007, 2008),
(5, 2008, 2009);

-- --------------------------------------------------------

--
-- Struktura tabulky `w_sport_stats`
--

CREATE TABLE IF NOT EXISTS `w_sport_stats` (
  `pid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL,
  `goals` tinyint(3) unsigned NOT NULL,
  `assists` tinyint(3) unsigned NOT NULL,
  `penalty` tinyint(3) unsigned NOT NULL,
  `shoots` tinyint(3) unsigned NOT NULL,
  `season` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`pid`,`mid`,`season`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Vypisuji data pro tabulku `w_sport_stats`
--

INSERT INTO `w_sport_stats` (`pid`, `mid`, `goals`, `assists`, `penalty`, `shoots`, `season`) VALUES
(13, 2, 4, 0, 2, 8, 5),
(12, 2, 0, 4, 2, 5, 5),
(11, 2, 1, 1, 0, 9, 5),
(7, 2, 5, 6, 2, 14, 5),
(6, 2, 6, 4, 1, 18, 5),
(8, 2, 5, 1, 0, 22, 5);

-- --------------------------------------------------------

--
-- Struktura tabulky `w_sport_table`
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
  PRIMARY KEY (`team`,`season`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Vypisuji data pro tabulku `w_sport_table`
--

INSERT INTO `w_sport_table` (`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`) VALUES
(1, 3, 2, 1, 0, 29, 20, 7, 5),
(2, 3, 0, 0, 3, 13, 38, 0, 5),
(3, 3, 2, 1, 0, 39, 26, 7, 5),
(4, 3, 1, 0, 2, 33, 30, 3, 5);

-- --------------------------------------------------------

--
-- Struktura tabulky `w_sport_team`
--

CREATE TABLE IF NOT EXISTS `w_sport_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `logo` tinytext NOT NULL,
  `season` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`season`),
  KEY `season` (`season`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `w_sport_team`
--

INSERT INTO `w_sport_team` (`id`, `name`, `logo`, `season`) VALUES
(1, 'Papaya', '~/file.php?rid=30', 5),
(2, 'Papaya B', '~/file.php?rid=30', 5),
(3, 'VodiÄi', '', 5),
(4, 'Vipers', '', 5);
