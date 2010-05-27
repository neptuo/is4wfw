-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 30, 2009 at 02:08 PM
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id`, `line_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(6, 7),
(5, 1),
(7, 7),
(8, 7),
(9, 9),
(10, 9),
(11, 9),
(12, 9);

-- --------------------------------------------------------

--
-- Table structure for table `article_content`
--

DROP TABLE IF EXISTS `article_content`;
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

--
-- Dumping data for table `article_content`
--

INSERT INTO `article_content` (`article_id`, `language_id`, `name`, `head`, `content`, `author`, `timestamp`) VALUES
(1, 2, 'Ahoj', 'Nejaky docela pekny lipsum text, vsak to znate ne? Co se tak tady proste psava a tak dale. A zitra bude prset! :D', 'Nejaky docela pekny lipsum text, vsak to znate ne? Co se tak tady proste psava a tak dale. A zitra bude prset! :) ;)', 'Marek', 1256691598),
(2, 2, 'Ahoj 2', 'Lorem ipsum dolor sit amet consectetuer nec enim ipsum tempus Vestibulum. Quisque vestibulum id molestie nibh odio at justo velit pretium et. Pulvinar tristique eros Aliquam quis tellus dignissim et lacus orci convallis. Eros tellus eget sed justo porttitor laoreet sed lobortis tristique lobortis. Pellentesque nisl rhoncus et montes In dignissim.', 'Lorem ipsum dolor sit amet consectetuer nec enim ipsum tempus Vestibulum. Quisque vestibulum id molestie nibh odio at justo velit pretium et. Pulvinar tristique eros Aliquam quis tellus dignissim et lacus orci convallis. Eros tellus eget sed justo porttitor laoreet sed lobortis tristique lobortis. Pellentesque nisl rhoncus et montes In dignissim.', 'Marek', 1242118385),
(1, 3, 'Hello', 'Some nice lipsum or hello text! You surely know what I mean! And tomorrow will rain!', 'Some nice lipsum or hello text! You surely know what I mean! And tomorrow will rain!', 'Marek', 1241357652),
(2, 3, 'Hello 1', 'Natoque tempus lacus feugiat justo sed eu amet laoreet ipsum vitae. Velit elit a accumsan massa mus laoreet mus sodales sit malesuada. Platea In eget tincidunt massa Pellentesque mauris nec gravida auctor ut. Elit porttitor mattis semper tellus at consectetuer elit Phasellus natoque amet. Lorem nibh pretium ac leo sem elit non Phasellus condimentum metus. Cursus Proin massa et et laoreet rhoncus sapien ante lacinia mauris. Iaculis.', 'Natoque tempus lacus feugiat justo sed eu amet laoreet ipsum vitae. Velit elit a accumsan massa mus laoreet mus sodales sit malesuada. Platea In eget tincidunt massa Pellentesque mauris nec gravida auctor ut. Elit porttitor mattis semper tellus at consectetuer elit Phasellus natoque amet. Lorem nibh pretium ac leo sem elit non Phasellus condimentum metus. Cursus Proin massa et et laoreet rhoncus sapien ante lacinia mauris. Iaculis.a', 'Marek', 1241466767),
(3, 2, 'Ahoj 3', 'Pellentesque Aenean laoreet elit id habitasse consectetuer accumsan risus porttitor orci. Id consequat dolor eu Fusce velit tincidunt nibh in et ac. Dignissim dapibus egestas Fusce molestie nulla pede cursus in et consectetuer. Elit pretium semper libero ridiculus nec urna eu pellentesque lobortis Nulla. Vel Nullam Nam neque risus felis turpis vel sodales lorem Vivamus. Orci nibh quis tellus nulla tellus risus eleifend Nunc quis justo. Hac justo.', 'Pellentesque Aenean laoreet elit id habitasse consectetuer accumsan risus porttitor orci. Id consequat dolor eu Fusce velit tincidunt nibh in et ac. Dignissim dapibus egestas Fusce molestie nulla pede cursus in et consectetuer. Elit pretium semper libero ridiculus nec urna eu pellentesque lobortis Nulla. Vel Nullam Nam neque risus felis turpis vel sodales lorem Vivamus. Orci nibh quis tellus nulla tellus risus eleifend Nunc quis justo. Hac justo.', 'Marek', 1241472003),
(3, 3, 'Hello 3', 'Pellentesque Aenean laoreet elit id habitasse consectetuer accumsan risus porttitor orci. Id consequat dolor eu Fusce velit tincidunt nibh in et ac. Dignissim dapibus egestas Fusce molestie nulla pede cursus in et consectetuer. Elit pretium semper libero ridiculus nec urna eu pellentesque lobortis Nulla. Vel Nullam Nam neque risus felis turpis vel sodales lorem Vivamus. Orci nibh quis tellus nulla tellus risus eleifend Nunc quis justo. Hac justo.', 'Pellentesque Aenean laoreet elit id habitasse consectetuer accumsan risus porttitor orci. Id consequat dolor eu Fusce velit tincidunt nibh in et ac. Dignissim dapibus egestas Fusce molestie nulla pede cursus in et consectetuer. Elit pretium semper libero ridiculus nec urna eu pellentesque lobortis Nulla. Vel Nullam Nam neque risus felis turpis vel sodales lorem Vivamus. Orci nibh quis tellus nulla tellus risus eleifend Nunc quis justo. Hac justo.', 'Marek', 1241357870),
(8, 2, 'Ale uz opravdu nevim ...', 'Tohle je prosledni clanek ktery pridavam, jesi to nevyjde ... asi to vsechno zahodim!!!', 'sdl;f k;dsl kf;l fks;dl fka; lfka;lds fasd lf;askdfaoerf kaore aapoer porj galkfd galkfdg jarojep kfdl galkfdjglkroi 111', 'Mara', 1244729189),
(7, 3, 'En', '<strong>Hello!!</strong>', 'sfd d fdag sg klsajre lej ij poagj alrk jgari jga', 'Mara', 1244729238),
(9, 2, 'Aktualita 1', '<p>Papaya team vznikl v roce 2001 a to především ze zrady jednotlivých členů z ostatních teamů superligy. Největším zrádcem a strůjcem myšlenky založení nového superteamu byl Jan Hrdlička z tehdejší Verona V.I.P.</p>', '<p>Papaya team vznikl v roce 2001 a to především ze zrady jednotlivých členů z ostatních teamů superligy. Největším zrádcem a strůjcem myšlenky založení nového superteamu byl Jan Hrdlička z tehdejší Verona V.I.P. a k tomu všemu mu dopomáhal další zákeřník, zrádce a dokonce i kapitán mužstva Buldoků Lukáš Černý. Hrdla k sobě nalákal výborného playera Jirku Šnajdra a Lukys si vzal na posílení svého spolužáka ze základní školy basketbalistu Pavla Šmída. Dalším přínosem pro Papayu byl Petr Hozák, který přešel ze zkrachovalého teamu Kohoutů a obránce Honza Dvořák tehdy ještě volejbalový mág. Nakonec jsme našli v zapadlém koutu Lovosic oporu do branky nejmladšího a největšího (skoro 3 m vysokého), internetového magnáta Míru Vintricha.</p>\r\n\r\n<p>Název Papaya vznikl, když si dva velezrádci Hrdla a Lukys sedli po vyrovnaném squashovém utkání (Hrdla vyhrál 9:0, 9:1 a 9:0) na tonic s preclíkem ve Vinárně Johanka v Lovosicích. A po prudké výměně názorů a názvů oba usoudili, že Papaya team je prostě název ideální. </p>', 'Mára', 1246789268),
(1, 4, 'Ahoj', 'adadada\r\ndasd\r\nasdasdasdas\r\ndsadadadasd\r\nadasdad\r\nadasdasdads\r\nadsasdasd\r\nasdadasda\r\ndasdasdasda\r\nsdasdad', 'sadasdasd\r\nasd\r\nsad\r\nsa\r\ndsadasd\r\nsadas\r\ndsadasd\r\nsadsadasasdsad', 'Marek', 1242036865),
(6, 2, 'Ahoj', 'Ahoj svete!!!!', 'sadslkdfj alsd jgalkdg jalfd gjfaldk jf lk', 'Mara', 1244728779),
(6, 3, 'Hello1', 'as fdasf dsa fsd fad fds fas fas fas f s', 'salkfjsdlk jsd lkjdalkf jalk fjdaslk fjsd lfkjsd lkfj dsalkf j', 'Mara', 1244728956),
(6, 4, 'Hi', 'Ahoj lidicky!!!!', 'asfl ;dsak f;las kfa;sl kfas;l fakj haewu hfak jdkf hadskjf ', 'Mara', 1244729043),
(7, 2, 'Uz nevim ...', 'sdf ad fas ds fasd fas f', 'fads fasd gfdh sadf gar gad age agdg f r gadg ', 'Mara', 1244729087),
(5, 2, 'Ahoj 2', 'Id quis et eget pellentesque Aliquam risus Aliquam porta ac Maecenas. Commodo vitae faucibus auctor consequat ante consequat sed pulvinar dignissim egestas. Ut risus Curabitur et at nunc elit auctor senectus Curabitur Morbi. Non faucibus pede sociis lobortis laoreet et tincidunt vel dui lorem. Augue risus eu Vestibulum pharetra nibh dui fames Vestibulum at Nam. Cursus auctor malesuada egestas at Quisque Vestibulum parturient. ?????', 'Id quis et eget pellentesque Aliquam risus Aliquam porta ac Maecenas. Commodo vitae faucibus auctor consequat ante consequat sed pulvinar dignissim egestas. Ut risus Curabitur et at nunc elit auctor senectus Curabitur Morbi. Non faucibus pede sociis lobortis laoreet et tincidunt vel dui lorem. Augue risus eu Vestibulum pharetra nibh dui fames Vestibulum at Nam. Cursus auctor malesuada egestas at Quisque Vestibulum parturient.', 'Marek', 1241472010),
(5, 3, 'Hello 2', 'Id quis et eget pellentesque Aliquam risus Aliquam porta ac Maecenas. Commodo vitae faucibus auctor consequat ante consequat sed pulvinar dignissim egestas. Ut risus Curabitur et at nunc elit auctor senectus Curabitur Morbi. Non faucibus pede sociis lobortis laoreet et tincidunt vel dui lorem. Augue risus eu Vestibulum pharetra nibh dui fames Vestibulum at Nam. Cursus auctor malesuada egestas at Quisque Vestibulum parturient.', 'Id quis et eget pellentesque Aliquam risus Aliquam porta ac Maecenas. Commodo vitae faucibus auctor consequat ante consequat sed pulvinar dignissim egestas. Ut risus Curabitur et at nunc elit auctor senectus Curabitur Morbi. Non faucibus pede sociis lobortis laoreet et tincidunt vel dui lorem. Augue risus eu Vestibulum pharetra nibh dui fames Vestibulum at Nam. Cursus auctor malesuada egestas at Quisque Vestibulum parturient.', 'Marek', 1241358107),
(10, 2, 'Aktualita 2', '<p>Povzbuzeni výsledky z přípravných turnajů, těšili jsme se na zahájení naší první sezóny v Litoměřické florbalové Superlize CRESS. Jako nováček (ovšem jen papírově, protože nepřeberné zkušenosti jádra týmu</p>', '<p>Povzbuzeni výsledky z přípravných turnajů, těšili jsme se na zahájení naší první sezóny v Litoměřické florbalové Superlize CRESS. Jako nováček (ovšem jen papírově, protože nepřeberné zkušenosti jádra týmu – Lukyse, Hrdly, Jirky a Hoziho nás, podle Hrdlových skromných slov, řadily pomalu mezi adepty celkového vítězství) jsme byli zařazeni do skupiny Progress a strženi nezřízeným sebevědomím obou vůdců jsme si stanovili za cíl postup do skupiny ELITE. Základní skupinu jsme dokončili s jedinou porážkou na prvním místě (i když několikrát jsme utekli hrobníkovi z jeho pracovního náčiní jen díky gólům v posledních vteřinách zápasu). Postupný cíl naší cesty do ELITE byl tedy splněn a nás čekala baráž, ve které jsme za soupeře měli týmy VIP, Vipers (oba sestupující z ELITE) a Radolen, který skončil druhý ve skupině Progress.</p>\r\n\r\n<p>Po úvodním vítězství nad VIP (8:5) jsme však prohráli s Radolenem (9:15) a potom i v infarktovém zápase s Vipers (13:12), když jsme 5 min. před koncem vedli 12:9 a rozhodující gól na 13:12 dostali při power-play do prázdné branky.</p>', 'Mára', 1246789324),
(11, 2, 'Aktualita 3', '<p>Tím pro nás sezóna 2002/2003 skončila a zápasy play-off o mistra Supreligy CRESS jsme sledovali jen jako velmi zanícení diváci. Malou náplastí na nepodařenou baráž může být jen individuální úspěch Hrdly</p>', '<p>Tím pro nás sezóna 2002/2003 skončila a zápasy play-off o mistra Supreligy CRESS jsme sledovali jen jako velmi zanícení diváci. Malou náplastí na nepodařenou baráž může být jen individuální úspěch Hrdly, který vyhrál kanadské bodování skupiny Progress se 108 body a druhé místo „pouštěče“ Míry mezi brankáři s úspěšností 73%. I Míra mohl být první, ale v posledním zápase ve skupině s Lyrou Roudnice, jsme se kolektivně postarali o jeho pád na 2. místo (budiž nám slabou omluvou, že jsme v poli hráli jen ve 4 hráčích (soutěž se hraje na 3+1), takže střídající jedinec se na střídačce cítil poněkud osaměle, zatímco hráči Lyry museli při střídání stát, protože jich bylo tolik,že se na lavičku nevešli).</p>\r\n\r\n<p>Během soutěže došlo k jedné personální změně v teamu, když s námi přestal hrát Hozi, kterému časově náročné podnikání nedovolovalo účast na trénincích a zápasech. Během léta s námi začal trénovat Jirka „Kiler“ Hrdlička (takto bratr slovutného kanonýra Hrdly, to jedno „l“ v přezdívce není chyba, ale úmysl – to druhé si vyslouží až dá tolik gólů co jeho brácha spálí šancí, takže má co dělat !....</p>\r\n\r\n<p>Přes prázdniny jsme pak víceméně vypustili tréninky, a když jsme s nimi opět chtěli začít, přihnaly se nejstrašnější povodně v historii zemí Českých a vyplavily nám sportovní halu v Lovosicích. Za krvavý peníz jsme pak sháněli volné tělocvičny kde se dalo a potrénovali jsme na nadcházející sezónu. Naši formu, perně nabytou při trénincích, jsme pak mohli ověřit na dalším ročníku turnaje Radobýl Cup.</p>', 'Mára', 1246789371),
(12, 2, 'Aktualita 4', '<p>To už jsme věděli, že v sezóně 2002/03 budeme hrát, i přes nevydařenou baráž, ve skupině ELITE, protože se během léta rozpadly týmy Radolenu a Cobry. O to více jsme se těšili na nadcházející turnaj, kde jsme měli možnost změřit síly s Elitními soupeři a Hrdla vyhlásil útok na titul.</p>', '<p>To už jsme věděli, že v sezóně 2002/03 budeme hrát, i přes nevydařenou baráž, ve skupině ELITE, protože se během léta rozpadly týmy Radolenu a Cobry. O to více jsme se těšili na nadcházející turnaj, kde jsme měli možnost změřit síly s Elitními soupeři a Hrdla vyhlásil útok na titul.</p>\r\n\r\n<p>Pro účely turnaje jsme vytvořili alianci s týmem Sokola Libochovice (již v baráži za nás hrál a výborně, Honza Fictum). Celý turnaj jsme tak odehráli v kombinovaném složení pod hlavičkou Papaya teamu a vyneslo nám to 3. místo v turnaji a další porci sebevědomí do další sezóny.</p>\r\n\r\n<p>Protože se spolupráce s Libochovickými ďábly osvědčila, nominovali jsme pro následující sezonu to nejlepší co Libochovice nabízejí a to konkrétně kantorské duo Honza Fictum a Miloš Matějka. Tento tah se vyplatil a my byli schopni konkurovat i nejlepším týmům. Fíca, který tenkrát ještě dával góly a běhal a samozřejmě Miloš, který jako bek raději sežere balón, než by dostal gól. Stali jsme se týmem horní poloviny elitní skupiny a ostatní týmy se třásly strachy. I přes takto nabitou sestavu jsme nedosáhli na stupně vítězů a bylo třeba dalších změn abychom se z pátého místa vyhoupli na kýženou „bednu“, protože Hrdla vyhlásil útok na titul.</p>', 'Mára', 1246789419);

-- --------------------------------------------------------

--
-- Table structure for table `article_line`
--

DROP TABLE IF EXISTS `article_line`;
CREATE TABLE IF NOT EXISTS `article_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `article_line`
--

INSERT INTO `article_line` (`id`, `name`) VALUES
(1, 'News'),
(8, 'Neco'),
(7, 'PokusnaNaPravaNaDelete'),
(9, 'PAPAYA - News');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

--
-- Dumping data for table `article_line_right`
--

INSERT INTO `article_line_right` (`line_id`, `gid`, `type`) VALUES
(0, 2, 102),
(0, 2, 103),
(0, 3, 101),
(1, 2, 102),
(1, 2, 103),
(1, 3, 101),
(7, 1, 103),
(7, 2, 102),
(7, 3, 101),
(8, 2, 102),
(8, 2, 103),
(8, 3, 101),
(9, 2, 102),
(9, 2, 103),
(9, 3, 101);

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
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
-- Dumping data for table `content`
--

INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES
(2, 1, '<login:init group="web-admins" />', '', '<link rel="stylesheet" href="~/css/cms.css" type="text/css" />', '<web:content />'),
(3, 1, '', '', '', '<login:redirectWhenNotLogged pageId="4" />\r\n<login:redirectWhenLogged pageId="56" />'),
(4, 1, '', '', '<script type="text/javascript" src="~/js/domready.js"></script>\n<script type="text/javascript" src="~/js/formFieldEffect.js"></script>\n<script type="text/javascript" src="~/js/initLogin.js"></script>', '<div class="login-icons">\n  <img src="~/images/icons/service/rssmm_wfw.png" width="80" height="15" />\n  <img src="~/images/icons/service/ctags_php.png" width="80" height="15" />\n  <hr />\n  <img src="~/images/icons/service/valid_xhtml.png" width="80" height="15" />\n  <img src="~/images/icons/service/valid_css.png" width="80" height="15" />\n  <hr />\n  <img src="~/images/icons/service/firefox_copy2.gif" width="80" height="15" />\n  <img src="~/images/icons/service/opera.gif" width="80" height="15" />\n  <img src="~/images/icons/service/safari_copy2.gif" width="80" height="15" />\n  <hr />\n  <img src="~/images/icons/service/1024768.gif" width="80" height="15" />\n  <img src="~/images/icons/service/12801024.gif" width="80" height="15" />\n  <img src="~/images/icons/service/16001200.gif" width="80" height="15" />\n</div>\n<web:incTemplate browser="MSIE" templateId="34" />\n<div class="login">\n  <div class="login-head"></div>\n  <div class="login-in">\n    <login:form group="web-admins" pageId="56" autoLoginUserName="admin" autoLoginPassword="111111" />\n  </div>\n</div>'),
(5, 1, '<php:register tagPrefix="wp" classPath="php.libs.WebProject" />\n<login:init group="web-admins" />\n<wp:selectProject showMsg="false" useFrames="false" />', '<php:unregister tagPrefix="wp" />', '', '<div style="display: none">\n    <login:logout group="web-admins" pageId="4" />\n</div>\n<web:content />'),
(151, 1, 'tl start 11sdfsfsdfsf', 'tl end 22dsfsdfsfsdf', 'head 33dfsf', '<div class="neco">\n    neco v divu ;-)\n</div>\n\n<p>\n    Obsah napsany v IE ;)\n</p>\n<p>\n    Obsah psany pres cache editor ...\n</p>'),
(152, 1, '<php:register tagPrefix="pgng" classPath="php.libs.PageNG" />\n<php:register tagPrefix="webprj" classPath="php.libs.WebProject" />\n<login:init group="pageng-test" />', '<php:unregister tagPrefix="pgng" />\n<php:unregister tagPrefix="webprj" />', '', '<h2>Welcome to content editation:</h2>\n\n<login:logout pageId="153" group="pageng-test" />\n\n<web:setProperty prefix="pgng" name="language" value="1" />\n<web:setProperty prefix="webprj" name="selectedProject" value="18" />\n\n<hr />\n\n<pgng:searchFilter templateId="35" useFrames="false" showMsg="trie" />\n\n<hr />\n\n<table>\n    <tr>\n        <th>Id:</th>\n        <th>Name:</th>\n        <th>Actions:</th>\n    </tr>\n    <pgng:listPages templateId="36" rootPageId="0" webProjectId="webprj:selectedProject" langId="pgng:language" useFrames="false" showMsg="true" />\n</table>\n<br />\n<pgng:actionAddsub parentPageId="0" langId="pgng:language" type="button" detailPageId="154" />'),
(6, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showEditPage />\r\n\r\n<pg:showList editable="true" />'),
(7, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showEditFile />\r\n\r\n<pg:showFiles editable="true" />'),
(8, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<p><fl:showUploadForm /></p><p><fl:showNewDirectoryForm /></p><p><fl:showDirectory /></p>'),
(9, 1, '<php:register tagPrefix="user" classPath="php.libs.User" />', '<php:unregister tagPrefix="user" />', '', '<web:content />'),
(95, 1, '', '', '<script type="text/javascript" src="~/js/domready.js"></script>\r\n<script type="text/javascript" src="~/js/rxmlhttp.js"></script>', '<div class="all">\r\n  <div class="head">\r\n    <div class="corner"></div>\r\n    <div class="head-center">\r\n      <div class="head-image"></div>\r\n      <div id="loading" class="loading">\r\n        Načítám ....\r\n      </div>\r\n    </div>\r\n    <div class="clear"></div>\r\n  </div>\r\n  <div id="ajax-body" class="body">\r\n    <web:content />\r\n  </div>\r\n</div>'),
(96, 1, '', '', '', '<h1>Holla!</h1>'),
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
(47, 1, '<php:register tagPrefix="artc" classPath="php.libs.Article" />\r\n<php:register tagPrefix="cn" classPath="php.libs.Counter" />\r\n<cn:access id="1" every="day" />\r\n<web:pair property="dir-id" scope="session" />\r\n<log:write msg="Hello world." />', '<php:unregister tagPrefix="artc" />\r\n<php:unregister tagPrefix="cn" />', '', '<p>\r\n  <artc:showLine lineId="1" pageId="47" pageLangId="1" articleLangId="2" method="static" templateId="8" />\r\n</p>\r\n<p>\r\n  <artc:showDetail defaultArticleId="1" articleLangId="2" templateId="2" showError="false" />\r\n</p>\r\n<p>\r\n  <cn:show templateId="10" id="1" />\r\n</p>\r\n<p>\r\n  <img src="~/file.php?rid=10&width=400" width="400" height="640" alt="Rhonaaa ;)" title="Rhonaaa ;)" />\r\n</p>\r\n<p>\r\n  Last page update: <web:lastPageUpdate />\r\n</p>\r\n<p>\r\n  <web:yearsFrom year="2000" />\r\n</p>'),
(43, 1, '<php:register tagPrefix="artc" classPath="php.libs.Article" />', '<php:unregister tagPrefix="artc" />', '', '<artc:showRss lineId="1" articleLangId="2" pageId="47" lageLangId="1" method="static" />'),
(44, 1, '', '', '', '<web:content />'),
(45, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<pg:showTemplates detailPageId="46" />'),
(46, 1, '<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pg" />', '', '<p>\r\n  <a href="&web:page=45">Back template list ...</a>\r\n</p>\r\n\r\n<pg:editTemplate />'),
(56, 1, '<php:register tagPrefix="sys" classPath="php.libs.System" />', '<php:unregister tagPrefix="sys" />', '', '<div id="home-desktop" class="home-cover">\n    <strong>Kam dále?</strong>\n    <hr />\n    <web:menu parentId="5" inner="1" />\n    <hr />\n    <strong>TODO & Notes:</strong>\n    <sys:printNotes useFrames="false" showMsg="false" />\n</div>'),
(54, 1, '', '', '', '<web:incTemplate templateId="12" />'),
(53, 1, '', '', '', '<p>\r\n  <a href="&web:page=52">Groups edit</a>\r\n</p>\r\n<p>\r\n  <user:management />\r\n</p>'),
(94, 1, '', '', '', '<web:redirect path="http://mail.google.com/a/epapaya.cz" />'),
(91, 1, 'Pokus', 'Pokus', 'Pokus', 'Pokus'),
(90, 1, 'Pokus', 'Pokus', 'Pokus', 'Pokus'),
(89, 1, '', '', '', '<h1 class="error">Error 403</h1><p>Permission denied!</p>'),
(88, 1, '', '', '', '<h1 class="error">Error 404</h1><p>Requested resource doesn''t exists!</p>'),
(87, 1, '', '', '', '<h1 class="error">Some Error</h1><p>while processing request.</p>'),
(85, 1, '', '', '', '<form:form2 />'),
(83, 1, '<php:register tagPrefix="form" classPath="php.libs.Form" />', '<php:unregister tagPrefix="form" />', '', '<web:content />'),
(84, 1, '', '', '', '<form:form1 />'),
(75, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<web:content />'),
(74, 1, '', '', '', '<a href="&web:page=73">back</a>\r\n<hr />\r\n\r\n<fl:galleryDetail />'),
(73, 1, '<login:init group="test" />\r\n<web:pair property="dir-id" scope="request" />', '', '', '<web:incTemplate templateId="15" whenLogged="true" />\r\n<web:incTemplate templateId="16" whenNotLogged="true" />\r\n\r\n<hr />\r\n\r\n<fl:gallery method="static" pageId="74" defaultDirId="25" detailHeight="150" />\r\n\r\n<hr />\r\n\r\n<web:time />'),
(86, 1, '', '', '', '<web:content />'),
(71, 1, '', '', '', 'No cache used!'),
(66, 1, '', '', '', '<p>\r\n  <strong>Timestamp:</strong> <web:time />\r\n</p>\r\n<p>\r\n  <strong>Used cache:</strong> <web:content />\r\n</p>'),
(67, 1, '', '', '', '1 Hour cache time!'),
(68, 1, '', '', '', '1 Day cache time!'),
(69, 1, '', '', '', 'Unlimited cache time!'),
(70, 1, '', '', '', '1 Minute cache time!'),
(65, 1, '', '', '', '<p>\r\n<web:frame title="ahoj">V prvnim \r\nframu</web:frame>\r\n</p>\r\n<p>\r\n<web:frame open="true">Ve druhem framu</web:frame>\r\n</p>'),
(64, 1, '', '', '', 'Hi :)'),
(63, 1, '', '', '', '<h1>Vypis vsech viditelnych referenci</h1>\r\n<p>\r\n  <hp:references templateId="14" />\r\n</p>'),
(62, 1, '', '', '', '<h1>Vypis vsech viditelnych projekci</h1>\r\n<p>\r\n  <hp:projections templateId="13" />\r\n</p>'),
(60, 1, '<php:register tagPrefix="hp" classPath="php.libs.hp.Hotproject" />', '<php:unregister tagPrefix="hp" />', '', '<web:menu parentId="60" />\r\n<hr />\r\n<web:content />'),
(52, 1, '', '', '', '<p><a href="&web:page=53">Back to user manager ...</a></p>\r\n<p>\r\n  <user:newGroup />\r\n</p>\r\n<p>\r\n  <user:deleteGroup />\r\n</p>'),
(178, 1, '', '', '', ''),
(175, 1, '', '', '', '<pg:updateKeywords />'),
(176, 1, '', '', '', '<pg:showLanguages editable="true" />'),
(97, 1, '', '', '', '<h1>Historie</h1>\r\n<p>Papaya team vznikl v roce 2001 a to především ze zrady jednotlivých členů z ostatních teamů superligy. Největším zrádcem a strůjcem myšlenky založení nového superteamu byl Jan Hrdlička z tehdejší Verona V.I.P. a  k tomu všemu mu dopomáhal další zákeřník, zrádce a dokonce i kapitán mužstva Buldoků Lukáš Černý. Hrdla k sobě nalákal výborného playera Jirku Šnajdra a Lukys si vzal na posílení svého spolužáka ze základní školy basketbalistu Pavla Šmída. Dalším přínosem pro Papayu byl Petr Hozák, který přešel ze zkrachovalého teamu Kohoutů a obránce Honza Dvořák tehdy ještě volejbalový mág. Nakonec jsme našli v zapadlém koutu Lovosic oporu do branky nejmladšího a největšího (skoro 3 m vysokého), internetového magnáta Míru Vintricha.</p>\r\n<p>Název Papaya vznikl, když si dva velezrádci Hrdla a Lukys sedli po vyrovnaném squashovém utkání (Hrdla vyhrál 9:0, 9:1 a 9:0) na tonic s preclíkem ve Vinárně Johanka v Lovosicích. A po prudké výměně názorů a názvů oba usoudili, že Papaya team je prostě název ideální. </p>\r\n<p>Povzbuzeni výsledky z přípravných turnajů, těšili jsme se na zahájení naší první sezóny v Litoměřické florbalové Superlize CRESS. Jako nováček (ovšem jen papírově, protože nepřeberné zkušenosti jádra týmu – Lukyse, Hrdly, Jirky a  Hoziho nás, podle Hrdlových skromných slov, řadily pomalu mezi adepty celkového vítězství) jsme byli zařazeni do skupiny Progress a strženi nezřízeným sebevědomím obou vůdců jsme si stanovili za cíl postup do skupiny ELITE. Základní skupinu jsme dokončili s jedinou porážkou na prvním místě (i když několikrát jsme utekli hrobníkovi z jeho pracovního náčiní jen díky gólům v posledních vteřinách zápasu). Postupný cíl naší cesty do ELITE byl tedy splněn a nás čekala baráž, ve které jsme za soupeře měli týmy VIP, Vipers (oba sestupující z ELITE) a Radolen, který skončil druhý ve skupině Progress.</p>\r\n<p>Po úvodním vítězství nad VIP (8:5) jsme však prohráli s Radolenem (9:15) a potom i v infarktovém zápase s Vipers (13:12), když jsme 5 min. před koncem vedli 12:9 a rozhodující gól na 13:12 dostali při power-play do prázdné branky.</p>\r\n<p>Tím pro nás sezóna 2002/2003 skončila a zápasy play-off o mistra Supreligy CRESS jsme sledovali jen jako velmi zanícení diváci. Malou náplastí na nepodařenou baráž může být jen individuální úspěch Hrdly, který vyhrál kanadské bodování skupiny Progress se 108 body a druhé místo „pouštěče“ Míry mezi brankáři  s úspěšností 73%. I Míra mohl být první, ale v posledním zápase ve skupině s Lyrou Roudnice, jsme se kolektivně postarali o jeho pád na 2. místo  (budiž nám slabou omluvou, že jsme v poli hráli jen ve 4 hráčích (soutěž se hraje na 3+1), takže střídající jedinec se na střídačce cítil poněkud osaměle, zatímco hráči Lyry museli při střídání stát, protože jich bylo tolik,že se na lavičku nevešli).</p>\r\n<p>Během soutěže došlo k jedné personální změně v teamu, když s námi přestal hrát Hozi, kterému časově náročné podnikání nedovolovalo účast na trénincích a zápasech. Během léta s námi začal trénovat Jirka „Kiler“ Hrdlička (takto bratr slovutného kanonýra Hrdly, to jedno „l“ v přezdívce není chyba, ale úmysl – to druhé si vyslouží až dá tolik gólů co jeho brácha spálí šancí, takže má co dělat !....</p>\r\n<p>Přes prázdniny jsme pak víceméně vypustili tréninky, a když jsme s nimi opět chtěli začít, přihnaly se nejstrašnější povodně v historii zemí Českých a vyplavily nám sportovní halu v Lovosicích. Za krvavý peníz jsme pak sháněli volné tělocvičny kde se dalo a potrénovali jsme na nadcházející sezónu. Naši formu, perně nabytou při trénincích, jsme pak mohli ověřit na dalším ročníku turnaje Radobýl Cup.</p>\r\n<p>To už jsme věděli, že v sezóně 2002/03 budeme hrát, i přes nevydařenou baráž, ve skupině ELITE, protože se během léta rozpadly týmy Radolenu a Cobry.  O to více jsme se těšili na nadcházející turnaj, kde jsme měli možnost změřit síly s Elitními soupeři a Hrdla vyhlásil útok na titul.</p>\r\n<p>Pro účely turnaje jsme vytvořili alianci s týmem Sokola Libochovice (již v baráži za nás hrál a výborně, Honza Fictum). Celý turnaj jsme tak odehráli v kombinovaném složení pod hlavičkou Papaya teamu a vyneslo nám to 3. místo v turnaji a další porci sebevědomí do další sezóny. </p>\r\n<p>Protože se spolupráce s Libochovickými ďábly osvědčila, nominovali jsme pro následující sezonu to nejlepší co Libochovice nabízejí a to konkrétně kantorské duo Honza Fictum a Miloš Matějka. Tento tah se vyplatil a my byli schopni konkurovat i nejlepším týmům. Fíca, který tenkrát ještě dával góly a běhal a samozřejmě Miloš, který jako bek raději sežere balón, než by dostal gól. Stali jsme se týmem horní poloviny elitní skupiny a ostatní týmy se třásly strachy. I přes takto nabitou sestavu jsme nedosáhli na stupně vítězů a bylo třeba dalších změn abychom se z pátého místa vyhoupli na kýženou „bednu“, protože Hrdla vyhlásil útok na titul.</p>\r\n<p>A kam jinam sáhnout než do Libochovic. Jako farma se nám osvědčily – tak proč nepoužít Petra „Mrkvu“ Mrkvičku, který na loňském Radobýl Cupu chytal jako Hašek v Naganu. Mrkva nahradil pro sezónu 2003/2004 v brance Míru Vintricha, který měl studijní povinnosti. Takto posíleni jsme bojovali jako lvi a po krásném čtvrtfinále s Kerbem (2:0 na zápasy) a prohraném semíčku s Dominátory jsme se o třetí místo utkali s týmem Draps. Bohužel jsme nestačili ani na ně a tak jsme se medailové pozici přiblížili jen o stupínek. Nicméně konečné 4. místo nás nedeprimovalo a nabudilo do dalších bojů v příštím ročníku a Hrdla na další rok opět vyhlásil útok na titul.</p>\r\n<p>Proto před sezónou 2004/05 proběhla v našem týmu další gólmanská rošáda - vycházející hvězda libochovicka Marek Fišera nahradila Mrkvu, který se vrátil do týmu Libochovic, nyní již oficiálně zvaného Papaya team B a naše Áčko (mimochodem, jde jen o čistě rozlišovací označení) tak získalo i možnost brát si na hostování další libochovická esa – např. Mirečka Zůnu, Jirku Jiráska a další. Navíc jsme angažovali lovosického bijce a kanonýra, z Panterů zběhnuvšího Jirku „Elwise“ Haufa.</p>\r\n<p>Sezónu 2004/2005 jsme zahájili ve velkém stylu drtivým vítězstvím nad Vodiči a vše se zdálo být v pořádku. Po zahajovací sérii výher jsme se však v druhé půli soutěže dostali do útlumu a základní část jsme zakončili uprostřed tabulky. Ve čtvrtfinále nás čekal těžký a tradiční soupeř – Vodiči. Bohužel jsme jediný čtvrtfinálový zápas nezvládli a sezóna pro nás skončila na celkovém pátém místě. </p>\r\n<p>Na následující ročník 2005/06 jsme se chystali tradičně – povolali jsme nového gólmana a Hrdla vyhlásil útok na titul. </p>\r\n<p>Do brány jsme koupili reprezentační gólwomanku Jitku Mohrovou a začaly se dít věci. Jíťa chytala famózně a z nás se stal nejútočnější tým hrající systémem 0+3, protože jsme prostě nepotřebovali bránit. Drtili jsme jednoho soupeře za druhým (tedy skoro) a v této sezóně konečně dosáhli na stupně vítězů. Sice to byl ten stupeň nejnižší, ale teď nás již nic nezastaví a navíc Hrdla překvapivě vyhlásil na další ročník útok na titul.</p>'),
(98, 1, '<php:register tagPrefix="artc" classPath="php.libs.Article" />', '<php:unregister tagPrefix="artc" />', '', '<h1>Aktuality</h1>\r\n\r\n<artc:showLine templateId="18" lineId="9" articleLangId="2" />'),
(99, 1, '<php:register tagPrefix="sport" classPath="php.libs.Sport" />', '<php:unregister tagPrefix="sport" />', '', '<div class="dido">\r\n  <div class="results">\r\n    <sport:rounds templateId="23" seasonId="5" sorting="DESC" />\r\n  </div>\r\n  <div class="table">\r\n    <h1>Tabulka <sport:season seasonId="5" field="start_year" /> / <sport:season seasonId="5" field="end_year" /></h1>\r\n    <div class="table-in">\r\n      <table>\r\n        <sport:table templateId="20" seasonId="5" useFrames="false" tableId="2" />\r\n      </table>\r\n    </div>\r\n    <h1>Top 20 kan.bodování</h1>\r\n    <div class="table-in">\r\n      <table>\r\n        <sport:players tableId="1" templateId="25" sorting="desc" sortBy="season_goals" showGolmans="false" seasonId="5" limit="20" />\r\n      </table>\r\n    </div>\r\n    <h1>Top 5 gólmanů</h1>\r\n    <div class="table-in">\r\n      <table>\r\n        <sport:players tableId="1" templateId="27" sorting="desc" sortBy="season_percentage" showGolmans="true" seasonId="5" limit="5" />\r\n      </table>\r\n    </div>\r\n  </div>\r\n  <div class="clear"></div>\r\n</div>'),
(100, 1, '<php:register tagPrefix="sport" classPath="php.libs.Sport" />', '<php:unregister tagPrefix="sport" />', '', '<div class="players">\r\n  <h1>Hráči - <sport:team field="name" teamId="1" seasonId="5" /></h1>\r\n  <div class="a-team">\r\n    <sport:players templateId="28" teamId="1" sorting="asc" sortBy="number" seasonId="5" />\r\n  </div>\r\n  <h1>Hráči - <sport:team field="name" teamId="2" seasonId="5" /></h1>\r\n  <div class="b-team">\r\n    <sport:players templateId="28" teamId="2" sorting="asc" sortBy="number" seasonId="5" />\r\n  </div>\r\n</div>'),
(101, 1, '<php:register tagPrefix="gb" classPath="php.libs.Guestbook" />', '<php:unregister tagPrefix="gb" />', '', '<h1>Guestbook</h1>\r\n\r\n<gb:input guestbookId="1" />\r\n\r\n<hr />\r\n\r\n<gb:show guestbookId="1" />'),
(102, 1, '', '', '', '<h1>Sponzoři</h1>'),
(103, 1, '<php:register tagPrefix="cn" classPath="php.libs.Counter" />\r\n<cn:access id="2" every="day" />', '<php:unregister tagPrefix="cn" />', '', '<div class="left">\r\n  <div class="menu-cover">\r\n    <web:menu parentId="103" />\r\n  </div>\r\n  <div class="clear"></div>\r\n  <hr />\r\n  <div class="counter-cover">\r\n    <cn:show templateId="17" id="2" valueLength="4" />\r\n  </div>\r\n  <hr />\r\n  <div class="banners">\r\n    <a target="_blank" href="http://www.volny.cz/florbal.lfp"><img src="~/file.php?rid=40" width="150" height="50" alt="www.volny.cz/florbal.lfp" /></a>\r\n    <a target="_blank" href="http://www.kostalven.cz/"><img src="~/file.php?rid=39" width="150" height="50" alt="www.kostalven.cz" /></a>\r\n  </div>\r\n  <hr />\r\n  <div class="copyright">\r\n    a nejake copy rights ;)\r\n  </div>\r\n</div>\r\n<div class="content">\r\n  <web:content />\r\n</div>'),
(104, 1, '', '', '', '<script type="text/javascript">\r\n\r\n  if(window.location.href.indexOf(''#'') == -1) {\r\n    window.location.href = window.location.href + ''#/home'';\r\n  }\r\n\r\n</script>'),
(105, 1, '<php:register tagPrefix="sport" classPath="php.libs.Sport" />', '<php:unregister tagPrefix="sport" />', '', '<sport:selectSeason />\r\n<sport:selectTeam />\r\n<sport:selectTable />\r\n<web:content />'),
(106, 1, '', '', '', '<p>Select from menu above.</p>'),
(107, 1, '', '', '', '<p>\r\n  <sport:editSeasonForm />\r\n</p>\r\n<p>\r\n  <sport:editSeasons />\r\n</p>'),
(108, 1, '', '', '', '<p>\r\n  <sport:editTeamForm />\r\n</p>\r\n<p>\r\n  <sport:editTeams />\r\n</p>'),
(148, 1, '', '', '', '<web:menu parentId="147" inner="1" />'),
(109, 1, '', '', '', '<p>\r\n  <sport:editPlayerForm />\r\n</p>\r\n<p>\r\n  <sport:editPlayers />\r\n</p>'),
(147, 1, '<php:register tagPrefix="sys" classPath="php.libs.System" />', '<php:unregister tagPrefix="sys" />', '', '<web:content />'),
(110, 1, '', '', '', '<p>\r\n  <sport:editStatsForm />\r\n</p>\r\n<p>\r\n  <sport:editMatchForm />\r\n</p>\r\n<p>\r\n  <sport:editMatches />\r\n</p>'),
(111, 1, '', '', '', '<sport:table />'),
(112, 1, '<php:register tagPrefix="sport" classPath="php.libs.Sport" />', '<php:unregister tagPrefix="sport" />', '', '<h3>Testing sport!</h3>\r\n<hr />\r\n<web:menu parentId="112" />\r\n<hr />\r\n<web:content />'),
(113, 1, '', '', '', 'Index'),
(114, 1, '', '', '', '<table><sport:table templateId="20" seasonId="5" useFrames="false" /></table>'),
(115, 1, '', '', '', '<table>\r\n<sport:matches templateId="22" sorting="DESC" />\r\n</table>'),
(116, 1, '', '', '', '<sport:seasons templateId="26" sorting="DESC" />'),
(117, 1, '', '', '', '<table><sport:players templateId="25" sorting="desc" sortBy="season_goals" fromMatchId="4" showGolmans="false" seasonId="5" limit="3" /></table>'),
(118, 1, '<php:register tagPrefix="sport" classPath="php.libs.Sport" />\r\n<sport:setFromRequest />', '<php:unregister tagPrefix="sport" />', '', '<web:content />'),
(119, 1, '', '', '', '<div class="player-details">\r\n  <img src="<sport:player field="photo" />" />  \r\n</div>\r\n<div class="player-seasons">\r\n<table>\r\n  <tr>\r\n    <th>Sezóna</th>\r\n    <th>Z</th>\r\n    <th>G</th>\r\n    <th>A</th>\r\n    <th>KB</th>\r\n    <th>T</th>\r\n  </tr>\r\n  <sport:seasons templateId="30" sorting="desc" />\r\n  <tr class="summary">\r\n    <td>celkem: </td>\r\n    <td><sport:player field="total_matches" /></td>\r\n    <td><sport:player field="total_goals" /></td>\r\n    <td><sport:player field="total_assists" /></td>\r\n    <td><sport:player field="total_points" /></td>\r\n    <td><sport:player field="total_penalty" /></td>\r\n  </tr>\r\n</table>\r\n</div>\r\n<div class="clear"></div>'),
(120, 1, '', '', '', '<div class="player-details">\r\n  <img src="<sport:player field="photo" />" />  \r\n</div>\r\n<div class="player-seasons">\r\n<table>\r\n  <tr>\r\n    <th>Sezóna</th>\r\n    <th>Z</th>\r\n    <th>S</th>\r\n    <th>G</th>\r\n    <th>Pr</th>\r\n    <th>Ús</th>\r\n    <th>A</th>\r\n    <th>T</th>\r\n  </tr>\r\n  <sport:seasons templateId="31" sorting="desc" />\r\n  <tr class="summary">\r\n    <td>celkem: </td>\r\n    <td><sport:player field="total_matches" /></td>\r\n    <td><sport:player field="total_shoots" /></td>\r\n    <td><sport:player field="total_goals" /></td>\r\n    <td><sport:player field="total_average" /></td>\r\n    <td><sport:player field="total_percentage" /></td>\r\n    <td><sport:player field="total_assists" /></td>\r\n    <td><sport:player field="total_penalty" /></td>\r\n  </tr>\r\n</table>\r\n</div>\r\n<div class="clear"></div>'),
(121, 1, '', '', '', '<web:content />'),
(122, 1, 'ahoj="svete"', '', '', 'Ahoj svete!'),
(123, 1, '', '', '', '<web:content />'),
(124, 1, '', '', '', '<p>\r\n  Hello world! <a href="&web:page=124">This page</a>\r\n</p>\r\n<p>\r\n  <img src="~/file.php?rid=45&width=200" />\r\n</p>'),
(96, 2, '', '', '', '<h1>Cus bus!</h1>'),
(125, 1, '<php:register tagPrefix="hint" classPath="php.libs.Hint" />', '<php:unregister tagPrefix="hint" />', '', '<web:content />'),
(126, 1, '', '', '', '<hint:selectLib />\r\n\r\n<hint:lib classPath="hint:classPath" />'),
(127, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<p>\n  <fl:showUploadForm dirId="30" useRights="false" useFrames="false" />\n</p>\n<p>\n  <fl:gallery dirId="30" lightbox="true" lightTitle="true" lightHeight="600" lightId="1" />\n</p>'),
(121, 2, '', '', '', '<h1>Ahoj ;)</h1>'),
(128, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<fl:get />'),
(129, 1, '', '', '', '<web:redirectTo ip="23.23.23.33,127.0.0.1" pageId="130" />'),
(130, 1, '', '', '', 'Hello ;)'),
(131, 1, '', '', '', '<web:menu parentId="131" />\n\n<web:content />'),
(132, 1, '', '', '', '<web:content />'),
(133, 1, '', '', '', '<p><strong>Hello World ;-)</strong></p>'),
(134, 1, '', '', '', ''),
(135, 1, '', '', '', ''),
(136, 1, '', '', '', ''),
(137, 1, '', '', '', '<web:content />'),
(138, 1, '', '', '', ''),
(139, 1, '', '', '', ''),
(140, 1, '', '', '', ''),
(141, 1, '', '', '', '<web:content />'),
(142, 1, '', '', '', ''),
(143, 1, '', '', '', 'dfsgfdgdgdfgf'),
(145, 1, '', '', '', ''),
(146, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<fl:gallery dirId="24" lightbox="true" useDirectLink="true" />'),
(149, 1, '', '', '', '<sys:manageProperties />'),
(150, 1, '<login:init group="web-admins" />', '', '<link rel="stylesheet" href="~/css/editor.css" type="text/css" />\n<link rel="stylesheet" href="~/css/edit-area.css" type="text/css" />\n<link rel="stylesheet" href="~/css/window.css" type="text/css" />\n<link rel="stylesheet" href="~/css/jquery-autocomplete.css" type="text/css" />\n<link rel="stylesheet" href="~/css/jquery-wysiwyg.css" type="text/css" />\n<link rel="stylesheet" href="~/css/demo_table.css" type="text/css" />\n<script type="text/javascript" src="~/edit_area/edit_area_full.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-autocomplete-pack.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-blockui.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-dataTables-min.js"></script>\n<script type="text/javascript" src="~/js/jquery/jquery-wysiwyg.js"></script>\n<script type="text/javascript" src="~/js/functions.js"></script>\n<script type="text/javascript" src="~/js/window.js"></script>\n<script type="text/javascript" src="~/js/domready.js"></script>\n<script type="text/javascript" src="~/js/rxmlhttp.js"></script>\n<script type="text/javascript" src="~/js/links.js"></script>\n<script type="text/javascript" src="~/js/processform.js"></script>\n<script type="text/javascript" src="~/js/domready.js"></script>\n<script type="text/javascript" src="~/js/Closer.js"></script>\n<script type="text/javascript" src="~/js/Confirm.js"></script>\n<script type="text/javascript" src="~/js/Editor.js"></script>\n<script type="text/javascript" src="~/js/FileName.js"></script>\n<script type="text/javascript" src="~/js/CountDown.js"></script>\n<script type="text/javascript" src="~/js/formFieldEffect.js"></script>\n<script type="text/javascript" src="~/js/init.js"></script>\n<script type="text/javascript" src="~/tiny-mce/tiny_mce.js"></script>\n<script type="text/javascript" src="~/scripts/js/initTiny.js"></script>', '<div class="cms">\n  <div id="cms-head" class="head">\n    <login:logout group="web-admins" pageId="4" />\n    <div id="logon-count-down" class="logon-count-down">\n      <div class="count-down-cover">\n        <span class="count-down-label">Login session <br/>expires in: </span>\n        <span id="count-down-counter" class="count-down-counter"><web:systemPropertyValue name="Login.session" /></span>\n      </div>\n    </div>\n    <login:info />\n    <php:register tagPrefix="wp" classPath="php.libs.WebProject" />\n    <wp:selectProject showMsg="false" useFrames="false" />\n    <php:unregister tagPrefix="wp" />\n    <div class="web-version">\n      <div class="label">CMS version</div>\n      <div class="value">\n        <web:cmsVersion />\n      </div>\n    </div>\n    <div class="web-version">\n      <div class="label">Web version</div>\n      <div class="value">\n        <web:version />\n      </div>\n    </div>\n    <div id="loading" class="web-version loading">\n      Loading ...\n    </div>\n    <div id="cms-menus">\n        <div class="cms-menu">\n        <span class="menu-root"><a href="&web:page=56">Web</a></span>\n          <web:menu parentId="5" inner="1" />\n      </div>\n        <div class="cms-menu cms-menu-2">\n          <span class="menu-root"><a href="&web:page=105">Floorball</a></span>\n        <web:menu parentId="105" inner="1" />\n        </div>\n        <div class="cms-menu cms-menu-3">\n          <span class="menu-root"><a href="&web:page=125">Hint</a></span>\n        </div>\n        <div class="cms-menu cms-menu-4">\n          <span class="menu-root"><a href="&web:page=148">System setup</a></span>\n          <web:menu parentId="147" inner="1" />\n        </div>\n        <div class="cms-menu cms-menu-5">\n          <span class="menu-root"><a href="&web:page=23">Web settings</a></span>\n          <web:menu parentId="23" inner="1" />\n        </div>\n    </div>\n  </div>\n  <div class="dock-bar">\n    <div class="dock-in">\n      <div id="dock-left" class="dock-left">\n      </div>\n      <div id="dock" class="dock-mid">\n      </div>\n      <div id="dock-right" class="dock-right">\n        <div id="web-ajax-log-cover" class="web-ajax-log-cover">\n\n        </div>\n        <div id="clock" class="clock">\n          <div id="hours" class="clock-hours">\n          --\n          </div>:<div id="minutes" class="clock-minutes">\n          --\n          </div>:<div id="seconds" class="clock-seconds">\n          --\n          </div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div id="cms-body" class="body">\n    <web:content />\n  </div>\n</div>'),
(153, 1, '<login:init group="pageng-test" />', '', '', '<login:form group="pageng-test" pageId="152" />'),
(156, 1, '', '', '', 'sdfsdf\nsfd\nsdfsdfdsf\nsdfsd\nfds\nfsdf\nsdfsdf\n  ++'),
(154, 1, '<php:register tagPrefix="pgng" classPath="php.libs.PageNG" />\n<php:register tagPrefix="webprj" classPath="php.libs.WebProject" />\n<login:init group="pageng-test" />\n<php:register tagPrefix="pg" classPath="php.libs.Page" />', '<php:unregister tagPrefix="pgng" />\n<php:unregister tagPrefix="webprj" />\n<php:unregister tagPrefix="pg" />', '', '<strong>Editation of page: <pgng:name pageId="pgng:actionPageId" langId="pgng:language" type="value" /></strong>\n<hr />\n<pgng:detailBefore />\n\n<pg:showEditPage />\n\n<pgng:detailAfter backPageId="152" />'),
(155, 1, '<php:register tagPrefix="pgng" classPath="php.libs.PageNG" />', '<php:unregister tagPrefix="pgng" />', '', '<web:setProperty prefix="pgng" name="language" value="1" />\n\n<web:getProperty name="pgng:language" />'),
(157, 1, '<login:init group="pageng-test" />', '', '', '<login:form group="pageng-test" pageId="158" />'),
(158, 1, '<php:register tagPrefix="pgng" classPath="php.libs.PageNG" />\n<php:register tagPrefix="pg" classPath="php.libs.Page" />\n<php:register tagPrefix="webprj" classPath="php.libs.WebProject" />\n<login:init group="pageng-test" />', '<php:unregister tagPrefix="pgng" />\n<php:unregister tagPrefix="pg" />\n<php:unregister tagPrefix="webprj" />', '<script type="text/javascript" src="~/tiny-mce/tiny_mce.js"></script>', '<h2>Welcome to content editation:</h2>\n\n<login:logout pageId="157" group="pageng-test" />\n\n<web:setProperty prefix="pgng" name="language" value="1" />\n<web:setProperty prefix="webprj" name="selectedProject" value="18" />\n<hr />\n\n<pg:showEditPage editable="true" />\n\n<hr />\n\n<pg:showList editable="true" />'),
(166, 2, '', '', '', '<h1>Hello world ;)</h1>\r\n<p>Lorem ipsum dolor sit amet consectetuer laoreet et Pellentesque fringilla risus. Eleifend dis urna sem lacinia cursus ligula nec ac id ut. Wisi Nunc ut nascetur Donec eleifend morbi urna congue justo pellentesque. Donec consectetuer Nunc Vivamus in pellentesque cursus ridiculus euismod quam interdum. In elit tellus Aenean non interdum facilisis vestibulum nonummy ullamcorper.<br /><br />Vestibulum et quis id cursus eros semper parturient elit sit arcu. Neque natoque massa interdum interdum tincidunt nibh ac amet tincidunt arcu. Nunc eu nec Nulla porta Quisque Curabitur et fermentum In pretium. Commodo commodo odio elit pellentesque et In Aenean Phasellus montes Morbi. Donec pede consectetuer ligula consequat Aliquam Nam tellus ipsum at id. Auctor orci Proin interdum pharetra.<br /><br />Consequat hendrerit ac feugiat gravida ligula nonummy pretium Mauris elit facilisi. Quisque dignissim Sed scelerisque consequat feugiat diam ut Aliquam Sed congue. Sed orci gravida tincidunt Suspendisse nec justo id justo auctor nulla. At Nunc Sed vitae wisi ut nunc pellentesque odio lobortis nibh. Risus nunc eu condimentum sagittis ante malesuada ante parturient vitae.<br /><br />Amet sit at urna Morbi libero non malesuada vel pretium Phasellus. Tincidunt Pellentesque Ut porttitor justo metus Mauris semper Aenean consequat consequat. Ligula felis et interdum Sed sit ultrices pede sed ut mauris. Elit morbi eu urna libero quam Sed pretium cursus nec eu. Amet nulla quis Pellentesque at ipsum pretium et eros Lorem Pellentesque. Convallis lacinia senectus In sed egestas nisl dignissim.</p>'),
(167, 2, '', '', '', '<h1>Test&iacute;&iacute;k ;)</h1>'),
(168, 1, '', '', '', '<fl:gallery dirId="51" pageId="169" showSubDirs="true" />'),
(169, 1, '', '', '<script type="text/javascript" src="~/js/domready.js"></script>', '<fl:gallery dirId="51" showSubDirs="true" pageId="169" />\n<hr />\n<div id="gallery-content">\n    <fl:gallery detailWidth="600" />\n</div>'),
(170, 1, '<php:register tagPrefix="fl" classPath="php.libs.File" />', '<php:unregister tagPrefix="fl" />', '', '<web:content />'),
(171, 1, 'Test', 'Test', 'Test', 'Test1'),
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
  `ip` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL,
  PRIMARY KEY (`counter_id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `counter`
--

INSERT INTO `counter` (`ip`, `timestamp`, `count`, `counter_id`) VALUES
('127.0.0.1', 1259261235, 76, 1),
('127.0.0.1', 1260991706, 26, 2);

-- --------------------------------------------------------

--
-- Table structure for table `directory`
--

DROP TABLE IF EXISTS `directory`;
CREATE TABLE IF NOT EXISTS `directory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `url` tinytext COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=61 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `title` tinytext COLLATE latin1_general_ci NOT NULL,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=170 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

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
  `comp_name` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_person` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_email` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_phone` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_address` tinytext COLLATE latin1_general_ci NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `door_type` tinyint(4) NOT NULL,
  `cover` tinyint(4) NOT NULL,
  `fill_in` tinyint(4) NOT NULL,
  `comment` text COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `ip` varchar(16) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `form_order1`
--

INSERT INTO `form_order1` (`id`, `comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `door_type`, `cover`, `fill_in`, `comment`, `timestamp`, `ip`) VALUES
(12, 'aaa', 'aaa', 'a', 'a', 'a', 0, 0, 1, 1, 1, 'sdfdsfsd', 0, ''),
(11, 'sadasd', 'sadasd', 'asdasd', 'asdads', 'asdasd', 7686868, 768768, 2, 1, 1, '67678rth gfh gfh fg h gf', 0, ''),
(13, 'ojlkj', 'lk', 'jlkj', 'lkjjlk', 'jlkjlk', 100, 100, 1, 1, 1, '', 1252587707, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `form_order2`
--

DROP TABLE IF EXISTS `form_order2`;
CREATE TABLE IF NOT EXISTS `form_order2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comp_name` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_person` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_email` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_phone` tinytext COLLATE latin1_general_ci NOT NULL,
  `cont_address` tinytext COLLATE latin1_general_ci NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `fixture` tinyint(4) NOT NULL,
  `draught` tinyint(11) NOT NULL,
  `transit` tinyint(11) NOT NULL,
  `heating` tinyint(11) NOT NULL,
  `gripping_1` tinyint(11) NOT NULL,
  `gripping_2` tinyint(11) NOT NULL,
  `comment` text COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `ip` varchar(16) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `form_order2`
--

INSERT INTO `form_order2` (`id`, `comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `fixture`, `draught`, `transit`, `heating`, `gripping_1`, `gripping_2`, `comment`, `timestamp`, `ip`) VALUES
(7, 'asdsad', 'sadasd', 'asdasdasd', 'asdsadas', 'dasdasd', 2324, 324234, 2, 2, 34, 3, 2, 3, '324234234234ewr ewf dsf dsf dsf sd f', 0, ''),
(8, 'dasdas', 'sdadsas', 'dasdas', 'dsadasd', 'dsadsade', 65465, 65465, 2, 3, 60, 3, 3, 2, 'dsfsdfsdfsdfsdfsdfsd fsd fds fsd fsd', 0, ''),
(9, 'asdads', 'ghjg', 'hj', 'ghj', 'ghj', 0, 0, 1, 1, 20, 1, 1, 1, 'dsfdsf', 0, ''),
(10, 'sadasd', 'sadasd', 'sdsadsa', 'asdsadas', 'd', 111, 1111, 1, 1, 13, 1, 1, 1, 'safasdsad', 0, ''),
(11, 'asdsad', 'sad', 'asdasdasdd', 'd', 'asdsada', 1111, 11111, 1, 1, 63, 1, 1, 1, 'sadasdad', 0, ''),
(12, 'jkhkj', 'hkjh', 'kjh', 'kjhkj', 'hkj', 100, 100, 1, 1, 0, 1, 1, 1, '', 1252587414, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_gid` int(11) NOT NULL DEFAULT '1',
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`gid`, `parent_gid`, `name`, `value`) VALUES
(1, 0, 'admins', 1),
(2, 1, 'web-admins', 50),
(3, 2, 'web', 254),
(10, 6, 'test', 61),
(6, 1, 'web-projects', 60),
(11, 6, 'GalerieUpload', 61),
(12, 6, 'pageng-test', 61);

-- --------------------------------------------------------

--
-- Table structure for table `guestbook`
--

DROP TABLE IF EXISTS `guestbook`;
CREATE TABLE IF NOT EXISTS `guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `content` text COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `guestbook_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `guestbook`
--

INSERT INTO `guestbook` (`id`, `parent_id`, `name`, `content`, `timestamp`, `guestbook_id`) VALUES
(2, 0, 'Mára', 'Lorem ipsum dolor sit amet consectetuer elit molestie egestas massa Aenean. Tristique Aliquam rutrum Sed tempor iaculis Aenean velit euismod platea Vestibulum. In accumsan at convallis sed et neque dolor nec ac turpis. Nonummy malesuada Morbi congue Nulla urna ut interdum semper congue convallis. Id Morbi nunc tempus condimentum massa ut molestie vel eget elit. Cursus id.', 1247951540, 1),
(3, 0, 'Mára', 'Lorem ipsum dolor sit amet consectetuer elit molestie egestas massa Aenean. Tristique Aliquam rutrum Sed tempor iaculis Aenean velit euismod platea Vestibulum. In accumsan at convallis sed et neque dolor nec ac turpis. Nonummy malesuada Morbi congue Nulla urna ut interdum semper congue convallis. Id Morbi nunc tempus condimentum massa ut molestie vel eget elit. Cursus id.\r\n', 1248103893, 1),
(4, 0, 'Mára', 'Lorem ipsum dolor sit amet consectetuer elit molestie egestas massa Aenean. Tristique Aliquam rutrum Sed tempor iaculis Aenean velit euismod platea Vestibulum. In accumsan at convallis sed et neque dolor nec ac turpis. Nonummy malesuada Morbi congue Nulla urna ut interdum semper congue convallis. Id Morbi nunc tempus condimentum massa ut molestie vel eget elit. Cursus id.', 1248103909, 1),
(5, 0, 'Mára', 'Lorem ipsum dolor sit amet consectetuer elit molestie egestas massa Aenean. Tristique Aliquam rutrum Sed tempor iaculis Aenean velit euismod platea Vestibulum. In accumsan at convallis sed et neque dolor nec ac turpis. Nonummy malesuada Morbi congue Nulla urna ut interdum semper congue convallis. Id Morbi nunc tempus condimentum massa ut molestie vel eget elit. Cursus id.', 1248103917, 1);

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
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
  `cachetime` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES
(63, 1, 'References', 1, 'references', 1, 63, 1, '', 1243009014, -1),
(2, 1, 'CMS', 1, '', 0, 43, 1, '', 1248480300, -1),
(3, 1, 'Index', 0, '', 0, 3, 1, '', 1256931278, -1),
(4, 1, 'Login', 1, 'login', 0, 4, 1, '', 1259498355, -1),
(5, 1, 'in', 0, '', 1, 5, 1, '', 1256686914, -1),
(6, 1, 'Page Manager', 1, 'page-manager', 1, 8, 1, '', 1243352751, -1),
(7, 1, 'Text File Manager', 1, 'text-file-manager', 1, 16, 1, '', 1255186798, -1),
(8, 1, 'File Manager', 1, 'file-manager', 1, 23, 1, '', 1244011043, -1),
(9, 1, 'User Manager', 1, 'user-manager', 1, 50, 1, '', 1242245797, -1),
(65, 1, 'Frames', 1, 'frames', 0, 65, 1, '', 1243352545, -1),
(16, 1, 'Article Manager', 1, 'article-manager', 1, 25, 1, '', 1241358620, -1),
(17, 1, 'Guestbook Manager', 1, 'guestbook-manager', 1, 44, 1, '', 1234282979, -1),
(23, 1, 'Web Settings', 1, 'web-settings', 0, 105, 1, '', 1261013526, -1),
(25, 1, 'Web Project Manager', 1, 'web-project-manager', 1, 56, 1, '', 1241994270, -1),
(26, 1, 'List', 1, '', 0, 26, 1, '', 1241950546, -1),
(27, 1, 'Edit', 1, 'edit', 0, 27, 1, '', 1241950560, -1),
(28, 1, 'Select', 1, 'select', 0, 28, 1, '', 1241310063, -1),
(39, 1, 'Lines', 1, 'lines', 0, 40, 1, '', 1241994208, -1),
(47, 1, 'Homepage', 1, '', 0, 2, 1, '', 1245430860, -1),
(95, 1, 'Root Template', 0, '', 0, 95, 1, '', 1246747050, -1),
(45, 1, 'List', 1, '', 0, 45, 1, '', 1241517092, -1),
(46, 1, 'Edit', 1, 'edit', 0, 46, 1, '', 1241519980, -1),
(43, 1, 'RSS', 1, 'rss', 0, 47, 1, '', 1241635125, -1),
(44, 1, 'Template Manager', 1, 'template-manager', 1, 17, 1, '', 1241516140, -1),
(40, 1, 'List', 1, '', 0, 39, 1, '', 1241464605, -1),
(41, 1, 'Edit Article', 1, 'edit-article', 0, 41, 1, '', 1241387815, -1),
(42, 1, 'Edit Line', 1, 'edit-line', 0, 42, 1, '', 1241369340, -1),
(56, 1, 'Home', 1, '', 0, 7, 1, '', 1261172872, -1),
(177, 1, 'Show & Truncate log', 1, 'show-and-truncate-log', 1, 177, 1, '', 1261013385, -1),
(178, 1, 'Index', 1, '', 0, 178, 1, '', 1261013427, -1),
(60, 1, 'Hot project', 1, 'hp', 0, 60, 1, '', 1247040077, -1),
(62, 1, 'Projections', 1, 'projections', 1, 62, 1, '', 1243009202, -1),
(52, 1, 'Groups', 1, 'groups', 0, 53, 1, '', 1242427728, -1),
(53, 1, 'Users', 1, '', 0, 52, 1, '', 1242245779, -1),
(54, 1, 'Include Template', 1, 'include-template', 0, 55, 1, '', 1242246291, -1),
(64, 1, 'Index', 1, '', 0, 64, 1, '', 1243009131, -1),
(66, 1, 'Caching', 1, 'caching', 0, 66, 1, '', 1243854004, 60),
(67, 1, '1 Hour', 1, '1hour', 0, 69, 1, '', 1243852892, 3600),
(68, 1, '1 Day', 1, '1day', 0, 70, 1, '', 1243852916, 86400),
(69, 1, 'Unlimited', 1, 'unlimited', 0, 71, 1, '', 1243852949, 0),
(70, 1, '1 Minute', 1, '1minute', 0, 68, 1, '', 1245426390, 60),
(71, 1, 'NO cache', 1, '', 0, 67, 1, '', 1243853072, -1),
(86, 1, 'Error', 1, 'err', 0, 86, 1, '', 1244819446, -1),
(73, 1, 'Gallery Pair', 1, 'pair', 0, 73, 1, '', 1244011554, -1),
(74, 1, 'Gallery Detail', 1, 'detail', 0, 74, 1, '', 1244011541, -1),
(75, 1, 'Gallery', 1, 'gallery', 0, 75, 1, '', 1244488575, -1),
(175, 1, 'Keywords', 1, 'keywords', 1, 175, 1, '', 1261013318, -1),
(176, 1, 'Languages', 1, 'languages', 1, 176, 1, '', 1261013348, -1),
(174, 1, 'Url cache', 1, 'url-cache', 1, 174, 1, '', 1261013053, -1),
(173, 1, 'Page Manager - Edit only', 1, 'page-manager-edit-only', 0, 9, 1, '', 1260630356, -1),
(84, 1, 'obj1', 1, 'obj1', 0, 84, 1, '', 1244481082, -1),
(83, 1, 'PP', 1, 'pp', 0, 83, 1, '', 1244481062, -1),
(172, 1, 'System notes', 1, 'system-notes', 1, 172, 1, '', 1259345724, -1),
(85, 1, 'obj2', 1, 'obj2', 0, 85, 1, '', 1244481101, 0),
(87, 1, 'All Errors', 1, '', 0, 87, 1, '', 1244819564, -1),
(88, 1, 'Error 404', 1, '404', 0, 88, 1, '', 1244819573, -1),
(89, 1, 'Error 403', 1, '403', 0, 89, 1, '', 1244819602, -1),
(90, 1, 'Pokus', 1, 'pokus', 0, 90, 1, '', 1244826302, -1),
(91, 1, 'Pokus', 1, 'pokus', 0, 91, 1, 'Pokus', 1244826446, 0),
(96, 1, 'Name testing', 1, 'SomeNameOfSomePage', 0, 96, 1, '', 1246721478, -1),
(94, 1, 'Redirect', 1, 'redirect', 0, 94, 1, '', 1245146318, -1),
(97, 1, 'Home', 1, 'home', 1, 97, 1, '', 1246788381, -1),
(98, 1, 'Aktuality', 1, 'aktuality', 1, 98, 1, '', 1246789762, -1),
(99, 1, 'Dido liga', 1, 'dido-liga', 1, 99, 1, '', 1255189015, -1),
(100, 1, 'Hráči', 1, 'hraci', 1, 100, 1, '', 1248070532, -1),
(101, 1, 'Guestbook', 1, 'guestbook', 1, 101, 1, '', 1247951257, -1),
(102, 1, 'Sponzoři', 1, 'sponzori', 1, 102, 1, '', 1246748631, -1),
(103, 1, 'Inner Template', 0, '', 0, 103, 1, '', 1247921558, -1),
(104, 1, 'Blank', 0, '', 0, 104, 1, '', 1246789026, -1),
(105, 1, 'Sport', 1, 'sport', 0, 125, 1, '', 1254921169, -1),
(106, 1, 'Index', 1, '', 0, 106, 1, '', 1247405260, -1),
(107, 1, 'Seasons', 1, 'seasons', 1, 107, 1, '', 1247855669, -1),
(108, 1, 'Teams', 1, 'teams', 1, 108, 1, '', 1247855678, -1),
(109, 1, 'Players', 1, 'players', 1, 109, 1, '', 1247855688, -1),
(110, 1, 'Matches', 1, 'matches', 1, 110, 1, '', 1247855700, -1),
(111, 1, 'Table', 1, 'table', 1, 111, 1, '', 1247855709, -1),
(112, 1, 'Testing', 1, 'Testing', 0, 112, 1, '', 1247638153, -1),
(113, 1, 'Index', 1, '', 1, 113, 1, '', 1247638165, -1),
(114, 1, 'Table', 1, 'Table', 1, 114, 1, '', 1247917968, -1),
(115, 1, 'Matches', 1, 'Matches', 1, 115, 1, '', 1247666302, -1),
(116, 1, 'Matches in rounds', 1, 'MatchesInRounds', 1, 116, 1, '', 1247858753, -1),
(117, 1, 'Players', 1, 'Players', 1, 117, 1, '', 1247921597, -1),
(118, 1, 'Info', 1, 'info', 0, 118, 1, '', 1248023264, -1),
(119, 1, 'Player', 1, 'player', 0, 119, 1, '', 1248072948, -1),
(120, 1, 'Golman', 1, 'golman', 0, 120, 1, '', 1248073111, -1),
(121, 1, 'Parsing url', 1, 'parsing/url', 0, 121, 1, '', 1248416588, -1),
(124, 1, 'Index', 1, '', 0, 124, 1, '', 1249573948, -1),
(96, 2, 'Naming', 1, 'NejakaCeskaUrl', 0, 96, 1, '', 1248467509, -1),
(125, 1, 'Hint', 1, 'hint', 0, 147, 1, '', 1255428865, -1),
(126, 1, 'Hint for lib', 1, '', 0, 126, 1, '', 1255428884, -1),
(122, 1, 'Copying', 1, 'copying', 0, 122, 1, 'asdasda', 1248479781, -1),
(121, 2, 'Jazykovy test', 1, 'parsing/url/testing/web:language', 0, 121, 1, '', 1249574694, -1),
(127, 1, 'Index', 1, '', 0, 127, 1, '', 1259270965, -1),
(123, 1, 'Next page', 1, 'next/page/web:language', 0, 123, 1, '', 1248466660, -1),
(128, 1, 'File test', 1, 'FileTest/fl:fileId', 0, 128, 1, '', 1249575480, -1),
(129, 1, 'Redirect test', 1, 'redirect-test', 0, 129, 1, '', 1255186255, -1),
(130, 1, 'Some page', 1, 'some-page', 0, 130, 1, '', 1252588670, -1),
(131, 1, 'Menu', 1, 'menu', 0, 131, 1, '', 1255445785, -1),
(132, 1, 'Online Manual', 1, 'online-manual', 1, 132, 1, '', 1252589450, -1),
(133, 1, 'Game Overview', 1, 'game-overview', 1, 133, 1, '', 1257346762, -1),
(134, 1, 'How to train', 1, 'how to train', 1, 134, 1, '', 1252589573, -1),
(135, 1, 'Managing team', 1, 'managing-team', 1, 135, 1, '', 1252589562, -1),
(136, 1, 'FbM Forum', 1, 'fbm-forum', 1, 136, 1, '', 1252589606, -1),
(137, 1, 'Screenshots', 1, 'screenshots', 0, 137, 1, '', 1252589632, -1),
(138, 1, 'Official', 1, 'official', 1, 138, 1, '', 1252589651, -1),
(139, 1, 'Users', 1, 'users', 1, 139, 1, '', 1252589674, -1),
(140, 1, 'Hall of fame', 1, 'hall-of-fame', 1, 140, 1, '', 1252589705, -1),
(141, 1, 'About FbM', 1, 'about-fbm', 1, 141, 1, '', 1252589735, -1),
(142, 1, 'About authors', 1, 'about-authors', 1, 142, 1, '', 1252589759, -1),
(143, 1, 'About game', 1, 'about-game', 1, 143, 1, '', 1255445736, -1),
(147, 1, 'System setup', 1, 'system-setup', 0, 173, 1, '', 1255281598, -1),
(145, 1, 'Root Template', 1, '', 0, 145, 1, '', 1254153457, -1),
(146, 1, 'Direct link gallery', 1, 'DirectLinkGallery', 0, 146, 1, '', 1254666578, -1),
(148, 1, 'Index', 1, '', 0, 148, 1, '', 1255280622, -1),
(149, 1, 'System properties', 1, 'system-properties', 1, 149, 1, '', 1255281625, -1),
(150, 1, 'Content', 0, 'in', 0, 150, 1, '', 1261170703, -1),
(151, 1, 'News 5', 1, '', 0, 151, 1, '', 1261012701, -1),
(152, 1, 'Index', 1, '', 0, 152, 1, '', 1257175527, -1),
(153, 1, 'Login', 1, 'login', 0, 153, 1, '', 1256945942, -1),
(154, 1, 'Edit', 1, 'edit', 0, 154, 1, '', 1257176298, -1),
(155, 1, 'Property', 1, 'Property', 0, 155, 1, '', 1257175201, -1),
(156, 1, 'testsadads', 1, 'testesadads', 0, 156, 1, '', 1257767051, -1),
(157, 1, 'Login', 1, 'login', 0, 157, 1, '', 1257239498, -1),
(158, 1, 'Index', 1, '', 0, 158, 1, '', 1257241782, -1),
(166, 2, 'Testovaci', 1, 'Testovaci', 0, 166, 1, 'Testovaci', 1257766446, -1),
(167, 2, 'Testik', 1, 'Testik', 0, 167, 1, 'Testik', 1257766398, -1),
(168, 1, 'Gallery test', 1, 'GalleryTest', 0, 168, 1, '', 1257782656, -1),
(169, 1, 'Gallery Test Detail', 1, 'GalleryTest/fl:dirId', 0, 169, 1, '', 1257784758, -1),
(170, 1, 'Gallery test', 1, 'gallery', 0, 170, 1, '', 1257782617, -1),
(171, 1, 'Test1', 1, 'Test1', 0, 171, 1, '', 1260628441, -1);

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL,
  `language` tinytext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `language`) VALUES
(1, ''),
(2, 'cs'),
(3, 'en'),
(4, 'fr');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`id`, `parent_id`, `wp`) VALUES
(2, 0, 6),
(3, 2, 6),
(4, 2, 6),
(5, 150, 6),
(6, 5, 6),
(7, 5, 6),
(8, 5, 6),
(9, 5, 6),
(54, 0, 8),
(53, 9, 6),
(52, 9, 6),
(16, 5, 6),
(17, 5, 6),
(23, 5, 6),
(43, 0, 8),
(25, 5, 6),
(26, 25, 6),
(27, 25, 6),
(28, 25, 6),
(39, 16, 6),
(44, 5, 6),
(45, 44, 6),
(47, 0, 8),
(46, 44, 6),
(95, 0, 17),
(178, 23, 6),
(56, 5, 6),
(40, 16, 6),
(41, 16, 6),
(42, 16, 6),
(171, 0, 21),
(170, 0, 18),
(169, 170, 18),
(168, 170, 18),
(167, 0, 18),
(166, 0, 18),
(165, 0, 18),
(164, 0, 18),
(163, 0, 18),
(162, 0, 18),
(161, 0, 18),
(160, 0, 18),
(159, 0, 18),
(158, 0, 23),
(157, 0, 23),
(156, 0, 18),
(155, 0, 18),
(154, 0, 22),
(153, 0, 22),
(152, 0, 22),
(151, 0, 21),
(150, 2, 6),
(149, 147, 6),
(148, 147, 6),
(146, 0, 8),
(145, 0, 20),
(147, 5, 6),
(143, 141, 18),
(142, 141, 18),
(141, 131, 18),
(140, 131, 18),
(139, 137, 18),
(138, 137, 18),
(137, 131, 18),
(136, 131, 18),
(135, 132, 18),
(134, 132, 18),
(133, 132, 18),
(132, 131, 18),
(131, 0, 18),
(125, 5, 6),
(126, 125, 6),
(127, 0, 19),
(128, 0, 8),
(129, 0, 18),
(123, 121, 8),
(122, 0, 8),
(130, 0, 18),
(124, 123, 8),
(121, 0, 8),
(120, 118, 17),
(119, 118, 17),
(118, 0, 17),
(117, 112, 17),
(116, 112, 17),
(115, 112, 17),
(114, 112, 17),
(113, 112, 17),
(112, 0, 17),
(111, 105, 6),
(110, 105, 6),
(109, 105, 6),
(108, 105, 6),
(107, 105, 6),
(106, 105, 6),
(105, 5, 6),
(104, 103, 17),
(103, 95, 17),
(102, 103, 17),
(101, 103, 17),
(100, 103, 17),
(99, 103, 17),
(98, 103, 17),
(94, 0, 8),
(97, 103, 17),
(96, 0, 8),
(91, 90, 8),
(90, 0, 8),
(89, 86, 8),
(88, 86, 8),
(87, 86, 8),
(85, 83, 8),
(84, 83, 8),
(172, 147, 6),
(83, 0, 8),
(173, 5, 6),
(177, 23, 6),
(176, 23, 6),
(175, 23, 6),
(174, 23, 6),
(75, 0, 8),
(74, 75, 8),
(73, 75, 8),
(86, 0, 8),
(71, 66, 8),
(70, 66, 8),
(69, 66, 8),
(68, 66, 8),
(67, 66, 8),
(66, 0, 8),
(65, 0, 8),
(64, 60, 8),
(63, 60, 8),
(62, 60, 8),
(60, 0, 8);

-- --------------------------------------------------------

--
-- Table structure for table `page_file`
--

DROP TABLE IF EXISTS `page_file`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=34 ;

--
-- Dumping data for table `page_file`
--

INSERT INTO `page_file` (`id`, `name`, `content`, `for_all`, `for_msie6`, `for_msie7`, `for_msie8`, `for_firefox`, `for_opera`, `for_safari`, `type`, `wp`) VALUES
(1, 'ajax', 'var ajaxRootElement = null;\r\nvar ajaxLoading = null;\r\n\r\nfunction addEvent (obj, ev, func, b) {\r\n  if(obj.addEventListener) {\r\n    obj.addEventListener(ev, func, b);\r\n  } else {\r\n    obj.attachEvent("on" + ev, func);\r\n  }\r\n}\r\n\r\nfunction stopEvent(event) {\r\n  event.cancelBubble = true;\r\n  event.returnValue = false;\r\n  if(navigator.appName != "Microsoft Internet Explorer") {\r\n    event.preventDefault();\r\n  }\r\n}\r\n\r\naddEvent(window, ''load'', initAjax, false);\r\n\r\nfunction initAjax(event) {\r\n  initDynamicLinks(document);\r\n}\r\n\r\nfunction initDynamicLinks(root) {\r\n  if(root != null) {\r\n    var lis = root.getElementsByTagName(''div'');\r\n    for(var i = 0; i < lis.length; i ++) {\r\n      if(lis[i].className.indexOf(''link'') != -1) {\r\n        if(lis[i].childNodes[0] != null && lis[i].childNodes[0].tagName == "A") {\r\n          addEvent(lis[i].childNodes[0], ''click'', menuLinkClick, false);\r\n        }\r\n      }\r\n    }\r\n    var as = root.getElementsByTagName(''a'');\r\n    for(var i = 0; i < as.length; i ++) {\r\n      if(as[i].rel == "dynamic-link") {\r\n        addEvent(as[i], ''click'', menuLinkClick, false);\r\n      }\r\n    }\r\n  }\r\n}\r\n\r\nfunction menuLinkClick(event) {\r\n  var anchor = (event.srcElement) ? event.srcElement : event.target;\r\n  if(anchor.parentNode != null && anchor.parentNode.tagName == "A") {\r\n    anchor = anchor.parentNode;\r\n    if(ajaxLoading == null) {\r\n      ajaxLoading = document.createElement(''div'');\r\n      ajaxLoading.className = "ajax-loading";\r\n      document.body.appendChild(ajaxLoading);\r\n    }\r\n    ajaxLoading.innerHTML = "Loading ...";\r\n    var xmlhttp = new Rxmlhttp();\r\n    xmlhttp.setAsync(true);\r\n    xmlhttp.setMethod("GET");\r\n    xmlhttp.onSuccess(processRequest);\r\n    xmlhttp.loadPage(anchor.href + "?__START_ID=24");\r\n    stopEvent(event);\r\n  }\r\n}\r\n\r\nfunction processRequest(xmlhttp) {\r\n  var temp = document.createElement(''div'');\r\n  temp.innerHTML = xmlhttp.responseText.replace(''<body'', ''<div '').replace(''</body'', ''</div'');\r\n  var body = temp.getElementsByTagName(''div'');\r\n  body = body[0];\r\n  ajaxRootElement = document.getElementById(''web-content'');\r\n  if(ajaxRootElement == null) {\r\n    ajaxLoading.innerHTML = "ERROR LOADING .. Press F5!";\r\n  } else {\r\n    ajaxRootElement.innerHTML = body.innerHTML;\r\n    ajaxLoading.innerHTML = "";\r\n    initDynamicLinks(ajaxRootElement);\r\n  }\r\n}', 1, 0, 0, 0, 0, 0, 0, 2, 1),
(2, 'tiny', '/* CSS file for Tiny Editor! */', 1, 0, 0, 0, 0, 0, 0, 1, 1),
(3, 'Ahoj', 'body {\r\n  background: red;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 9),
(4, 'web', 'body {\r\n  width: 800px;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 8),
(6, 'wysiwyg', 'body {\r\n  background: red;\r\n}\r\n\r\nh1 {\r\n  color: blue;\r\n}\r\n\r\nh2 {\r\n  background: yellow;\r\n  color: green;\r\n}\r\n\r\np {\r\n  margin: 10px;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 8),
(8, 'Frames', '.closed .frame-body {\r\n	background: white;\r\n	display: none;\r\n}\r\n\r\n.frame-cover {\r\n	border: 1px solid #04601C;\r\n}\r\n\r\n.frame-head {\r\n	width: 100%;\r\n	background: #04601C;\r\n}\r\n\r\n.frame-cover .frame-head .click-able-roll {\r\n  width: 15px;\r\n  height: 15px;\r\n  margin: 3px;\r\n  display: block;\r\n  background: url(''/images/minus.png'');\r\n}\r\n\r\n.frame-cover.closed-frame .frame-head .click-able-roll {\r\n  background: url(''/images/plus.png'');\r\n}\r\n\r\n.frame-cover .frame-head .click-able-roll span {\r\n  display: none;\r\n}\r\n\r\n.frame-head .frame-label {\r\n	color: white;\r\n	font-weight: bold;\r\n	float: left;\r\n	padding: 1px 0 1px 5px;\r\n}\r\n\r\n.frame-head .frame-close {\r\n	float: right;\r\n}\r\n\r\n.frame-body {\r\n	padding: 5px;\r\n	background: white;\r\n}\r\n\r\n.frames-used .frame-body {\r\n	display: none;\r\n}\r\n\r\n\r\n.frame-body .error {\r\n	margin: 2px 0;\r\n	padding: 0 0 1px 18px;\r\n	color: white;\r\n	background: url(''/images/error.png'') #bd2828 no-repeat 1px 3px;\r\n}\r\n\r\n.frame-body .success {\r\n	margin: 2px 0;\r\n	padding: 0 0 1px 18px;\r\n	color: white;\r\n	background: url(''/images/success.png'') #38cb35 no-repeat 1px 3px;\r\n}\r\n\r\n.frame-body .warning {\r\n	margin: 2px 0;\r\n	padding: 0 0 1px 18px;\r\n	color: black;\r\n	background: url(''/images/warning.png'') no-repeat 1px 3px;\r\n}\r\n\r\n.clear {\r\n  clear: both;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 8),
(9, 'Gallery', '.gallery-item {\r\n  float: left;\r\n  margin: 15px;\r\n}\r\n\r\n.clear {\r\n  clear: both;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 8),
(10, 'Plasticport', '.form-orders {\r\n  border-collapse:collapse;\r\n  border-top:1px solid #CCCCCC;\r\n}\r\n\r\n.form-orders td {\r\n  overflow: hidden;\r\n  padding: 2px 10px;\r\n  border-bottom: 1px solid #cccccc;\r\n}\r\n\r\n.form-orders th {\r\n  text-align: left;\r\n  background: #cccccc;\r\n  padding: 0px 10px;\r\n  border-bottom: 1px solid black;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 6),
(11, 'Pokusny', 'body {\r\n  background: red;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 8),
(12, 'Pokusny', 'window.onload = function () {\r\n  alert(''Hello world!'');\r\n}', 1, 0, 0, 0, 0, 0, 0, 2, 8),
(13, 'Error pages', 'body {\r\n  background: #cccccc;\r\n}\r\n\r\n.error {\r\n  color: red;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 8),
(14, 'Web Design', 'img {\r\n  border: none;\r\n}\r\n\r\n.clear {\r\n  clear: both;\r\n}\r\n\r\nbody {\r\n  font-family: Arial, Verdana, Times;\r\n  margin: 0;\r\n  color: white;\r\n  background: black;\r\n}\r\n\r\nh1, h2, h3, h4, h5 {\r\n  color: black;\r\n  margin-top: 0;\r\n  font-size: 16px;\r\n  padding: 6px 0;\r\n  text-align: center;\r\n  background: url(''~/file.php?rid=41'') repeat-x left top;\r\n}\r\n\r\np {\r\n  text-align: justify;\r\n}\r\n\r\n/* -------- DESIGN ----------------- */\r\n\r\n.all {\r\n  width: 900px;\r\n}\r\n\r\n.head .corner {\r\n  width: 200px;\r\n  height: 200px;\r\n  float: left;\r\n  background: url(''~/file.php?rid=30'') no-repeat left top;\r\n}\r\n\r\n.head .head-center {\r\n  width: 700px;\r\n  height: 200px;\r\n  float: left;\r\n}\r\n\r\n.head .head-center .head-image {\r\n  height: 150px;\r\n  background: url(''~/file.php?rid=31'') no-repeat left top;\r\n}\r\n\r\n.head .head-center .loading {\r\n  padding: 15px 0 15px 38px;\r\n  height: 20px;\r\n  background: url(''~/file.php?rid=42'') no-repeat 6px 12px;\r\n}\r\n\r\n.left {\r\n  width: 200px;\r\n  float: left;\r\n  margin: 0 0 20px 0;\r\n}\r\n\r\n.left .counter-cover {\r\n  margin: 0 25px;\r\n}\r\n\r\n.content {\r\n  width: 700px;\r\n  min-height: 500px;\r\n  _height: 500px;\r\n  margin: 10px 0 20px 0;\r\n  float: left;\r\n}\r\n\r\nhr {\r\n  margin: 10px;\r\n  height: 5px;\r\n  background: #FE9834;\r\n  border: none;\r\n  color: transparent;\r\n}\r\n\r\n.banners, .copyright {\r\n  margin: 0 25px;\r\n}\r\n\r\n/* -------------- MENU -------------- */\r\n\r\n.menu-cover {\r\n  width: 150px;\r\n  margin: 10px 0;\r\n  padding: 0 25px;\r\n}\r\n\r\n.menu-cover ul {\r\n  margin: 0;\r\n  padding: 0;\r\n  list-style: none;\r\n}\r\n\r\n.menu-cover ul li.menu-item {\r\n  list-style: none;\r\n  margin: 0;\r\n  padding: 0;\r\n}\r\n\r\n.menu-cover ul li.menu-item a {\r\n  float: left;\r\n  display: block;\r\n  width: 150px;\r\n  height: 50px;\r\n  margin: 0;\r\n  padding: 0;\r\n}\r\n\r\n.menu-cover ul li.menu-item a span {\r\n  display: none;\r\n}\r\n\r\n.menu-cover ul li.menu-item.li-1 a {\r\n  background: url(''~/file.php?rid=35'') no-repeat left top;\r\n}\r\n\r\n.menu-cover ul li.menu-item.li-2 a {\r\n  background: url(''~/file.php?rid=36'') no-repeat left top;\r\n}\r\n\r\n.menu-cover ul li.menu-item.li-3 a {\r\n  background: url(''~/file.php?rid=33'') no-repeat left top;\r\n}\r\n\r\n.menu-cover ul li.menu-item.li-4 a {\r\n  background: url(''~/file.php?rid=37'') no-repeat left top;\r\n}\r\n\r\n.menu-cover ul li.menu-item.li-5 a {\r\n  background: url(''~/file.php?rid=34'') no-repeat left top;\r\n}\r\n\r\n.menu-cover ul li.menu-item.li-6 a {\r\n  background: url(''~/file.php?rid=38'') no-repeat left top;\r\n}\r\n\r\n.menu-cover ul li.menu-item a:hover {\r\n  background-position: 0 -100px;\r\n}\r\n\r\n.menu-cover ul li.menu-item.active-item a {\r\n  background-position: 0 -50px;\r\n}\r\n\r\n.menu-cover ul li.menu-item.active-item a:hover {\r\n  background-position: 0 -50px;\r\n}\r\n\r\n/* -------------------------- Temp ------------------------------- */\r\n/*\r\nbody {\r\n  color: black;\r\n  background: white;\r\n}\r\n*/', 1, 0, 0, 0, 0, 0, 0, 1, 17),
(15, 'Counter', '.clear {\r\n  clear: both;\r\n}\r\n\r\n.counter .col-name {\r\n  float: left;\r\n}\r\n\r\n.counter .col-value {\r\n  float: right;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 17),
(16, 'Ajax Links', '/**\r\n *\r\n *  @author  Marek Fišera marek.fisera@email.cz\r\n *  @date    2009/07/19\r\n *\r\n */\r\nfunction Links(root, startPageId, responseTemplate, disableInit) {\r\n	if(root == null) {\r\n		throw "Passed item isn''t dom element!";\r\n		return;\r\n	}\r\n	\r\n	var Inited = false;\r\n	var IsLoadingNow = false;\r\n	var This = this;\r\n	var Root = root;\r\n	var Links = new Array();\r\n	var XmlHttp = new Rxmlhttp();\r\n	var AdditionalQuery = new String();\r\n	var StartPageId = (startPageId != null) ? startPageId : -1;\r\n	var UseStartPageId = true;\r\n	var ResponseTemplate = (responseTemplate != null) ? responseTemplate : '''';\r\n	var DisableInit = (disableInit != null) ? disableInit : '''';\r\n	var UpdateLocation = true;\r\n	\r\n	/**\r\n	 *\r\n	 *	Init process form.	 \r\n	 *\r\n	 */\r\n	this.init = function() {\r\n		if(!Inited) {\r\n			if(DisableInit != true) {\r\n				var links = root.getElementsByTagName(''A'');\r\n				for(var i = 0; i < links.length; i ++) {\r\n					This.addLink(links[i], ''click'');\r\n				}\r\n			}\r\n			XmlHttp.setAsync(true);\r\n			XmlHttp.setMethod(''get'');\r\n			XmlHttp.onSuccess(This.onSuccessInner);\r\n			XmlHttp.onError(This.onErrorInner);\r\n			\r\n			Inited = true;\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Loads page that is in url after #.\r\n	 *\r\n	 */	 	 	 	\r\n	this.loadDefault = function() {\r\n		if(window.location.href.indexOf(''#'') != -1) {\r\n			var url = window.location.protocol + ''//'' + window.location.host + window.location.href.substring(window.location.href.indexOf(''#'') + 1, window.location.href.length);\r\n			This.loadPage(url);\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Adds event to link.\r\n	 *	@param		object				object (anchor) to add event to\r\n	 *	@param		event					event name, without "on"\r\n	 *\r\n	 */	 	 	 	 	\r\n	this.addLink = function(object, event) {\r\n		if(object != null && object.tagName && object.tagName == "A") {\r\n			Links[Links.length] = object;\r\n			This.addEvent(object, event, This.onEvent, false);\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Adds event to links.\r\n	 *	@param		root					root element to find elements in to add event to\r\n	 *	@param		event					event name, without "on"\r\n	 *\r\n	 */\r\n	this.addLinks = function(root, event) {\r\n		var links = root.getElementsByTagName(''A'');\r\n			for(var i = 0; i < links.length; i ++) {\r\n			if(!Links.inArray(links[i])) {\r\n				This.addLink(links[i], ''click'');\r\n			}\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Removes event form link.\r\n	 *	@param		object				object (anchor) to add event to\r\n	 *	@param		event					event name, without "on"\r\n	 *\r\n	 */	 	 	 	 	\r\n	this.removeLink = function(object, event) {\r\n		if(object != null && object.tagName && object.tagName == "A") {\r\n			for(var i = 0; i < Links.length; i ++) {\r\n				if(Links[i] == object) {\r\n					This.removeEvent(Links[i], event, This.onEvent, false);\r\n					Links.remove(i);\r\n					break;\r\n				}\r\n			}\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Sets addtional query.\r\n	 *	@param		str						query string	 \r\n	 *	 \r\n	 */	 	 	\r\n	this.setQuery = function(str) {\r\n		AdditionalQuery = str;\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Sets use start page id parameter.\r\n	 *	\r\n	 *	@param		use						use start page id parameter	 	 \r\n	 *\r\n	 */	 	 	 	\r\n	this.setUseStartPageId = function(use) {\r\n		if(use == true) {\r\n			UseStartPageId = true;\r\n		} else {\r\n			UseStartPageId = false;\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Set start page id for requested paged.\r\n	 *	\r\n	 *	@param		pageId				start page id	 	 \r\n	 *	 \r\n	 */	 	 	\r\n	this.setStartPageId = function(pageId) {\r\n		if(pageId != null) {\r\n			StartPageId = pageId;\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Set response template for requested paged.\r\n	 *	\r\n	 *	@param		responseTemplate		 	 response template, possible value xml.\r\n	 *	 \r\n	 */	 	 	\r\n	this.setResponseTemplate = function(responseTemplate) {\r\n		if(responseTemplate != null) {\r\n			ResponseTemplate = responseTemplate;\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Set update location.\r\n	 *	\r\n	 *  @param		updateLocation					new value	 	 \r\n	 *\r\n	 *\r\n	 */	 	 	 	 	\r\n	this.setUpdateLocation = function(updateLocation) {\r\n		if(updateLocation == false) {\r\n			UpdateLocation = false;\r\n		} else if(updateLocation == true) {\r\n			UpdateLocation = true;\r\n		}\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Called when event is fired.\r\n	 *	@param		event					dom event object	 \r\n	 *\r\n	 */	 	 	 	\r\n	this.onEvent = function(event) {\r\n		if(!IsLoadingNow) {\r\n			var url = '''';\r\n			\r\n			element = ((event.srcElement) ? event.srcElement : event.target);\r\n			if(element.tagName != ''A'') {\r\n				element = element.parentNode;\r\n				if(element.tagName != ''A'') {\r\n					element = element.parentNode;\r\n					if(element.tagName != ''A'') {\r\n						element = element.parentNode;\r\n						if(element.tagName != ''A'') {\r\n							throw "Cannot find anchor element!";\r\n						} else {\r\n							url = element.href;\r\n						}\r\n					} else {\r\n						url = element.href;\r\n					}\r\n				} else {\r\n					url = element.href;\r\n				}\r\n			} else {\r\n				url = element.href;\r\n			}\r\n			\r\n			if(url && url.length && url.length > 0) {\r\n				if(AdditionalQuery && AdditionalQuery.length && AdditionalQuery.length > 0) {\r\n					if(url.indexOf(''?'') == -1) {\r\n						url += ''?'' + AdditionalQuery;\r\n					} else {\r\n						url += ''&'' + AdditionalQuery;\r\n					}\r\n				}\r\n				if(UseStartPageId == true) {\r\n					This.loadPage(url, true);\r\n				} else {\r\n					This.loadPage(url, false);\r\n				}\r\n			}\r\n		}\r\n		\r\n		This.stopEvent(event);\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Load specific page.\r\n	 *	@param		url							server page url\r\n	 *\r\n	 */	 	 	 	\r\n	this.loadPage = function(url) {\r\n		var pos = window.location.href.indexOf(''#'');\r\n		if(pos == -1) {\r\n			pos = window.location.href.length;\r\n		}\r\n		var upos = url.indexOf(''?__'');\r\n		if(upos == -1) {\r\n			upos = url.length;\r\n		}\r\n		This.beforeRequest();\r\n		\r\n		\r\n		if(UseStartPageId && StartPageId && StartPageId != -1) {\r\n			if(url.indexOf(''?'') == -1) {\r\n				url += ''?__START_ID='' + StartPageId;\r\n			} else {\r\n				url += ''&__START_ID='' + StartPageId;\r\n			}\r\n		}\r\n		if(ResponseTemplate != '''') {\r\n			if(url.indexOf(''?'') == -1) {\r\n				url += ''?__TEMPLATE='' + ResponseTemplate;\r\n			} else {\r\n				url += ''&__TEMPLATE='' + ResponseTemplate;\r\n			}\r\n		}\r\n		\r\n		if(UpdateLocation) {\r\n			window.location.href = window.location.href.substring(0, pos) + ''#'' + url.substring(url.substring(9, url.length).indexOf(''/'') + 9, upos);\r\n		}\r\n		XmlHttp.loadPage(url);\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Redefine this function before form submit.\r\n	 *	@param		event					dom event object\r\n	 *\r\n	 */	 	 	 	\r\n	this.beforeRequest = function(event) {\r\n		alert(''Submitting request ...'');\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Redefine this function called on request success.\r\n	 *	\r\n	 *	@param		xmlHttp				Rxmlhttp object	 	 \r\n	 *\r\n	 */\r\n	this.onSuccess = function(xmlHttp) {\r\n		alert(''Request successfully completed!'');\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Redefine this function called on request error.\r\n	 *	\r\n	 *	@param		xmlHttp				Rxmlhttp object	 	 \r\n	 *\r\n	 */\r\n	this.onError = function(xmlHttp) {\r\n		alert(''Some error occured in request!'');\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Called on success.	 \r\n	 *\r\n	 *	@param		xmlHttp				Rxmlhttp object	 \r\n	 */	 	 	\r\n	this.onSuccessInner = function(xmlHttp) {\r\n		This.onSuccess(xmlHttp);\r\n		IsLoadingNow = false;\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Called on error.	 \r\n	 *\r\n	 *	@param		xmlHttp				Rxmlhttp object	 \r\n	 */	 	 	\r\n	this.onErrorInner = function(xmlHttp) {\r\n		This.onError(xmlHttp);\r\n		IsLoadingNow = false;\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Adds event to element\r\n	 *	@param		obj						dom object to add event to\r\n	 *	@param		ev						event name, without "on"\r\n	 *	@param		func					function to call\r\n	 *	@param		b							boolean -> bubble	 	 	 	 \r\n	 *	 \r\n	 */\r\n	this.addEvent = function (obj, ev, func, b) {\r\n		if(ev != null && ev.length > 1) {\r\n			if(ev.substring(0,2) == ''on'') {\r\n				ev = ev.substring(2, ev.length);\r\n			} \r\n		} else {\r\n			ev = ''click'';\r\n		}\r\n    if(obj.addEventListener) {\r\n      obj.addEventListener(ev, func, b);\r\n    } else {\r\n      obj.attachEvent("on" + ev, func);\r\n    }\r\n  }\r\n	\r\n	/**\r\n	 *\r\n	 *	Removes event form element\r\n	 *	@param		obj						dom object to add event to\r\n	 *	@param		ev						event name, without "on"\r\n	 *	@param		func					function to call\r\n	 *	@param		b							boolean -> bubble	 	 	 	 \r\n	 *	 \r\n	 */\r\n  this.removeEvent = function(obj, ev, func, b) {\r\n  	if(ev != null && ev.length > 1) {\r\n			if(ev.substring(0,2) == ''on'') {\r\n				ev = ev.substring(2, ev.length);\r\n			} \r\n		} else {\r\n			ev = ''click'';\r\n		}\r\n    if(obj.removeEventListener) {\r\n      obj.removeEventListener(ev, func, b);\r\n    } else {\r\n      obj.deattachEvent("on" + ev, func);\r\n    }\r\n	}\r\n	\r\n	/**\r\n	 *\r\n	 *	Stops event.\r\n	 *	\r\n	 *	@param		event					event to stop\r\n	 *\r\n	 */	 	 	 	\r\n  this.stopEvent = function (event) {\r\n    if(navigator.appName != "Microsoft Internet Explorer") {\r\n      event.stopPropagation();\r\n      event.preventDefault();\r\n    } else {\r\n      event.cancelBubble = true;\r\n      event.returnValue = false;\r\n    }\r\n  }\r\n  \r\n  this.init();\r\n}\r\n\r\nArray.prototype.remove = function(from, to) {\r\n  var rest = this.slice((to || from) + 1 || this.length);\r\n  this.length = from < 0 ? this.length + from : from;\r\n  return this.push.apply(this, rest);\r\n};\r\n\r\nArray.prototype.inArray = function(el) {\r\n	if(el == null) return false;\r\n	for(var i = 0; i < this.length; i ++) {\r\n		if(this[i] == el) {\r\n			return true;\r\n		}\r\n	}\r\n	return false;\r\n};', 1, 0, 0, 0, 0, 0, 0, 2, 17),
(17, 'Ajax Init', 'Event.domReady.add(init);\r\n	\r\nfunction init(event) {\r\n  var addedScripts = new Array();\r\n  var addedStyles = new Array();\r\n  var links = new Links(document);\r\n  var result = document.getElementById(''ajax-body'');\r\n  var loading = document.getElementById(''loading'');\r\n  \r\n  var head = document.getElementsByTagName(''head'');\r\n  head = head[0];\r\n\r\n  links.setStartPageId(103);\r\n  links.setResponseTemplate(''xml'');\r\n  links.setUseStartPageId(true);\r\n		\r\n  links.beforeRequest = function(event) {\r\n    loading.style.display = '''';\r\n  }\r\n		\r\n  links.onSuccess = function(xmlHttp) {\r\n    for(var i = 0; i < addedScripts.length; i ++) {\r\n      head.removeChild(addedScripts[i]);\r\n    }\r\n    for(var i = 0; i < addedStyles.length; i ++) {\r\n      head.removeChild(addedStyles[i]);\r\n    }\r\n    addedScripts = new Array();\r\n    addedStyles = new Array();\r\n  \r\n    var temp = document.createElement(''div'');\r\n\r\n    temp.innerHTML = xmlHttp.responseText;\r\n    if(navigator.appName == ''Microsoft Internet Explorer'') {\r\n      var body = temp.getElementsByTagName(''content'');\r\n      var title = temp.getElementsByTagName(''title'');\r\n      var scripts = temp.getElementsByTagName(''scripts'');\r\n      var styles = temp.getElementsByTagName(''styles'');\r\n    } else {\r\n      var body = temp.getElementsByTagName(''rssmm:content'');\r\n      var title = temp.getElementsByTagName(''rssmm:title'');\r\n      var scripts = temp.getElementsByTagName(''rssmm:scripts'');\r\n      var styles = temp.getElementsByTagName(''rssmm:styles'');\r\n    }\r\n    body = body[0];\r\n    title = title[0];\r\n    scripts = scripts[0];\r\n    styles = styles[0];\r\n\r\n    var childs = styles.childNodes;\r\n    for(var i = 0; i < childs.length; i ++) {\r\n    	addedStyles[addedStyles.length] = document.createElement(''link'');\r\n    	addedStyles[addedStyles.length - 1].type = ''text/javascript'';\r\n    	addedStyles[addedStyles.length - 1].rel = ''stylesheet'';\r\n    	addedStyles[addedStyles.length - 1].src = childs[i].innerHTML;\r\n      head.appendChild(addedStyles[addedStyles.length - 1]);\r\n    }\r\n    var childs = scripts.childNodes;\r\n    for(var i = 0; i < childs.length; i ++) {\r\n    	addedScripts[addedScripts.length] = document.createElement(''script'');\r\n    	addedScripts[addedScripts.length - 1].type = ''text/javascript'';\r\n    	addedScripts[addedScripts.length - 1].src = childs[i].innerHTML;\r\n      head.appendChild(addedScripts[addedScripts.length - 1]);\r\n    }\r\n    \r\n    result.innerHTML = body.innerHTML;\r\n    document.title = title.innerHTML;\r\n\r\n    links.addLinks(result);\r\n    loading.style.display = ''none'';\r\n  }\r\n		\r\n  links.onError = function(xmlHttp) {\r\n    loading.style.display = ''none'';\r\n    result.innerHTML = ''<h4 class="error">Some occurs!</h4><p class="error" >Press F5 to reload page.</p>'';\r\n  }\r\n\r\n  loading.style.display = ''none'';\r\n  links.loadDefault();\r\n}', 1, 0, 0, 0, 0, 0, 0, 2, 17),
(18, 'Content', '/* -------------------- Articles ----------------------- */\r\n\r\n.article {\r\n  margin-bottom: 20px;\r\n}\r\n\r\n.article-timestamp {\r\n  font-size: 90%;\r\n}\r\n\r\n.article-author {\r\n  float: right;\r\n}\r\n\r\n/* --------------------- Dido -------------------------- */\r\n\r\n.dido .results, .dido .table {\r\n  width: 50%;\r\n  float: left;\r\n}\r\n\r\n.dido .results h1, .dido .table h1 {\r\n  margin-bottom: 2px;\r\n}\r\n\r\n.dido .results .machtes-in-round, .dido .table .table-in {\r\n  padding: 0 10px;\r\n}\r\n\r\n.dido .results .round, .dido .table .table-in {\r\n  margin-bottom: 5px;\r\n}\r\n\r\n.dido .results .machtes-in-round table, .dido .table .table-in table {\r\n  width: 100%;\r\n}\r\n\r\n/* --------------- Players ------------------- */\r\n\r\n.players .player {\r\n  min-height: 25px;\r\n  font-weight: bold;\r\n  font-size: 110%;\r\n  border-bottom: 1px solid #666666;\r\n}\r\n\r\n.players .a-team, .players .b-team {\r\n  margin: 0 10px 10px 10px;\r\n  border-top: 1px solid #666666;\r\n}\r\n\r\n.players .player .number, .players .player .name {\r\n  padding: 5px 10px;\r\n  float: left;\r\n}\r\n\r\n.players .player .number {\r\n  width: 20px;\r\n  text-align: right;\r\n}\r\n\r\n.players .player:hover {\r\n  cursor: pointer;\r\n  color: #fe9834;\r\n  background: #111111;\r\n}\r\n\r\n.player-info {\r\n  border-bottom: 1px solid #666666;\r\n}\r\n\r\n.player-info .player-details {\r\n  width: 147px;\r\n  height: 195px;\r\n  padding: 10px;\r\n  float: right;\r\n}\r\n\r\n.player-info .player-details img {\r\n  width: 147px;\r\n  height: 195px;\r\n  border: 2px solid #333333;\r\n}\r\n\r\n.player-info .player-seasons {\r\n  width: 513px;\r\n  float: left;\r\n}\r\n\r\n.player-info table {\r\n  width: 100%;\r\n  border-collapse: collapse;\r\n}\r\n\r\n.player-info table th, .player-info table td {\r\n  padding: 2px 15px;\r\n}\r\n\r\n.player-info table th {\r\n  text-align: left;\r\n  background: #333333;\r\n}\r\n\r\n.player-info table td {\r\n  border-top: 1px solid #333333;\r\n}\r\n\r\n.player-info table tr.summary td {\r\n  color: #fe9834;\r\n  border-top: 2px solid #333333;\r\n}', 1, 0, 0, 0, 0, 0, 0, 1, 17),
(21, 'Players', 'function showPlayerDetail(event, pid, pos) {\r\n  var el = (event.target) ? event.target : event.srcElement;\r\n  var links = new Links(el);\r\n  var loading = document.getElementById(''loading'');\r\n  if(pos == 1) {\r\n    var url = ''~/info/golman?player-id='' + pid;\r\n  } else {\r\n    var url = ''~/info/player?player-id='' + pid;\r\n  }\r\n  var next = el;\r\n  if(next.className.indexOf(''player'') == -1) {\r\n    next = next.parentNode;\r\n    if(next.className.indexOf(''player'') == -1) {\r\n      next = next.parentNode;\r\n      if(next.className.indexOf(''player'') == -1) {\r\n        next = next.parentNode;\r\n      }\r\n    }\r\n  }\r\n  if(navigator.appName == ''Microsoft Internet Explorer'') {\r\n    next = next.nextSibling;\r\n  } else {\r\n    next = next.nextSibling.nextSibling;\r\n  }\r\n\r\n  next.innerHTML = '''';\r\n\r\n  if(next.style.display != '''') {\r\n    links.setResponseTemplate(''xml'');\r\n    links.setUpdateLocation(false);\r\n  \r\n    links.beforeRequest = function(event) {\r\n      loading.style.display = '''';\r\n    }\r\n		\r\n    links.onSuccess = function(xmlHttp) {\r\n      var temp = document.createElement(''div'');\r\n\r\n      temp.innerHTML = xmlHttp.responseText;\r\n      if(navigator.appName == ''Microsoft Internet Explorer'') {\r\n        var body = temp.getElementsByTagName(''content'');\r\n      } else {\r\n        var body = temp.getElementsByTagName(''rssmm:content'');\r\n      }\r\n      body = body[0];\r\n      next.innerHTML = body.innerHTML\r\n    \r\n      loading.style.display = ''none'';\r\n    }\r\n		\r\n    links.onError = function(xmlHttp) {\r\n      loading.style.display = ''none'';\r\n      next.innerHTML = ''<h4 class="error">Some occurs!</h4><p class="error" >Press F5 to reload page.</p>'';\r\n    }\r\n\r\n    links.loadPage(url);\r\n    next.style.display = '''';\r\n  } else {\r\n    next.style.display = ''none'';\r\n  }\r\n}', 1, 0, 0, 0, 0, 0, 0, 2, 17),
(30, 'Styles', 'h1 {\n    color: red;\n}', 1, 0, 0, 0, 0, 0, 0, 1, 18),
(29, 'Tiny init', 'function addEvent (obj, ev, func, b) {\r\n  if(obj.addEventListener) {\r\n    obj.addEventListener(ev, func, b);\r\n  } else {\r\n    obj.attachEvent("on" + ev, func);\r\n  }\r\n}\r\n\r\nfunction createTiny() {\r\n    var taId = "page-edit-content";\r\n    \r\n    tinyMCE.init({\r\n        // General options\r\n        mode : "none",\r\n        mode : "exact",\r\n        elements: taId,\r\n        theme : "advanced",\r\n        plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",\r\n\r\n        // Theme options\r\n        theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",\r\n        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor",\r\n        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl",\r\n        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",\r\n        theme_advanced_toolbar_location : "top",\r\n        theme_advanced_toolbar_align : "left",\r\n        theme_advanced_statusbar_location : "bottom",\r\n        theme_advanced_resizing : false,\r\n\r\n        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\r\n        // Example content CSS (should be your site CSS)\r\n        content_css : "css/content.css",\r\n        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\r\n\r\n        // Drop lists for link/image/media/template dialogs\r\n        template_external_list_url : "lists/template_list.js",\r\n        external_link_list_url : "lists/link_list.js",\r\n        external_image_list_url : "lists/image_list.js",\r\n        media_external_list_url : "lists/media_list.js",\r\n    });\r\n}\r\n\r\naddEvent(window, ''load'', createTiny, false);', 1, 0, 0, 0, 0, 0, 0, 2, 23),
(26, 'test', '.body {\n    background: red;\n}', 1, 0, 0, 0, 0, 0, 0, 1, 21),
(27, 'Styles', '.error {\n    margin: 2px 0;\n    padding: 0 0 1px 18px;\n    color: white;\n    background: url(''~/images/error.png'') #bd2828 no-repeat 1px 3px;\n}\n\n.success {\n    margin: 2px 0;\n    padding: 0 0 1px 18px;\n    color: white;\n    background: url(''~/images/success.png'') #38cb35 no-repeat 1px 3px;\n}\n\n.warning {\n    margin: 2px 0;\n    padding: 0 0 1px 18px;\n    color: black;\n    background: url(''~/images/warning.png'') #ff8c2f no-repeat 1px 3px;\n}\n\n.page-action, .page-action form {\n    display: inline;\n}', 1, 0, 0, 0, 0, 0, 0, 1, 22),
(28, 'Style', '.clear {\n    clear: both;\n}\n\n.error {\n    margin: 2px 0;\n    padding: 0 0 1px 18px;\n    color: white;\n    background: url(''/images/error.png'') #bd2828 no-repeat 1px 3px;\n}\n\n.success {\n    margin: 2px 0;\n    padding: 0 0 1px 18px;\n    color: white;\n    background: url(''/images/success.png'') #38cb35 no-repeat 1px 3px;\n}    \n\n.warning {\n    margin: 2px 0;\n    padding: 0 0 1px 18px;\n    color: black;\n    background: url(''/images/warning.png'') #ff8c2f no-repeat 1px 3px;\n}\n\n.page .page-id form, .page .page-id div {\n    display: inline;\n}\n\n.page-list ul {\n    margin: 0;\n}\n\n.page-list ul li {\n    margin: 8px 0;\n}\n\n.page-list input[type=image] {\n    margin: 0 2px -4px;\n}\n\n.page .page-id .form-page3, .page .page-id .page-move1, .page .page-id .page-move2 {\n    display: none;\n}\n\n.page .page-id .page-id-col {\n    color: #cccccc;\n}\n\n.frame .frame-head, .edit-clear-cache, .edit-cache-time, .edit-rights, #cover-page-edit-tag-lib-start, #cover-page-edit-tag-lib-end, #cover-page-edit-head {\n    display: none !important;\n}\n\n.frame .frame-body {\n    display: block !important;\n}\n\n.edit-submit, .edit-prop, .edit-area-editors, .edit-keywords, .add-page, .page-list {\n    margin: 2px 4px;\n    padding: 4px 8px;\n    background: #f5f5f5\n}\n\n.edit-submit label, .edit-prop label, .edit-keywords label {\n    width: 120px;\n    display: inline-block;\n}\n\n.edit-area-editors label {\n    display: block;\n}\n\n.edit-area-editors textarea {\n    width: 100%;\n}\n\n.edit-submit input[type=text], .edit-prop input[type=text], .edit-keywords input[type=text] {\n    width: 300px;\n}', 1, 0, 0, 0, 0, 0, 0, 1, 23),
(31, 'Gallery-script', 'Event.domReady.add(init);\n\nvar GalleryImages = null;\nvar GalleryImagesIndex = 0;\nvar GalleryPrevLink = null;\nvar GalleryNextLink = null;\nvar GalleryCover = null;\nvar GalleryCounter = null;\n\nfunction addEvent (obj, ev, func, b) {\n  if(obj.addEventListener) {\n    obj.addEventListener(ev, func, b);\n  } else {\n    obj.attachEvent("on" + ev, func);\n  }\n}\n\nfunction stopEvent(event) {\n    if(navigator.appName != "Microsoft Internet Explorer") {\n        event.stopPropagation();\n        event.preventDefault();\n    } else {\n        event.cancelBubble = true;\n        event.returnValue = false;\n    }\n}\n\nfunction init(event) {\n    var content = document.getElementById(''gallery-content'');\n    GalleryImages = content.getElementsByTagName(''img'');\n\n    if(GalleryImages.length < 2) return;\n    \n    for(var i = 1; i < GalleryImages.length; i ++) {\n        GalleryImages[i].style.display = ''none'';\n    }\n    GalleryImagesIndex = 0;\n    \n    GalleryPrevLink = document.createElement(''a'');\n    GalleryPrevLink.href = ''#'';\n    GalleryPrevLink.innerHTML = ''< Předchozí'';\n    addEvent(GalleryPrevLink, ''click'', galleryPrevLinkClick, false);\n    \n    GalleryNextLink = document.createElement(''a'');\n    GalleryNextLink.href = ''#'';\n    GalleryNextLink.innerHTML = ''Další >'';\n    addEvent(GalleryNextLink, ''click'', galleryNextLinkClick, false);\n    \n    GalleryCounter = document.createElement(''span'');\n    GalleryCounter.innerHTML = (GalleryImagesIndex + 1) + '' - '' + GalleryImages.length;\n    \n    GalleryCover = document.createElement(''div'');\n    GalleryCover.className = ''js-gallery-cover'';\n    content.parentNode.insertBefore(GalleryCover, content);\n    \n    GalleryCover.appendChild(GalleryPrevLink);\n    GalleryCover.appendChild(GalleryCounter);\n    GalleryCover.appendChild(GalleryNextLink);\n}\n\nfunction galleryPrevLinkClick(event) {\n    if(GalleryImagesIndex > 0) {\n        GalleryImages[GalleryImagesIndex].style.display = ''none'';\n        GalleryImages[--GalleryImagesIndex].style.display = '''';\n        GalleryCounter.innerHTML = (GalleryImagesIndex + 1) + '' - '' + GalleryImages.length;\n    }\n    stopEvent(event);\n}\n\nfunction galleryNextLinkClick(event) {\n    if(GalleryImagesIndex < GalleryImages.length - 1) {\n        GalleryImages[GalleryImagesIndex].style.display = ''none'';\n        GalleryImages[++GalleryImagesIndex].style.display = '''';\n        GalleryCounter.innerHTML = (GalleryImagesIndex + 1) + '' - '' + GalleryImages.length;\n    }\n    stopEvent(event);\n}', 1, 0, 0, 0, 0, 0, 0, 2, 18),
(32, 'Slimbox - style', '/* SLIMBOX */\n\n#lbOverlay {\n	position: fixed;\n	z-index: 9999;\n	left: 0;\n	top: 0;\n	width: 100%;\n	height: 100%;\n	background-color: #000;\n	cursor: pointer;\n}\n\n#lbCenter, #lbBottomContainer {\n	position: absolute;\n	z-index: 9999;\n	overflow: hidden;\n	background-color: #fff;\n}\n\n.lbLoading {\n	background: #fff url(~/file/12-loading-gif) no-repeat center;\n}\n\n#lbImage {\n	position: absolute;\n	left: 0;\n	top: 0;\n	border: 10px solid #fff;\n	background-repeat: no-repeat;\n}\n\n#lbPrevLink, #lbNextLink {\n	display: block;\n	position: absolute;\n	top: 0;\n	width: 50%;\n	outline: none;\n}\n\n#lbPrevLink {\n	left: 0;\n}\n\n#lbPrevLink:hover {\n	background: transparent url(/file/14-prevlabel-gif) no-repeat 0 15%;\n}\n\n#lbNextLink {\n	right: 0;\n}\n\n#lbNextLink:hover {\n	background: transparent url(/file/13-nextlabel-gif) no-repeat 100% 15%;\n}\n\n#lbBottom {\n	font-family: Verdana, Arial, Geneva, Helvetica, sans-serif;\n	font-size: 10px;\n	color: #666;\n	line-height: 1.4em;\n	text-align: left;\n	border: 10px solid #fff;\n	border-top-style: none;\n}\n\n#lbCloseLink {\n	display: block;\n	float: right;\n	width: 66px;\n	height: 22px;\n	background: transparent url(/file/11-closelabel-gif) no-repeat center;\n	margin: 5px 0;\n	outline: none;\n}\n\n#lbCaption, #lbNumber {\n	margin-right: 71px;\n}\n\n#lbCaption {\n	font-weight: bold;\n}\n', 1, 0, 0, 0, 0, 0, 0, 1, 19);
INSERT INTO `page_file` (`id`, `name`, `content`, `for_all`, `for_msie6`, `for_msie7`, `for_msie8`, `for_firefox`, `for_opera`, `for_safari`, `type`, `wp`) VALUES
(33, 'Slimbox - js', '//MooTools, <http://mootools.net>, My Object Oriented (JavaScript) Tools. Copyright (c) 2006-2008 Valerio Proietti, <http://mad4milk.net>, MIT Style License.\n\nvar MooTools={version:"1.2.1",build:"0d4845aab3d9a4fdee2f0d4a6dd59210e4b697cf"};var Native=function(k){k=k||{};var a=k.name;var i=k.legacy;var b=k.protect;var c=k.implement;var h=k.generics;var f=k.initialize;var g=k.afterImplement||function(){};var d=f||i;h=h!==false;d.constructor=Native;d.$family={name:"native"};if(i&&f){d.prototype=i.prototype}d.prototype.constructor=d;if(a){var e=a.toLowerCase();d.prototype.$family={name:e};Native.typize(d,e)}var j=function(n,l,o,m){if(!b||m||!n.prototype[l]){n.prototype[l]=o}if(h){Native.genericize(n,l,b)}g.call(n,l,o);return n};d.alias=function(n,l,o){if(typeof n=="string"){if((n=this.prototype[n])){return j(this,l,n,o)}}for(var m in n){this.alias(m,n[m],l)}return this};d.implement=function(m,l,o){if(typeof m=="string"){return j(this,m,l,o)}for(var n in m){j(this,n,m[n],l)}return this};if(c){d.implement(c)}return d};Native.genericize=function(b,c,a){if((!a||!b[c])&&typeof b.prototype[c]=="function"){b[c]=function(){var d=Array.prototype.slice.call(arguments);return b.prototype[c].apply(d.shift(),d)}}};Native.implement=function(d,c){for(var b=0,a=d.length;b<a;b++){d[b].implement(c)}};Native.typize=function(a,b){if(!a.type){a.type=function(c){return($type(c)===b)}}};(function(){var a={Array:Array,Date:Date,Function:Function,Number:Number,RegExp:RegExp,String:String};for(var h in a){new Native({name:h,initialize:a[h],protect:true})}var d={"boolean":Boolean,"native":Native,object:Object};for(var c in d){Native.typize(d[c],c)}var f={Array:["concat","indexOf","join","lastIndexOf","pop","push","reverse","shift","slice","sort","splice","toString","unshift","valueOf"],String:["charAt","charCodeAt","concat","indexOf","lastIndexOf","match","replace","search","slice","split","substr","substring","toLowerCase","toUpperCase","valueOf"]};for(var e in f){for(var b=f[e].length;b--;){Native.genericize(window[e],f[e][b],true)}}})();var Hash=new Native({name:"Hash",initialize:function(a){if($type(a)=="hash"){a=$unlink(a.getClean())}for(var b in a){this[b]=a[b]}return this}});Hash.implement({forEach:function(b,c){for(var a in this){if(this.hasOwnProperty(a)){b.call(c,this[a],a,this)}}},getClean:function(){var b={};for(var a in this){if(this.hasOwnProperty(a)){b[a]=this[a]}}return b},getLength:function(){var b=0;for(var a in this){if(this.hasOwnProperty(a)){b++}}return b}});Hash.alias("forEach","each");Array.implement({forEach:function(c,d){for(var b=0,a=this.length;b<a;b++){c.call(d,this[b],b,this)}}});Array.alias("forEach","each");function $A(c){if(c.item){var d=[];for(var b=0,a=c.length;b<a;b++){d[b]=c[b]}return d}return Array.prototype.slice.call(c)}function $arguments(a){return function(){return arguments[a]}}function $chk(a){return !!(a||a===0)}function $clear(a){clearTimeout(a);clearInterval(a);return null}function $defined(a){return(a!=undefined)}function $each(c,b,d){var a=$type(c);((a=="arguments"||a=="collection"||a=="array")?Array:Hash).each(c,b,d)}function $empty(){}function $extend(c,a){for(var b in (a||{})){c[b]=a[b]}return c}function $H(a){return new Hash(a)}function $lambda(a){return(typeof a=="function")?a:function(){return a}}function $merge(){var e={};for(var d=0,a=arguments.length;d<a;d++){var b=arguments[d];if($type(b)!="object"){continue}for(var c in b){var g=b[c],f=e[c];e[c]=(f&&$type(g)=="object"&&$type(f)=="object")?$merge(f,g):$unlink(g)}}return e}function $pick(){for(var b=0,a=arguments.length;b<a;b++){if(arguments[b]!=undefined){return arguments[b]}}return null}function $random(b,a){return Math.floor(Math.random()*(a-b+1)+b)}function $splat(b){var a=$type(b);return(a)?((a!="array"&&a!="arguments")?[b]:b):[]}var $time=Date.now||function(){return +new Date};function $try(){for(var b=0,a=arguments.length;b<a;b++){try{return arguments[b]()}catch(c){}}return null}function $type(a){if(a==undefined){return false}if(a.$family){return(a.$family.name=="number"&&!isFinite(a))?false:a.$family.name}if(a.nodeName){switch(a.nodeType){case 1:return"element";case 3:return(/\\S/).test(a.nodeValue)?"textnode":"whitespace"}}else{if(typeof a.length=="number"){if(a.callee){return"arguments"}else{if(a.item){return"collection"}}}}return typeof a}function $unlink(c){var b;switch($type(c)){case"object":b={};for(var e in c){b[e]=$unlink(c[e])}break;case"hash":b=new Hash(c);break;case"array":b=[];for(var d=0,a=c.length;d<a;d++){b[d]=$unlink(c[d])}break;default:return c}return b}var Browser=$merge({Engine:{name:"unknown",version:0},Platform:{name:(window.orientation!=undefined)?"ipod":(navigator.platform.match(/mac|win|linux/i)||["other"])[0].toLowerCase()},Features:{xpath:!!(document.evaluate),air:!!(window.runtime),query:!!(document.querySelector)},Plugins:{},Engines:{presto:function(){return(!window.opera)?false:((arguments.callee.caller)?960:((document.getElementsByClassName)?950:925))},trident:function(){return(!window.ActiveXObject)?false:((window.XMLHttpRequest)?5:4)},webkit:function(){return(navigator.taintEnabled)?false:((Browser.Features.xpath)?((Browser.Features.query)?525:420):419)},gecko:function(){return(document.getBoxObjectFor==undefined)?false:((document.getElementsByClassName)?19:18)}}},Browser||{});Browser.Platform[Browser.Platform.name]=true;Browser.detect=function(){for(var b in this.Engines){var a=this.Engines[b]();if(a){this.Engine={name:b,version:a};this.Engine[b]=this.Engine[b+a]=true;break}}return{name:b,version:a}};Browser.detect();Browser.Request=function(){return $try(function(){return new XMLHttpRequest()},function(){return new ActiveXObject("MSXML2.XMLHTTP")})};Browser.Features.xhr=!!(Browser.Request());Browser.Plugins.Flash=(function(){var a=($try(function(){return navigator.plugins["Shockwave Flash"].description},function(){return new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version")})||"0 r0").match(/\\d+/g);return{version:parseInt(a[0]||0+"."+a[1]||0),build:parseInt(a[2]||0)}})();function $exec(b){if(!b){return b}if(window.execScript){window.execScript(b)}else{var a=document.createElement("script");a.setAttribute("type","text/javascript");a[(Browser.Engine.webkit&&Browser.Engine.version<420)?"innerText":"text"]=b;document.head.appendChild(a);document.head.removeChild(a)}return b}Native.UID=1;var $uid=(Browser.Engine.trident)?function(a){return(a.uid||(a.uid=[Native.UID++]))[0]}:function(a){return a.uid||(a.uid=Native.UID++)};var Window=new Native({name:"Window",legacy:(Browser.Engine.trident)?null:window.Window,initialize:function(a){$uid(a);if(!a.Element){a.Element=$empty;if(Browser.Engine.webkit){a.document.createElement("iframe")}a.Element.prototype=(Browser.Engine.webkit)?window["[[DOMElement.prototype]]"]:{}}a.document.window=a;return $extend(a,Window.Prototype)},afterImplement:function(b,a){window[b]=Window.Prototype[b]=a}});Window.Prototype={$family:{name:"window"}};new Window(window);var Document=new Native({name:"Document",legacy:(Browser.Engine.trident)?null:window.Document,initialize:function(a){$uid(a);a.head=a.getElementsByTagName("head")[0];a.html=a.getElementsByTagName("html")[0];if(Browser.Engine.trident&&Browser.Engine.version<=4){$try(function(){a.execCommand("BackgroundImageCache",false,true)})}if(Browser.Engine.trident){a.window.attachEvent("onunload",function(){a.window.detachEvent("onunload",arguments.callee);a.head=a.html=a.window=null})}return $extend(a,Document.Prototype)},afterImplement:function(b,a){document[b]=Document.Prototype[b]=a}});Document.Prototype={$family:{name:"document"}};new Document(document);Array.implement({every:function(c,d){for(var b=0,a=this.length;b<a;b++){if(!c.call(d,this[b],b,this)){return false}}return true},filter:function(d,e){var c=[];for(var b=0,a=this.length;b<a;b++){if(d.call(e,this[b],b,this)){c.push(this[b])}}return c},clean:function(){return this.filter($defined)},indexOf:function(c,d){var a=this.length;for(var b=(d<0)?Math.max(0,a+d):d||0;b<a;b++){if(this[b]===c){return b}}return -1},map:function(d,e){var c=[];for(var b=0,a=this.length;b<a;b++){c[b]=d.call(e,this[b],b,this)}return c},some:function(c,d){for(var b=0,a=this.length;b<a;b++){if(c.call(d,this[b],b,this)){return true}}return false},associate:function(c){var d={},b=Math.min(this.length,c.length);for(var a=0;a<b;a++){d[c[a]]=this[a]}return d},link:function(c){var a={};for(var e=0,b=this.length;e<b;e++){for(var d in c){if(c[d](this[e])){a[d]=this[e];delete c[d];break}}}return a},contains:function(a,b){return this.indexOf(a,b)!=-1},extend:function(c){for(var b=0,a=c.length;b<a;b++){this.push(c[b])}return this},getLast:function(){return(this.length)?this[this.length-1]:null},getRandom:function(){return(this.length)?this[$random(0,this.length-1)]:null},include:function(a){if(!this.contains(a)){this.push(a)}return this},combine:function(c){for(var b=0,a=c.length;b<a;b++){this.include(c[b])}return this},erase:function(b){for(var a=this.length;a--;a){if(this[a]===b){this.splice(a,1)}}return this},empty:function(){this.length=0;return this},flatten:function(){var d=[];for(var b=0,a=this.length;b<a;b++){var c=$type(this[b]);if(!c){continue}d=d.concat((c=="array"||c=="collection"||c=="arguments")?Array.flatten(this[b]):this[b])}return d},hexToRgb:function(b){if(this.length!=3){return null}var a=this.map(function(c){if(c.length==1){c+=c}return c.toInt(16)});return(b)?a:"rgb("+a+")"},rgbToHex:function(d){if(this.length<3){return null}if(this.length==4&&this[3]==0&&!d){return"transparent"}var b=[];for(var a=0;a<3;a++){var c=(this[a]-0).toString(16);b.push((c.length==1)?"0"+c:c)}return(d)?b:"#"+b.join("")}});Function.implement({extend:function(a){for(var b in a){this[b]=a[b]}return this},create:function(b){var a=this;b=b||{};return function(d){var c=b.arguments;c=(c!=undefined)?$splat(c):Array.slice(arguments,(b.event)?1:0);if(b.event){c=[d||window.event].extend(c)}var e=function(){return a.apply(b.bind||null,c)};if(b.delay){return setTimeout(e,b.delay)}if(b.periodical){return setInterval(e,b.periodical)}if(b.attempt){return $try(e)}return e()}},run:function(a,b){return this.apply(b,$splat(a))},pass:function(a,b){return this.create({bind:b,arguments:a})},bind:function(b,a){return this.create({bind:b,arguments:a})},bindWithEvent:function(b,a){return this.create({bind:b,arguments:a,event:true})},attempt:function(a,b){return this.create({bind:b,arguments:a,attempt:true})()},delay:function(b,c,a){return this.create({bind:c,arguments:a,delay:b})()},periodical:function(c,b,a){return this.create({bind:b,arguments:a,periodical:c})()}});Number.implement({limit:function(b,a){return Math.min(a,Math.max(b,this))},round:function(a){a=Math.pow(10,a||0);return Math.round(this*a)/a},times:function(b,c){for(var a=0;a<this;a++){b.call(c,a,this)}},toFloat:function(){return parseFloat(this)},toInt:function(a){return parseInt(this,a||10)}});Number.alias("times","each");(function(b){var a={};b.each(function(c){if(!Number[c]){a[c]=function(){return Math[c].apply(null,[this].concat($A(arguments)))}}});Number.implement(a)})(["abs","acos","asin","atan","atan2","ceil","cos","exp","floor","log","max","min","pow","sin","sqrt","tan"]);String.implement({test:function(a,b){return((typeof a=="string")?new RegExp(a,b):a).test(this)},contains:function(a,b){return(b)?(b+this+b).indexOf(b+a+b)>-1:this.indexOf(a)>-1},trim:function(){return this.replace(/^\\s+|\\s+$/g,"")},clean:function(){return this.replace(/\\s+/g," ").trim()},camelCase:function(){return this.replace(/-\\D/g,function(a){return a.charAt(1).toUpperCase()})},hyphenate:function(){return this.replace(/[A-Z]/g,function(a){return("-"+a.charAt(0).toLowerCase())})},capitalize:function(){return this.replace(/\\b[a-z]/g,function(a){return a.toUpperCase()})},escapeRegExp:function(){return this.replace(/([-.*+?^${}()|[\\]\\/\\\\])/g,"\\\\$1")},toInt:function(a){return parseInt(this,a||10)},toFloat:function(){return parseFloat(this)},hexToRgb:function(b){var a=this.match(/^#?(\\w{1,2})(\\w{1,2})(\\w{1,2})$/);return(a)?a.slice(1).hexToRgb(b):null},rgbToHex:function(b){var a=this.match(/\\d{1,3}/g);return(a)?a.rgbToHex(b):null},stripScripts:function(b){var a="";var c=this.replace(/<script[^>]*>([\\s\\S]*?)<\\/script>/gi,function(){a+=arguments[1]+"\\n";return""});if(b===true){$exec(a)}else{if($type(b)=="function"){b(a,c)}}return c},substitute:function(a,b){return this.replace(b||(/\\\\?\\{([^{}]+)\\}/g),function(d,c){if(d.charAt(0)=="\\\\"){return d.slice(1)}return(a[c]!=undefined)?a[c]:""})}});Hash.implement({has:Object.prototype.hasOwnProperty,keyOf:function(b){for(var a in this){if(this.hasOwnProperty(a)&&this[a]===b){return a}}return null},hasValue:function(a){return(Hash.keyOf(this,a)!==null)},extend:function(a){Hash.each(a,function(c,b){Hash.set(this,b,c)},this);return this},combine:function(a){Hash.each(a,function(c,b){Hash.include(this,b,c)},this);return this},erase:function(a){if(this.hasOwnProperty(a)){delete this[a]}return this},get:function(a){return(this.hasOwnProperty(a))?this[a]:null},set:function(a,b){if(!this[a]||this.hasOwnProperty(a)){this[a]=b}return this},empty:function(){Hash.each(this,function(b,a){delete this[a]},this);return this},include:function(b,c){var a=this[b];if(a==undefined){this[b]=c}return this},map:function(b,c){var a=new Hash;Hash.each(this,function(e,d){a.set(d,b.call(c,e,d,this))},this);return a},filter:function(b,c){var a=new Hash;Hash.each(this,function(e,d){if(b.call(c,e,d,this)){a.set(d,e)}},this);return a},every:function(b,c){for(var a in this){if(this.hasOwnProperty(a)&&!b.call(c,this[a],a)){return false}}return true},some:function(b,c){for(var a in this){if(this.hasOwnProperty(a)&&b.call(c,this[a],a)){return true}}return false},getKeys:function(){var a=[];Hash.each(this,function(c,b){a.push(b)});return a},getValues:function(){var a=[];Hash.each(this,function(b){a.push(b)});return a},toQueryString:function(a){var b=[];Hash.each(this,function(f,e){if(a){e=a+"["+e+"]"}var d;switch($type(f)){case"object":d=Hash.toQueryString(f,e);break;case"array":var c={};f.each(function(h,g){c[g]=h});d=Hash.toQueryString(c,e);break;default:d=e+"="+encodeURIComponent(f)}if(f!=undefined){b.push(d)}});return b.join("&")}});Hash.alias({keyOf:"indexOf",hasValue:"contains"});var Event=new Native({name:"Event",initialize:function(a,f){f=f||window;var k=f.document;a=a||f.event;if(a.$extended){return a}this.$extended=true;var j=a.type;var g=a.target||a.srcElement;while(g&&g.nodeType==3){g=g.parentNode}if(j.test(/key/)){var b=a.which||a.keyCode;var m=Event.Keys.keyOf(b);if(j=="keydown"){var d=b-111;if(d>0&&d<13){m="f"+d}}m=m||String.fromCharCode(b).toLowerCase()}else{if(j.match(/(click|mouse|menu)/i)){k=(!k.compatMode||k.compatMode=="CSS1Compat")?k.html:k.body;var i={x:a.pageX||a.clientX+k.scrollLeft,y:a.pageY||a.clientY+k.scrollTop};var c={x:(a.pageX)?a.pageX-f.pageXOffset:a.clientX,y:(a.pageY)?a.pageY-f.pageYOffset:a.clientY};if(j.match(/DOMMouseScroll|mousewheel/)){var h=(a.wheelDelta)?a.wheelDelta/120:-(a.detail||0)/3}var e=(a.which==3)||(a.button==2);var l=null;if(j.match(/over|out/)){switch(j){case"mouseover":l=a.relatedTarget||a.fromElement;break;case"mouseout":l=a.relatedTarget||a.toElement}if(!(function(){while(l&&l.nodeType==3){l=l.parentNode}return true}).create({attempt:Browser.Engine.gecko})()){l=false}}}}return $extend(this,{event:a,type:j,page:i,client:c,rightClick:e,wheel:h,relatedTarget:l,target:g,code:b,key:m,shift:a.shiftKey,control:a.ctrlKey,alt:a.altKey,meta:a.metaKey})}});Event.Keys=new Hash({enter:13,up:38,down:40,left:37,right:39,esc:27,space:32,backspace:8,tab:9,"delete":46});Event.implement({stop:function(){return this.stopPropagation().preventDefault()},stopPropagation:function(){if(this.event.stopPropagation){this.event.stopPropagation()}else{this.event.cancelBubble=true}return this},preventDefault:function(){if(this.event.preventDefault){this.event.preventDefault()}else{this.event.returnValue=false}return this}});var Class=new Native({name:"Class",initialize:function(b){b=b||{};var a=function(){for(var e in this){if($type(this[e])!="function"){this[e]=$unlink(this[e])}}this.constructor=a;if(Class.prototyping){return this}var d=(this.initialize)?this.initialize.apply(this,arguments):this;if(this.options&&this.options.initialize){this.options.initialize.call(this)}return d};for(var c in Class.Mutators){if(!b[c]){continue}b=Class.Mutators[c](b,b[c]);delete b[c]}$extend(a,this);a.constructor=Class;a.prototype=b;return a}});Class.Mutators={Extends:function(c,a){Class.prototyping=a.prototype;var b=new a;delete b.parent;b=Class.inherit(b,c);delete Class.prototyping;return b},Implements:function(a,b){$splat(b).each(function(c){Class.prototying=c;$extend(a,($type(c)=="class")?new c:c);delete Class.prototyping});return a}};Class.extend({inherit:function(b,e){var a=arguments.callee.caller;for(var d in e){var c=e[d];var g=b[d];var f=$type(c);if(g&&f=="function"){if(c!=g){if(a){c.__parent=g;b[d]=c}else{Class.override(b,d,c)}}}else{if(f=="object"){b[d]=$merge(g,c)}else{b[d]=c}}}if(a){b.parent=function(){return arguments.callee.caller.__parent.apply(this,arguments)}}return b},override:function(b,a,e){var d=Class.prototyping;if(d&&b[a]!=d[a]){d=null}var c=function(){var f=this.parent;this.parent=d?d[a]:b[a];var g=e.apply(this,arguments);this.parent=f;return g};b[a]=c}});Class.implement({implement:function(){var a=this.prototype;$each(arguments,function(b){Class.inherit(a,b)});return this}});var Chain=new Class({$chain:[],chain:function(){this.$chain.extend(Array.flatten(arguments));return this},callChain:function(){return(this.$chain.length)?this.$chain.shift().apply(this,arguments):false},clearChain:function(){this.$chain.empty();return this}});var Events=new Class({$events:{},addEvent:function(c,b,a){c=Events.removeOn(c);if(b!=$empty){this.$events[c]=this.$events[c]||[];this.$events[c].include(b);if(a){b.internal=true}}return this},addEvents:function(a){for(var b in a){this.addEvent(b,a[b])}return this},fireEvent:function(c,b,a){c=Events.removeOn(c);if(!this.$events||!this.$events[c]){return this}this.$events[c].each(function(d){d.create({bind:this,delay:a,"arguments":b})()},this);return this},removeEvent:function(b,a){b=Events.removeOn(b);if(!this.$events[b]){return this}if(!a.internal){this.$events[b].erase(a)}return this},removeEvents:function(c){if($type(c)=="object"){for(var d in c){this.removeEvent(d,c[d])}return this}if(c){c=Events.removeOn(c)}for(var d in this.$events){if(c&&c!=d){continue}var b=this.$events[d];for(var a=b.length;a--;a){this.removeEvent(d,b[a])}}return this}});Events.removeOn=function(a){return a.replace(/^on([A-Z])/,function(b,c){return c.toLowerCase()})};var Options=new Class({setOptions:function(){this.options=$merge.run([this.options].extend(arguments));if(!this.addEvent){return this}for(var a in this.options){if($type(this.options[a])!="function"||!(/^on[A-Z]/).test(a)){continue}this.addEvent(a,this.options[a]);delete this.options[a]}return this}});var Element=new Native({name:"Element",legacy:window.Element,initialize:function(a,b){var c=Element.Constructors.get(a);if(c){return c(b)}if(typeof a=="string"){return document.newElement(a,b)}return $(a).set(b)},afterImplement:function(a,b){Element.Prototype[a]=b;if(Array[a]){return}Elements.implement(a,function(){var c=[],g=true;for(var e=0,d=this.length;e<d;e++){var f=this[e][a].apply(this[e],arguments);c.push(f);if(g){g=($type(f)=="element")}}return(g)?new Elements(c):c})}});Element.Prototype={$family:{name:"element"}};Element.Constructors=new Hash;var IFrame=new Native({name:"IFrame",generics:false,initialize:function(){var e=Array.link(arguments,{properties:Object.type,iframe:$defined});var c=e.properties||{};var b=$(e.iframe)||false;var d=c.onload||$empty;delete c.onload;c.id=c.name=$pick(c.id,c.name,b.id,b.name,"IFrame_"+$time());b=new Element(b||"iframe",c);var a=function(){var f=$try(function(){return b.contentWindow.location.host});if(f&&f==window.location.host){var g=new Window(b.contentWindow);new Document(b.contentWindow.document);$extend(g.Element.prototype,Element.Prototype)}d.call(b.contentWindow,b.contentWindow.document)};(window.frames[c.id])?a():b.addListener("load",a);return b}});var Elements=new Native({initialize:function(f,b){b=$extend({ddup:true,cash:true},b);f=f||[];if(b.ddup||b.cash){var g={},e=[];for(var c=0,a=f.length;c<a;c++){var d=$.element(f[c],!b.cash);if(b.ddup){if(g[d.uid]){continue}g[d.uid]=true}e.push(d)}f=e}return(b.cash)?$extend(f,this):f}});Elements.implement({filter:function(a,b){if(!a){return this}return new Elements(Array.filter(this,(typeof a=="string")?function(c){return c.match(a)}:a,b))}});Document.implement({newElement:function(a,b){if(Browser.Engine.trident&&b){["name","type","checked"].each(function(c){if(!b[c]){return}a+=" "+c+''="''+b[c]+''"'';if(c!="checked"){delete b[c]}});a="<"+a+">"}return $.element(this.createElement(a)).set(b)},newTextNode:function(a){return this.createTextNode(a)},getDocument:function(){return this},getWindow:function(){return this.window}});Window.implement({$:function(b,c){if(b&&b.$family&&b.uid){return b}var a=$type(b);return($[a])?$[a](b,c,this.document):null},$$:function(a){if(arguments.length==1&&typeof a=="string"){return this.document.getElements(a)}var f=[];var c=Array.flatten(arguments);for(var d=0,b=c.length;d<b;d++){var e=c[d];switch($type(e)){case"element":f.push(e);break;case"string":f.extend(this.document.getElements(e,true))}}return new Elements(f)},getDocument:function(){return this.document},getWindow:function(){return this}});$.string=function(c,b,a){c=a.getElementById(c);return(c)?$.element(c,b):null};$.element=function(a,d){$uid(a);if(!d&&!a.$family&&!(/^object|embed$/i).test(a.tagName)){var b=Element.Prototype;for(var c in b){a[c]=b[c]}}return a};$.object=function(b,c,a){if(b.toElement){return $.element(b.toElement(a),c)}return null};$.textnode=$.whitespace=$.window=$.document=$arguments(0);Native.implement([Element,Document],{getElement:function(a,b){return $(this.getElements(a,true)[0]||null,b)},getElements:function(a,d){a=a.split(",");var c=[];var b=(a.length>1);a.each(function(e){var f=this.getElementsByTagName(e.trim());(b)?c.extend(f):c=f},this);return new Elements(c,{ddup:b,cash:!d})}});(function(){var h={},f={};var i={input:"checked",option:"selected",textarea:(Browser.Engine.webkit&&Browser.Engine.version<420)?"innerHTML":"value"};var c=function(l){return(f[l]||(f[l]={}))};var g=function(n,l){if(!n){return}var m=n.uid;if(Browser.Engine.trident){if(n.clearAttributes){var q=l&&n.cloneNode(false);n.clearAttributes();if(q){n.mergeAttributes(q)}}else{if(n.removeEvents){n.removeEvents()}}if((/object/i).test(n.tagName)){for(var o in n){if(typeof n[o]=="function"){n[o]=$empty}}Element.dispose(n)}}if(!m){return}h[m]=f[m]=null};var d=function(){Hash.each(h,g);if(Browser.Engine.trident){$A(document.getElementsByTagName("object")).each(g)}if(window.CollectGarbage){CollectGarbage()}h=f=null};var j=function(n,l,s,m,p,r){var o=n[s||l];var q=[];while(o){if(o.nodeType==1&&(!m||Element.match(o,m))){if(!p){return $(o,r)}q.push(o)}o=o[l]}return(p)?new Elements(q,{ddup:false,cash:!r}):null};var e={html:"innerHTML","class":"className","for":"htmlFor",text:(Browser.Engine.trident||(Browser.Engine.webkit&&Browser.Engine.version<420))?"innerText":"textContent"};var b=["compact","nowrap","ismap","declare","noshade","checked","disabled","readonly","multiple","selected","noresize","defer"];var k=["value","accessKey","cellPadding","cellSpacing","colSpan","frameBorder","maxLength","readOnly","rowSpan","tabIndex","useMap"];Hash.extend(e,b.associate(b));Hash.extend(e,k.associate(k.map(String.toLowerCase)));var a={before:function(m,l){if(l.parentNode){l.parentNode.insertBefore(m,l)}},after:function(m,l){if(!l.parentNode){return}var n=l.nextSibling;(n)?l.parentNode.insertBefore(m,n):l.parentNode.appendChild(m)},bottom:function(m,l){l.appendChild(m)},top:function(m,l){var n=l.firstChild;(n)?l.insertBefore(m,n):l.appendChild(m)}};a.inside=a.bottom;Hash.each(a,function(l,m){m=m.capitalize();Element.implement("inject"+m,function(n){l(this,$(n,true));return this});Element.implement("grab"+m,function(n){l($(n,true),this);return this})});Element.implement({set:function(o,m){switch($type(o)){case"object":for(var n in o){this.set(n,o[n])}break;case"string":var l=Element.Properties.get(o);(l&&l.set)?l.set.apply(this,Array.slice(arguments,1)):this.setProperty(o,m)}return this},get:function(m){var l=Element.Properties.get(m);return(l&&l.get)?l.get.apply(this,Array.slice(arguments,1)):this.getProperty(m)},erase:function(m){var l=Element.Properties.get(m);(l&&l.erase)?l.erase.apply(this):this.removeProperty(m);return this},setProperty:function(m,n){var l=e[m];if(n==undefined){return this.removeProperty(m)}if(l&&b[m]){n=!!n}(l)?this[l]=n:this.setAttribute(m,""+n);return this},setProperties:function(l){for(var m in l){this.setProperty(m,l[m])}return this},getProperty:function(m){var l=e[m];var n=(l)?this[l]:this.getAttribute(m,2);return(b[m])?!!n:(l)?n:n||null},getProperties:function(){var l=$A(arguments);return l.map(this.getProperty,this).associate(l)},removeProperty:function(m){var l=e[m];(l)?this[l]=(l&&b[m])?false:"":this.removeAttribute(m);return this},removeProperties:function(){Array.each(arguments,this.removeProperty,this);return this},hasClass:function(l){return this.className.contains(l," ")},addClass:function(l){if(!this.hasClass(l)){this.className=(this.className+" "+l).clean()}return this},removeClass:function(l){this.className=this.className.replace(new RegExp("(^|\\\\s)"+l+"(?:\\\\s|$)"),"$1");return this},toggleClass:function(l){return this.hasClass(l)?this.removeClass(l):this.addClass(l)},adopt:function(){Array.flatten(arguments).each(function(l){l=$(l,true);if(l){this.appendChild(l)}},this);return this},appendText:function(m,l){return this.grab(this.getDocument().newTextNode(m),l)},grab:function(m,l){a[l||"bottom"]($(m,true),this);return this},inject:function(m,l){a[l||"bottom"](this,$(m,true));return this},replaces:function(l){l=$(l,true);l.parentNode.replaceChild(this,l);return this},wraps:function(m,l){m=$(m,true);return this.replaces(m).grab(m,l)},getPrevious:function(l,m){return j(this,"previousSibling",null,l,false,m)},getAllPrevious:function(l,m){return j(this,"previousSibling",null,l,true,m)},getNext:function(l,m){return j(this,"nextSibling",null,l,false,m)},getAllNext:function(l,m){return j(this,"nextSibling",null,l,true,m)},getFirst:function(l,m){return j(this,"nextSibling","firstChild",l,false,m)},getLast:function(l,m){return j(this,"previousSibling","lastChild",l,false,m)},getParent:function(l,m){return j(this,"parentNode",null,l,false,m)},getParents:function(l,m){return j(this,"parentNode",null,l,true,m)},getChildren:function(l,m){return j(this,"nextSibling","firstChild",l,true,m)},getWindow:function(){return this.ownerDocument.window},getDocument:function(){return this.ownerDocument},getElementById:function(o,n){var m=this.ownerDocument.getElementById(o);if(!m){return null}for(var l=m.parentNode;l!=this;l=l.parentNode){if(!l){return null}}return $.element(m,n)},getSelected:function(){return new Elements($A(this.options).filter(function(l){return l.selected}))},getComputedStyle:function(m){if(this.currentStyle){return this.currentStyle[m.camelCase()]}var l=this.getDocument().defaultView.getComputedStyle(this,null);return(l)?l.getPropertyValue([m.hyphenate()]):null},toQueryString:function(){var l=[];this.getElements("input, select, textarea",true).each(function(m){if(!m.name||m.disabled){return}var n=(m.tagName.toLowerCase()=="select")?Element.getSelected(m).map(function(o){return o.value}):((m.type=="radio"||m.type=="checkbox")&&!m.checked)?null:m.value;$splat(n).each(function(o){if(typeof o!="undefined"){l.push(m.name+"="+encodeURIComponent(o))}})});return l.join("&")},clone:function(o,l){o=o!==false;var r=this.cloneNode(o);var n=function(v,u){if(!l){v.removeAttribute("id")}if(Browser.Engine.trident){v.clearAttributes();v.mergeAttributes(u);v.removeAttribute("uid");if(v.options){var w=v.options,s=u.options;for(var t=w.length;t--;){w[t].selected=s[t].selected}}}var x=i[u.tagName.toLowerCase()];if(x&&u[x]){v[x]=u[x]}};if(o){var p=r.getElementsByTagName("*"),q=this.getElementsByTagName("*");for(var m=p.length;m--;){n(p[m],q[m])}}n(r,this);return $(r)},destroy:function(){Element.empty(this);Element.dispose(this);g(this,true);return null},empty:function(){$A(this.childNodes).each(function(l){Element.destroy(l)});return this},dispose:function(){return(this.parentNode)?this.parentNode.removeChild(this):this},hasChild:function(l){l=$(l,true);if(!l){return false}if(Browser.Engine.webkit&&Browser.Engine.version<420){return $A(this.getElementsByTagName(l.tagName)).contains(l)}return(this.contains)?(this!=l&&this.contains(l)):!!(this.compareDocumentPosition(l)&16)},match:function(l){return(!l||(l==this)||(Element.get(this,"tag")==l))}});Native.implement([Element,Window,Document],{addListener:function(o,n){if(o=="unload"){var l=n,m=this;n=function(){m.removeListener("unload",n);l()}}else{h[this.uid]=this}if(this.addEventListener){this.addEventListener(o,n,false)}else{this.attachEvent("on"+o,n)}return this},removeListener:function(m,l){if(this.removeEventListener){this.removeEventListener(m,l,false)}else{this.detachEvent("on"+m,l)}return this},retrieve:function(m,l){var o=c(this.uid),n=o[m];if(l!=undefined&&n==undefined){n=o[m]=l}return $pick(n)},store:function(m,l){var n=c(this.uid);n[m]=l;return this},eliminate:function(l){var m=c(this.uid);delete m[l];return this}});window.addListener("unload",d)})();Element.Properties=new Hash;Element.Properties.style={set:function(a){this.style.cssText=a},get:function(){return this.style.cssText},erase:function(){this.style.cssText=""}};Element.Properties.tag={get:function(){return this.tagName.toLowerCase()}};Element.Properties.html=(function(){var c=document.createElement("div");var a={table:[1,"<table>","</table>"],select:[1,"<select>","</select>"],tbody:[2,"<table><tbody>","</tbody></table>"],tr:[3,"<table><tbody><tr>","</tr></tbody></table>"]};a.thead=a.tfoot=a.tbody;var b={set:function(){var e=Array.flatten(arguments).join("");var f=Browser.Engine.trident&&a[this.get("tag")];if(f){var g=c;g.innerHTML=f[1]+e+f[2];for(var d=f[0];d--;){g=g.firstChild}this.empty().adopt(g.childNodes)}else{this.innerHTML=e}}};b.erase=b.set;return b})();if(Browser.Engine.webkit&&Browser.Engine.version<420){Element.Properties.text={get:function(){if(this.innerText){return this.innerText}var a=this.ownerDocument.newElement("div",{html:this.innerHTML}).inject(this.ownerDocument.body);var b=a.innerText;a.destroy();return b}}}Element.Properties.events={set:function(a){this.addEvents(a)}};Native.implement([Element,Window,Document],{addEvent:function(e,g){var h=this.retrieve("events",{});h[e]=h[e]||{keys:[],values:[]};if(h[e].keys.contains(g)){return this}h[e].keys.push(g);var f=e,a=Element.Events.get(e),c=g,i=this;if(a){if(a.onAdd){a.onAdd.call(this,g)}if(a.condition){c=function(j){if(a.condition.call(this,j)){return g.call(this,j)}return true}}f=a.base||f}var d=function(){return g.call(i)};var b=Element.NativeEvents[f];if(b){if(b==2){d=function(j){j=new Event(j,i.getWindow());if(c.call(i,j)===false){j.stop()}}}this.addListener(f,d)}h[e].values.push(d);return this},removeEvent:function(c,b){var a=this.retrieve("events");if(!a||!a[c]){return this}var f=a[c].keys.indexOf(b);if(f==-1){return this}a[c].keys.splice(f,1);var e=a[c].values.splice(f,1)[0];var d=Element.Events.get(c);if(d){if(d.onRemove){d.onRemove.call(this,b)}c=d.base||c}return(Element.NativeEvents[c])?this.removeListener(c,e):this},addEvents:function(a){for(var b in a){this.addEvent(b,a[b])}return this},removeEvents:function(a){if($type(a)=="object"){for(var c in a){this.removeEvent(c,a[c])}return this}var b=this.retrieve("events");if(!b){return this}if(!a){for(var c in b){this.removeEvents(c)}this.eliminate("events")}else{if(b[a]){while(b[a].keys[0]){this.removeEvent(a,b[a].keys[0])}b[a]=null}}return this},fireEvent:function(d,b,a){var c=this.retrieve("events");if(!c||!c[d]){return this}c[d].keys.each(function(e){e.create({bind:this,delay:a,"arguments":b})()},this);return this},cloneEvents:function(d,a){d=$(d);var c=d.retrieve("events");if(!c){return this}if(!a){for(var b in c){this.cloneEvents(d,b)}}else{if(c[a]){c[a].keys.each(function(e){this.addEvent(a,e)},this)}}return this}});Element.NativeEvents={click:2,dblclick:2,mouseup:2,mousedown:2,contextmenu:2,mousewheel:2,DOMMouseScroll:2,mouseover:2,mouseout:2,mousemove:2,selectstart:2,selectend:2,keydown:2,keypress:2,keyup:2,focus:2,blur:2,change:2,reset:2,select:2,submit:2,load:1,unload:1,beforeunload:2,resize:1,move:1,DOMContentLoaded:1,readystatechange:1,error:1,abort:1,scroll:1};(function(){var a=function(b){var c=b.relatedTarget;if(c==undefined){return true}if(c===false){return false}return($type(this)!="document"&&c!=this&&c.prefix!="xul"&&!this.hasChild(c))};Element.Events=new Hash({mouseenter:{base:"mouseover",condition:a},mouseleave:{base:"mouseout",condition:a},mousewheel:{base:(Browser.Engine.gecko)?"DOMMouseScroll":"mousewheel"}})})();Element.Properties.styles={set:function(a){this.setStyles(a)}};Element.Properties.opacity={set:function(a,b){if(!b){if(a==0){if(this.style.visibility!="hidden"){this.style.visibility="hidden"}}else{if(this.style.visibility!="visible"){this.style.visibility="visible"}}}if(!this.currentStyle||!this.currentStyle.hasLayout){this.style.zoom=1}if(Browser.Engine.trident){this.style.filter=(a==1)?"":"alpha(opacity="+a*100+")"}this.style.opacity=a;this.store("opacity",a)},get:function(){return this.retrieve("opacity",1)}};Element.implement({setOpacity:function(a){return this.set("opacity",a,true)},getOpacity:function(){return this.get("opacity")},setStyle:function(b,a){switch(b){case"opacity":return this.set("opacity",parseFloat(a));case"float":b=(Browser.Engine.trident)?"styleFloat":"cssFloat"}b=b.camelCase();if($type(a)!="string"){var c=(Element.Styles.get(b)||"@").split(" ");a=$splat(a).map(function(e,d){if(!c[d]){return""}return($type(e)=="number")?c[d].replace("@",Math.round(e)):e}).join(" ")}else{if(a==String(Number(a))){a=Math.round(a)}}this.style[b]=a;return this},getStyle:function(g){switch(g){case"opacity":return this.get("opacity");case"float":g=(Browser.Engine.trident)?"styleFloat":"cssFloat"}g=g.camelCase();var a=this.style[g];if(!$chk(a)){a=[];for(var f in Element.ShortStyles){if(g!=f){continue}for(var e in Element.ShortStyles[f]){a.push(this.getStyle(e))}return a.join(" ")}a=this.getComputedStyle(g)}if(a){a=String(a);var c=a.match(/rgba?\\([\\d\\s,]+\\)/);if(c){a=a.replace(c[0],c[0].rgbToHex())}}if(Browser.Engine.presto||(Browser.Engine.trident&&!$chk(parseInt(a)))){if(g.test(/^(height|width)$/)){var b=(g=="width")?["left","right"]:["top","bottom"],d=0;b.each(function(h){d+=this.getStyle("border-"+h+"-width").toInt()+this.getStyle("padding-"+h).toInt()},this);return this["offset"+g.capitalize()]-d+"px"}if((Browser.Engine.presto)&&String(a).test("px")){return a}if(g.test(/(border(.+)Width|margin|padding)/)){return"0px"}}return a},setStyles:function(b){for(var a in b){this.setStyle(a,b[a])}return this},getStyles:function(){var a={};Array.each(arguments,function(b){a[b]=this.getStyle(b)},this);return a}});Element.Styles=new Hash({left:"@px",top:"@px",bottom:"@px",right:"@px",width:"@px",height:"@px",maxWidth:"@px",maxHeight:"@px",minWidth:"@px",minHeight:"@px",backgroundColor:"rgb(@, @, @)",backgroundPosition:"@px @px",color:"rgb(@, @, @)",fontSize:"@px",letterSpacing:"@px",lineHeight:"@px",clip:"rect(@px @px @px @px)",margin:"@px @px @px @px",padding:"@px @px @px @px",border:"@px @ rgb(@, @, @) @px @ rgb(@, @, @) @px @ rgb(@, @, @)",borderWidth:"@px @px @px @px",borderStyle:"@ @ @ @",borderColor:"rgb(@, @, @) rgb(@, @, @) rgb(@, @, @) rgb(@, @, @)",zIndex:"@",zoom:"@",fontWeight:"@",textIndent:"@px",opacity:"@"});Element.ShortStyles={margin:{},padding:{},border:{},borderWidth:{},borderStyle:{},borderColor:{}};["Top","Right","Bottom","Left"].each(function(g){var f=Element.ShortStyles;var b=Element.Styles;["margin","padding"].each(function(h){var i=h+g;f[h][i]=b[i]="@px"});var e="border"+g;f.border[e]=b[e]="@px @ rgb(@, @, @)";var d=e+"Width",a=e+"Style",c=e+"Color";f[e]={};f.borderWidth[d]=f[e][d]=b[d]="@px";f.borderStyle[a]=f[e][a]=b[a]="@";f.borderColor[c]=f[e][c]=b[c]="rgb(@, @, @)"});(function(){Element.implement({scrollTo:function(h,i){if(b(this)){this.getWindow().scrollTo(h,i)}else{this.scrollLeft=h;this.scrollTop=i}return this},getSize:function(){if(b(this)){return this.getWindow().getSize()}return{x:this.offsetWidth,y:this.offsetHeight}},getScrollSize:function(){if(b(this)){return this.getWindow().getScrollSize()}return{x:this.scrollWidth,y:this.scrollHeight}},getScroll:function(){if(b(this)){return this.getWindow().getScroll()}return{x:this.scrollLeft,y:this.scrollTop}},getScrolls:function(){var i=this,h={x:0,y:0};while(i&&!b(i)){h.x+=i.scrollLeft;h.y+=i.scrollTop;i=i.parentNode}return h},getOffsetParent:function(){var h=this;if(b(h)){return null}if(!Browser.Engine.trident){return h.offsetParent}while((h=h.parentNode)&&!b(h)){if(d(h,"position")!="static"){return h}}return null},getOffsets:function(){if(Browser.Engine.trident){var l=this.getBoundingClientRect(),j=this.getDocument().documentElement;return{x:l.left+j.scrollLeft-j.clientLeft,y:l.top+j.scrollTop-j.clientTop}}var i=this,h={x:0,y:0};if(b(this)){return h}while(i&&!b(i)){h.x+=i.offsetLeft;h.y+=i.offsetTop;if(Browser.Engine.gecko){if(!f(i)){h.x+=c(i);h.y+=g(i)}var k=i.parentNode;if(k&&d(k,"overflow")!="visible"){h.x+=c(k);h.y+=g(k)}}else{if(i!=this&&Browser.Engine.webkit){h.x+=c(i);h.y+=g(i)}}i=i.offsetParent}if(Browser.Engine.gecko&&!f(this)){h.x-=c(this);h.y-=g(this)}return h},getPosition:function(k){if(b(this)){return{x:0,y:0}}var l=this.getOffsets(),i=this.getScrolls();var h={x:l.x-i.x,y:l.y-i.y};var j=(k&&(k=$(k)))?k.getPosition():{x:0,y:0};return{x:h.x-j.x,y:h.y-j.y}},getCoordinates:function(j){if(b(this)){return this.getWindow().getCoordinates()}var h=this.getPosition(j),i=this.getSize();var k={left:h.x,top:h.y,width:i.x,height:i.y};k.right=k.left+k.width;k.bottom=k.top+k.height;return k},computePosition:function(h){return{left:h.x-e(this,"margin-left"),top:h.y-e(this,"margin-top")}},position:function(h){return this.setStyles(this.computePosition(h))}});Native.implement([Document,Window],{getSize:function(){var i=this.getWindow();if(Browser.Engine.presto||Browser.Engine.webkit){return{x:i.innerWidth,y:i.innerHeight}}var h=a(this);return{x:h.clientWidth,y:h.clientHeight}},getScroll:function(){var i=this.getWindow();var h=a(this);return{x:i.pageXOffset||h.scrollLeft,y:i.pageYOffset||h.scrollTop}},getScrollSize:function(){var i=a(this);var h=this.getSize();return{x:Math.max(i.scrollWidth,h.x),y:Math.max(i.scrollHeight,h.y)}},getPosition:function(){return{x:0,y:0}},getCoordinates:function(){var h=this.getSize();return{top:0,left:0,bottom:h.y,right:h.x,height:h.y,width:h.x}}});var d=Element.getComputedStyle;function e(h,i){return d(h,i).toInt()||0}function f(h){return d(h,"-moz-box-sizing")=="border-box"}function g(h){return e(h,"border-top-width")}function c(h){return e(h,"border-left-width")}function b(h){return(/^(?:body|html)$/i).test(h.tagName)}function a(h){var i=h.getDocument();return(!i.compatMode||i.compatMode=="CSS1Compat")?i.html:i.body}})();Native.implement([Window,Document,Element],{getHeight:function(){return this.getSize().y},getWidth:function(){return this.getSize().x},getScrollTop:function(){return this.getScroll().y},getScrollLeft:function(){return this.getScroll().x},getScrollHeight:function(){return this.getScrollSize().y},getScrollWidth:function(){return this.getScrollSize().x},getTop:function(){return this.getPosition().y},getLeft:function(){return this.getPosition().x}});Element.Events.domready={onAdd:function(a){if(Browser.loaded){a.call(this)}}};(function(){var b=function(){if(Browser.loaded){return}Browser.loaded=true;window.fireEvent("domready");document.fireEvent("domready")};if(Browser.Engine.trident){var a=document.createElement("div");(function(){($try(function(){a.doScroll("left");return $(a).inject(document.body).set("html","temp").dispose()}))?b():arguments.callee.delay(50)})()}else{if(Browser.Engine.webkit&&Browser.Engine.version<525){(function(){(["loaded","complete"].contains(document.readyState))?b():arguments.callee.delay(50)})()}else{window.addEvent("load",b);document.addEvent("DOMContentLoaded",b)}}})();var Fx=new Class({Implements:[Chain,Events,Options],options:{fps:50,unit:false,duration:500,link:"ignore"},initialize:function(a){this.subject=this.subject||this;this.setOptions(a);this.options.duration=Fx.Durations[this.options.duration]||this.options.duration.toInt();var b=this.options.wait;if(b===false){this.options.link="cancel"}},getTransition:function(){return function(a){return -(Math.cos(Math.PI*a)-1)/2}},step:function(){var a=$time();if(a<this.time+this.options.duration){var b=this.transition((a-this.time)/this.options.duration);this.set(this.compute(this.from,this.to,b))}else{this.set(this.compute(this.from,this.to,1));this.complete()}},set:function(a){return a},compute:function(c,b,a){return Fx.compute(c,b,a)},check:function(a){if(!this.timer){return true}switch(this.options.link){case"cancel":this.cancel();return true;case"chain":this.chain(a.bind(this,Array.slice(arguments,1)));return false}return false},start:function(b,a){if(!this.check(arguments.callee,b,a)){return this}this.from=b;this.to=a;this.time=0;this.transition=this.getTransition();this.startTimer();this.onStart();return this},complete:function(){if(this.stopTimer()){this.onComplete()}return this},cancel:function(){if(this.stopTimer()){this.onCancel()}return this},onStart:function(){this.fireEvent("start",this.subject)},onComplete:function(){this.fireEvent("complete",this.subject);if(!this.callChain()){this.fireEvent("chainComplete",this.subject)}},onCancel:function(){this.fireEvent("cancel",this.subject).clearChain()},pause:function(){this.stopTimer();return this},resume:function(){this.startTimer();return this},stopTimer:function(){if(!this.timer){return false}this.time=$time()-this.time;this.timer=$clear(this.timer);return true},startTimer:function(){if(this.timer){return false}this.time=$time()-this.time;this.timer=this.step.periodical(Math.round(1000/this.options.fps),this);return true}});Fx.compute=function(c,b,a){return(b-c)*a+c};Fx.Durations={"short":250,normal:500,"long":1000};Fx.CSS=new Class({Extends:Fx,prepare:function(d,e,b){b=$splat(b);var c=b[1];if(!$chk(c)){b[1]=b[0];b[0]=d.getStyle(e)}var a=b.map(this.parse);return{from:a[0],to:a[1]}},parse:function(a){a=$lambda(a)();a=(typeof a=="string")?a.split(" "):$splat(a);return a.map(function(c){c=String(c);var b=false;Fx.CSS.Parsers.each(function(f,e){if(b){return}var d=f.parse(c);if($chk(d)){b={value:d,parser:f}}});b=b||{value:c,parser:Fx.CSS.Parsers.String};return b})},compute:function(d,c,b){var a=[];(Math.min(d.length,c.length)).times(function(e){a.push({value:d[e].parser.compute(d[e].value,c[e].value,b),parser:d[e].parser})});a.$family={name:"fx:css:value"};return a},serve:function(c,b){if($type(c)!="fx:css:value"){c=this.parse(c)}var a=[];c.each(function(d){a=a.concat(d.parser.serve(d.value,b))});return a},render:function(a,d,c,b){a.setStyle(d,this.serve(c,b))},search:function(a){if(Fx.CSS.Cache[a]){return Fx.CSS.Cache[a]}var b={};Array.each(document.styleSheets,function(e,d){var c=e.href;if(c&&c.contains("://")&&!c.contains(document.domain)){return}var f=e.rules||e.cssRules;Array.each(f,function(j,g){if(!j.style){return}var h=(j.selectorText)?j.selectorText.replace(/^\\w+/,function(i){return i.toLowerCase()}):null;if(!h||!h.test("^"+a+"$")){return}Element.Styles.each(function(k,i){if(!j.style[i]||Element.ShortStyles[i]){return}k=String(j.style[i]);b[i]=(k.test(/^rgb/))?k.rgbToHex():k})})});return Fx.CSS.Cache[a]=b}});Fx.CSS.Cache={};Fx.CSS.Parsers=new Hash({Color:{parse:function(a){if(a.match(/^#[0-9a-f]{3,6}$/i)){return a.hexToRgb(true)}return((a=a.match(/(\\d+),\\s*(\\d+),\\s*(\\d+)/)))?[a[1],a[2],a[3]]:false},compute:function(c,b,a){return c.map(function(e,d){return Math.round(Fx.compute(c[d],b[d],a))})},serve:function(a){return a.map(Number)}},Number:{parse:parseFloat,compute:Fx.compute,serve:function(b,a){return(a)?b+a:b}},String:{parse:$lambda(false),compute:$arguments(1),serve:$arguments(0)}});Fx.Tween=new Class({Extends:Fx.CSS,initialize:function(b,a){this.element=this.subject=$(b);this.parent(a)},set:function(b,a){if(arguments.length==1){a=b;b=this.property||this.options.property}this.render(this.element,b,a,this.options.unit);return this},start:function(c,e,d){if(!this.check(arguments.callee,c,e,d)){return this}var b=Array.flatten(arguments);this.property=this.options.property||b.shift();var a=this.prepare(this.element,this.property,b);return this.parent(a.from,a.to)}});Element.Properties.tween={set:function(a){var b=this.retrieve("tween");if(b){b.cancel()}return this.eliminate("tween").store("tween:options",$extend({link:"cancel"},a))},get:function(a){if(a||!this.retrieve("tween")){if(a||!this.retrieve("tween:options")){this.set("tween",a)}this.store("tween",new Fx.Tween(this,this.retrieve("tween:options")))}return this.retrieve("tween")}};Element.implement({tween:function(a,c,b){this.get("tween").start(arguments);return this},fade:function(c){var e=this.get("tween"),d="opacity",a;c=$pick(c,"toggle");switch(c){case"in":e.start(d,1);break;case"out":e.start(d,0);break;case"show":e.set(d,1);break;case"hide":e.set(d,0);break;case"toggle":var b=this.retrieve("fade:flag",this.get("opacity")==1);e.start(d,(b)?0:1);this.store("fade:flag",!b);a=true;break;default:e.start(d,arguments)}if(!a){this.eliminate("fade:flag")}return this},highlight:function(c,a){if(!a){a=this.retrieve("highlight:original",this.getStyle("background-color"));a=(a=="transparent")?"#fff":a}var b=this.get("tween");b.start("background-color",c||"#ffff88",a).chain(function(){this.setStyle("background-color",this.retrieve("highlight:original"));b.callChain()}.bind(this));return this}});Fx.Morph=new Class({Extends:Fx.CSS,initialize:function(b,a){this.element=this.subject=$(b);this.parent(a)},set:function(a){if(typeof a=="string"){a=this.search(a)}for(var b in a){this.render(this.element,b,a[b],this.options.unit)}return this},compute:function(e,d,c){var a={};for(var b in e){a[b]=this.parent(e[b],d[b],c)}return a},start:function(b){if(!this.check(arguments.callee,b)){return this}if(typeof b=="string"){b=this.search(b)}var e={},d={};for(var c in b){var a=this.prepare(this.element,c,b[c]);e[c]=a.from;d[c]=a.to}return this.parent(e,d)}});Element.Properties.morph={set:function(a){var b=this.retrieve("morph");if(b){b.cancel()}return this.eliminate("morph").store("morph:options",$extend({link:"cancel"},a))},get:function(a){if(a||!this.retrieve("morph")){if(a||!this.retrieve("morph:options")){this.set("morph",a)}this.store("morph",new Fx.Morph(this,this.retrieve("morph:options")))}return this.retrieve("morph")}};Element.implement({morph:function(a){this.get("morph").start(a);return this}});\n\n// JavaScript Document\n/*!\n	Slimbox v1.69 - The ultimate lightweight Lightbox clone\n	(c) 2007-2009 Christophe Beyls <http://www.digitalia.be>\n	MIT-style license.\n*/\n\nvar Slimbox = (function() {\n\n	// Global variables, accessible to Slimbox only\n	var win = window, ie6 = Browser.Engine.trident4, options, images, activeImage = -1, activeURL, prevImage, nextImage, compatibleOverlay, middle, centerWidth, centerHeight,\n\n	// Preload images\n	preload = {}, preloadPrev = new Image(), preloadNext = new Image(),\n\n	// DOM elements\n	overlay, center, image, sizer, prevLink, nextLink, bottomContainer, bottom, caption, number,\n\n	// Effects\n	fxOverlay, fxResize, fxImage, fxBottom;\n\n	/*\n		Initialization\n	*/\n\n	win.addEvent("domready", function() {\n		// Append the Slimbox HTML code at the bottom of the document\n		$(document.body).adopt(\n			$$(\n				overlay = new Element("div", {id: "lbOverlay", events: {click: close}}),\n				center = new Element("div", {id: "lbCenter"}),\n				bottomContainer = new Element("div", {id: "lbBottomContainer"})\n			).setStyle("display", "none")\n		);\n\n		image = new Element("div", {id: "lbImage"}).injectInside(center).adopt(\n			sizer = new Element("div", {styles: {position: "relative"}}).adopt(\n				prevLink = new Element("a", {id: "lbPrevLink", href: "#", events: {click: previous}}),\n				nextLink = new Element("a", {id: "lbNextLink", href: "#", events: {click: next}})\n			)\n		);\n\n		bottom = new Element("div", {id: "lbBottom"}).injectInside(bottomContainer).adopt(\n			new Element("a", {id: "lbCloseLink", href: "#", events: {click: close}}),\n			caption = new Element("div", {id: "lbCaption"}),\n			number = new Element("div", {id: "lbNumber"}),\n			new Element("div", {styles: {clear: "both"}})\n		);\n	});\n\n\n	/*\n		Internal functions\n	*/\n\n	function position() {\n		var scroll = win.getScroll(), size = win.getSize();\n		$$(center, bottomContainer).setStyle("left", scroll.x + (size.x / 2));\n		if (compatibleOverlay) overlay.setStyles({left: scroll.x, top: scroll.y, width: size.x, height: size.y});\n	}\n\n	function setup(open) {\n		["object", ie6 ? "select" : "embed"].forEach(function(tag) {\n			Array.forEach(document.getElementsByTagName(tag), function(el) {\n				if (open) el._slimbox = el.style.visibility;\n				el.style.visibility = open ? "hidden" : el._slimbox;\n			});\n		});\n\n		overlay.style.display = open ? "" : "none";\n\n		var fn = open ? "addEvent" : "removeEvent";\n		win[fn]("scroll", position)[fn]("resize", position);\n		document[fn]("keydown", keyDown);\n	}\n\n	function keyDown(event) {\n		var code = event.code;\n		// Prevent default keyboard action (like navigating inside the page)\n		return options.closeKeys.contains(code) ? close()\n			: options.nextKeys.contains(code) ? next()\n			: options.previousKeys.contains(code) ? previous()\n			: false;\n	}\n\n	function previous() {\n		return changeImage(prevImage);\n	}\n\n	function next() {\n		return changeImage(nextImage);\n	}\n\n	function changeImage(imageIndex) {\n		if (imageIndex >= 0) {\n			activeImage = imageIndex;\n			activeURL = images[imageIndex][0];\n			prevImage = (activeImage || (options.loop ? images.length : 0)) - 1;\n			nextImage = ((activeImage + 1) % images.length) || (options.loop ? 0 : -1);\n\n			stop();\n			center.className = "lbLoading";\n\n			preload = new Image();\n			preload.onload = animateBox;\n			preload.src = activeURL;\n		}\n\n		return false;\n	}\n\n	function animateBox() {\n		center.className = "";\n		fxImage.set(0);\n		image.setStyles({backgroundImage: "url(" + activeURL + ")", display: ""});\n		sizer.setStyle("width", preload.width);\n		$$(sizer, prevLink, nextLink).setStyle("height", preload.height);\n\n		caption.set("html", images[activeImage][1] || "");\n		number.set("html", (((images.length > 1) && options.counterText) || "").replace(/{x}/, activeImage + 1).replace(/{y}/, images.length));\n\n		if (prevImage >= 0) preloadPrev.src = images[prevImage][0];\n		if (nextImage >= 0) preloadNext.src = images[nextImage][0];\n\n		centerWidth = image.offsetWidth;\n		centerHeight = image.offsetHeight;\n		var top = Math.max(0, middle - (centerHeight / 2)), fn;\n		if (center.offsetHeight != centerHeight) {\n			fxResize.start({height: centerHeight, top: top});\n		}\n		if (center.offsetWidth != centerWidth) {\n			fxResize.start({width: centerWidth, marginLeft: -centerWidth/2});\n		}\n		fn = function() {\n			bottomContainer.setStyles({width: centerWidth, top: top + centerHeight, marginLeft: -centerWidth/2, visibility: "hidden", display: ""});\n			fxImage.start(1);\n		};\n		if (fxResize.check(fn)) fn();\n	}\n\n	function animateCaption() {\n		if (prevImage >= 0) prevLink.style.display = "";\n		if (nextImage >= 0) nextLink.style.display = "";\n		fxBottom.set(-bottom.offsetHeight).start(0);\n		bottomContainer.style.visibility = "";\n	}\n\n	function stop() {\n		preload.onload = $empty;\n		preload.src = preloadPrev.src = preloadNext.src = activeURL;\n		fxResize.cancel();\n		fxImage.cancel();\n		fxBottom.cancel();\n		$$(prevLink, nextLink, image, bottomContainer).setStyle("display", "none");\n	}\n\n	function close() {\n		if (activeImage >= 0) {\n			stop();\n			activeImage = prevImage = nextImage = -1;\n			center.style.display = "none";\n			fxOverlay.cancel().chain(setup).start(0);\n		}\n\n		return false;\n	}\n\n\n	/*\n		API\n	*/\n\n	Element.implement({\n		slimbox: function(_options, linkMapper) {\n			// The processing of a single element is similar to the processing of a collection with a single element\n			$$(this).slimbox(_options, linkMapper);\n\n			return this;\n		}\n	});\n\n	Elements.implement({\n		/*\n			options:	Optional options object, see Slimbox.open()\n			linkMapper:	Optional function taking a link DOM element and an index as arguments and returning an array containing 2 elements:\n					the image URL and the image caption (may contain HTML)\n			linksFilter:	Optional function taking a link DOM element and an index as arguments and returning true if the element is part of\n					the image collection that will be shown on click, false if not. "this" refers to the element that was clicked.\n					This function must always return true when the DOM element argument is "this".\n		*/\n		slimbox: function(_options, linkMapper, linksFilter) {\n			linkMapper = linkMapper || function(el) {\n				return [el.href, el.title];\n			};\n\n			linksFilter = linksFilter || function() {\n				return true;\n			};\n\n			var links = this;\n\n			links.removeEvents("click").addEvent("click", function() {\n				// Build the list of images that will be displayed\n				var filteredLinks = links.filter(linksFilter, this);\n				return Slimbox.open(filteredLinks.map(linkMapper), filteredLinks.indexOf(this), _options);\n			});\n\n			return links;\n		}\n	});\n\n	return {\n		open: function(_images, startImage, _options) {\n			options = $extend({\n				loop: false,				// Allows to navigate between first and last images\n				overlayOpacity: 0.8,			// 1 is opaque, 0 is completely transparent (change the color in the CSS file)\n				overlayFadeDuration: 400,		// Duration of the overlay fade-in and fade-out animations (in milliseconds)\n				resizeDuration: 400,			// Duration of each of the box resize animations (in milliseconds)\n				resizeTransition: false,		// false uses the mootools default transition\n				initialWidth: 250,			// Initial width of the box (in pixels)\n				initialHeight: 250,			// Initial height of the box (in pixels)\n				imageFadeDuration: 400,			// Duration of the image fade-in animation (in milliseconds)\n				captionAnimationDuration: 400,		// Duration of the caption animation (in milliseconds)\n				counterText: "Image {x} of {y}",	// Translate or change as you wish, or set it to false to disable counter text for image groups\n				closeKeys: [27, 88, 67],		// Array of keycodes to close Slimbox, default: Esc (27), ''x'' (88), ''c'' (67)\n				previousKeys: [37, 80],			// Array of keycodes to navigate to the previous image, default: Left arrow (37), ''p'' (80)\n				nextKeys: [39, 78]			// Array of keycodes to navigate to the next image, default: Right arrow (39), ''n'' (78)\n			}, _options || {});\n\n			// Setup effects\n			fxOverlay = new Fx.Tween(overlay, {property: "opacity", duration: options.overlayFadeDuration});\n			fxResize = new Fx.Morph(center, $extend({duration: options.resizeDuration, link: "chain"}, options.resizeTransition ? {transition: options.resizeTransition} : {}));\n			fxImage = new Fx.Tween(image, {property: "opacity", duration: options.imageFadeDuration, onComplete: animateCaption});\n			fxBottom = new Fx.Tween(bottom, {property: "margin-top", duration: options.captionAnimationDuration});\n\n			// The function is called for a single image, with URL and Title as first two arguments\n			if (typeof _images == "string") {\n				_images = [[_images, startImage]];\n				startImage = 0;\n			}\n\n			middle = win.getScrollTop() + (win.getHeight() / 2);\n			centerWidth = options.initialWidth;\n			centerHeight = options.initialHeight;\n			center.setStyles({top: Math.max(0, middle - (centerHeight / 2)), width: centerWidth, height: centerHeight, marginLeft: -centerWidth/2, display: ""});\n			compatibleOverlay = ie6 || (overlay.currentStyle && (overlay.currentStyle.position != "fixed"));\n			if (compatibleOverlay) overlay.style.position = "absolute";\n			fxOverlay.set(0).start(options.overlayOpacity);\n			position();\n			setup(1);\n\n			images = _images;\n			options.loop = options.loop && (images.length > 1);\n			return changeImage(startImage);\n		}\n	};\n\n})();\n\n// AUTOLOAD CODE BLOCK (MAY BE CHANGED OR REMOVED)\nSlimbox.scanPage = function() {\n    $$(document.links).filter(function(el) {\n        return el.rel && el.rel.test(/^lightbox/i);\n    }).slimbox({/* Put custom options here */}, null, function(el) {\n        return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));\n    });\n};\nwindow.addEvent("domready", Slimbox.scanPage); ', 1, 0, 0, 0, 0, 0, 0, 2, 19);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `page_file_inc`
--

INSERT INTO `page_file_inc` (`file_id`, `page_id`, `language_id`) VALUES
(4, 47, 1),
(8, 65, 1),
(9, 73, 1),
(13, 86, 1),
(14, 95, 1),
(15, 95, 1),
(16, 95, 1),
(17, 95, 1),
(18, 95, 1),
(21, 100, 1),
(27, 152, 1),
(28, 158, 1),
(29, 158, 1),
(30, 166, 2),
(31, 169, 1),
(32, 127, 1),
(33, 127, 1);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
(43, 2, 102),
(43, 2, 103),
(43, 3, 101),
(44, 1, 102),
(44, 1, 103),
(44, 2, 101),
(45, 1, 102),
(45, 1, 103),
(45, 2, 101),
(46, 1, 102),
(46, 1, 103),
(46, 2, 101),
(47, 1, 102),
(47, 1, 103),
(47, 3, 101),
(52, 1, 102),
(52, 1, 103),
(52, 2, 101),
(53, 1, 102),
(53, 1, 103),
(53, 2, 101),
(54, 1, 102),
(54, 1, 103),
(54, 3, 101),
(56, 1, 102),
(56, 1, 103),
(56, 2, 101),
(60, 1, 102),
(60, 1, 103),
(60, 3, 101),
(62, 1, 102),
(62, 1, 103),
(62, 3, 101),
(63, 1, 102),
(63, 1, 103),
(63, 3, 101),
(64, 1, 102),
(64, 1, 103),
(64, 3, 101),
(65, 1, 102),
(65, 1, 103),
(65, 3, 101),
(66, 1, 102),
(66, 1, 103),
(66, 3, 101),
(67, 1, 102),
(67, 1, 103),
(67, 3, 101),
(68, 1, 102),
(68, 1, 103),
(68, 3, 101),
(69, 1, 102),
(69, 1, 103),
(69, 3, 101),
(70, 1, 102),
(70, 1, 103),
(70, 3, 101),
(71, 1, 102),
(71, 1, 103),
(71, 3, 101),
(73, 1, 102),
(73, 1, 103),
(73, 3, 101),
(74, 1, 102),
(74, 1, 103),
(74, 3, 101),
(75, 1, 102),
(75, 1, 103),
(75, 3, 101),
(83, 1, 102),
(83, 1, 103),
(83, 3, 101),
(84, 1, 102),
(84, 1, 103),
(84, 3, 101),
(85, 1, 102),
(85, 1, 103),
(85, 3, 101),
(86, 1, 102),
(86, 1, 103),
(86, 3, 101),
(87, 1, 102),
(87, 1, 103),
(87, 3, 101),
(88, 1, 102),
(88, 1, 103),
(88, 3, 101),
(89, 1, 102),
(89, 1, 103),
(89, 3, 101),
(90, 1, 102),
(90, 1, 103),
(90, 3, 101),
(91, 1, 102),
(91, 1, 103),
(91, 3, 101),
(94, 1, 102),
(94, 1, 103),
(94, 3, 101),
(95, 1, 102),
(95, 1, 103),
(95, 3, 101),
(96, 1, 102),
(96, 1, 103),
(96, 3, 101),
(97, 1, 102),
(97, 1, 103),
(97, 3, 101),
(98, 1, 102),
(98, 1, 103),
(98, 3, 101),
(99, 1, 102),
(99, 1, 103),
(99, 3, 101),
(100, 1, 102),
(100, 1, 103),
(100, 3, 101),
(101, 1, 102),
(101, 1, 103),
(101, 3, 101),
(102, 1, 102),
(102, 1, 103),
(102, 3, 101),
(103, 1, 102),
(103, 1, 103),
(103, 3, 101),
(104, 1, 102),
(104, 1, 103),
(104, 3, 101),
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
(112, 1, 102),
(112, 1, 103),
(112, 3, 101),
(113, 1, 102),
(113, 1, 103),
(113, 3, 101),
(114, 1, 102),
(114, 1, 103),
(114, 3, 101),
(115, 1, 102),
(115, 1, 103),
(115, 3, 101),
(116, 1, 102),
(116, 1, 103),
(116, 3, 101),
(117, 1, 102),
(117, 1, 103),
(117, 3, 101),
(118, 1, 102),
(118, 1, 103),
(118, 3, 101),
(119, 1, 102),
(119, 1, 103),
(119, 3, 101),
(120, 1, 102),
(120, 1, 103),
(120, 3, 101),
(121, 1, 102),
(121, 1, 103),
(121, 3, 101),
(122, 1, 102),
(122, 1, 103),
(122, 3, 101),
(123, 1, 102),
(123, 1, 103),
(123, 3, 101),
(124, 1, 102),
(124, 1, 103),
(124, 3, 101),
(125, 1, 102),
(125, 1, 103),
(125, 2, 101),
(126, 1, 102),
(126, 1, 103),
(126, 2, 101),
(127, 1, 102),
(127, 1, 103),
(127, 3, 101),
(128, 1, 102),
(128, 1, 103),
(128, 3, 101),
(129, 1, 102),
(129, 1, 103),
(129, 3, 101),
(130, 1, 102),
(130, 1, 103),
(130, 3, 101),
(131, 1, 102),
(131, 1, 103),
(131, 3, 101),
(132, 1, 102),
(132, 1, 103),
(132, 3, 101),
(133, 1, 102),
(133, 1, 103),
(133, 3, 101),
(134, 1, 102),
(134, 1, 103),
(134, 3, 101),
(135, 1, 102),
(135, 1, 103),
(135, 3, 101),
(136, 1, 102),
(136, 1, 103),
(136, 3, 101),
(137, 1, 102),
(137, 1, 103),
(137, 3, 101),
(138, 1, 102),
(138, 1, 103),
(138, 3, 101),
(139, 1, 102),
(139, 1, 103),
(139, 3, 101),
(140, 1, 102),
(140, 1, 103),
(140, 3, 101),
(141, 1, 102),
(141, 1, 103),
(141, 3, 101),
(142, 1, 102),
(142, 1, 103),
(142, 3, 101),
(143, 1, 102),
(143, 1, 103),
(143, 3, 101),
(145, 1, 102),
(145, 1, 103),
(145, 3, 101),
(146, 1, 102),
(146, 1, 103),
(146, 3, 101),
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
(151, 1, 102),
(151, 1, 103),
(151, 3, 101),
(152, 1, 102),
(152, 1, 103),
(152, 12, 101),
(153, 1, 102),
(153, 1, 103),
(153, 3, 101),
(154, 1, 102),
(154, 1, 103),
(154, 12, 101),
(155, 1, 102),
(155, 1, 103),
(155, 3, 101),
(156, 1, 102),
(156, 1, 103),
(156, 3, 101),
(157, 1, 102),
(157, 1, 103),
(157, 3, 101),
(158, 1, 102),
(158, 1, 103),
(158, 12, 101),
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
(166, 2, 102),
(166, 2, 103),
(166, 3, 101),
(167, 2, 102),
(167, 2, 103),
(167, 3, 101),
(168, 2, 102),
(168, 2, 103),
(168, 3, 101),
(169, 2, 102),
(169, 2, 103),
(169, 3, 101),
(170, 2, 102),
(170, 2, 103),
(170, 3, 101),
(171, 2, 102),
(171, 2, 103),
(171, 3, 101),
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
  `property_name` tinytext NOT NULL,
  `property_value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pair_uid_property`
--

INSERT INTO `pair_uid_property` (`id`, `uid`, `property_name`, `property_value`) VALUES
(1, 3, 'dir-id', 16),
(2, 4, 'dir-id', 24);

-- --------------------------------------------------------

--
-- Table structure for table `personal_note`
--

DROP TABLE IF EXISTS `personal_note`;
CREATE TABLE IF NOT EXISTS `personal_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` mediumtext NOT NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

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
  `name` tinytext NOT NULL,
  `value` tinytext NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=22 ;

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
  `content` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `template`
--

INSERT INTO `template` (`id`, `content`) VALUES
(1, '<div class="article">\r\n  <div class="article-timestamp">\r\n    <a href="<artc:link />"><artc:time /> - <artc:date /></a>\r\n  </div>\r\n  <div class="article-head">\r\n    <artc:head />\r\n  </div>\r\n</div>'),
(2, '<div class="article">\r\n  <div class="article-timestamp">\r\n    <artc:time /> <strong><artc:date /></strong>\r\n  </div>\r\n  <div class="article-content">\r\n    <artc:content />\r\n  </div>\r\n  <div class="article-author">\r\n    <artc:author />\r\n  </div>\r\n</div>'),
(8, '<div class="article">\r\n  <div class="article-timestamp">\r\n    <a href="<artc:link />"><artc:date /></a>\r\n  </div>\r\n</div>'),
(10, '<style type="text/css">\r\n\r\n.counter {\r\n  width: 200px;\r\n}\r\n\r\n.counter div {\r\n  clear: both;\r\n}\r\n\r\n.counter span.col-name {\r\n  float: left;\r\n}\r\n\r\n.counter span.col-value {\r\n  float: right;\r\n}\r\n\r\n</style>\r\n<div class="counter">\r\n  <div class="counter-all">\r\n    <span class="col-name">\r\n      All:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:all />\r\n    </span>\r\n  </div>\r\n  <div class="counter-user">\r\n    <span class="col-name">\r\n      You:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:user />\r\n    </span>\r\n  </div>\r\n  <div class="counter-visitors">\r\n    <span class="col-name">\r\n      Visitors:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitors />\r\n    </span>\r\n  </div>\r\n  <div class="counter-today">\r\n    <span class="col-name">\r\n      Today:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsToday />\r\n    </span>\r\n  </div>\r\n  <div class="counter-hour">\r\n    <span class="col-name">\r\n      Last hour:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsHour />\r\n    </span>\r\n  </div>\r\n  <div class="counter-online">\r\n    <span class="col-name">\r\n      Online:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsOnline />\r\n    </span>\r\n  </div>\r\n</div>'),
(11, '<div class="article">\r\n  <artc:showDetail defaultArticleId="1" articleLangId="2" templateId="2" showError="false" />\r\n</div>'),
(12, 'Nevim ne :)'),
(13, '<div class="projection">\r\n  <div class="name">\r\n    <hp:projection type="name" />\r\n  </div>\r\n  <div class="subname">\r\n    <hp:projection type="subname" />\r\n  </div>\r\n  <div class="value">\r\n    <hp:projection type="value" />\r\n  </div>\r\n</div>'),
(14, '<div class="reference">\r\n  <div class="name">\r\n    <hp:reference type="name" />\r\n  </div>\r\n  <div class="subname">\r\n    <hp:reference type="subname" />\r\n  </div>\r\n  <div class="type-name">\r\n    <hp:reference type="type-name" />\r\n  </div>\r\n</div>'),
(15, '<login:info />\r\n<login:logout pageId="73" group="test" />'),
(16, '<login:form group="test" pageId="73" />'),
(17, '<div class="counter">\r\n  <div class="counter-all">\r\n    <span class="col-name">\r\n      All:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:all />\r\n    </span>\r\n    <div class="clear"></div>\r\n  </div>\r\n  <div class="counter-user">\r\n    <span class="col-name">\r\n      You:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:user />\r\n    </span>\r\n    <div class="clear"></div>\r\n  </div>\r\n  <div class="counter-visitors">\r\n    <span class="col-name">\r\n      Visitors:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitors />\r\n    </span>\r\n    <div class="clear"></div>\r\n  </div>\r\n  <div class="counter-today">\r\n    <span class="col-name">\r\n      Today:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsToday />\r\n    </span>\r\n    <div class="clear"></div>\r\n  </div>\r\n  <div class="counter-hour">\r\n    <span class="col-name">\r\n      Last hour:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsHour />\r\n    </span>\r\n    <div class="clear"></div>\r\n  </div>\r\n  <div class="counter-online">\r\n    <span class="col-name">\r\n      Online:\r\n    </span>\r\n    <span class="col-value">\r\n      <cn:visitorsOnline />\r\n    </span>\r\n    <div class="clear"></div>\r\n  </div>\r\n</div>'),
(18, '<div class="article">\r\n  <artc:showDetail defaultArticleId="9" articleLangId="2" templateId="19" showError="false" />\r\n</div>'),
(19, '<div class="article">\r\n  <div class="article-timestamp">\r\n    <artc:time /> <strong><artc:date /></strong><!-- - <artc:name />-->\r\n  </div>\r\n  <!--<div class="article-head">\r\n    <artc:head />\r\n  </div>-->\r\n  <div class="article-content">\r\n    <artc:content />\r\n  </div>\r\n  <div class="article-author">\r\n    <artc:author />\r\n  </div>\r\n  <div class="clear"></div>\r\n</div>'),
(20, '<tr>\r\n  <td><sport:team field="i" /></td>\r\n  <td><sport:team field="name" /></td>\r\n  <td><sport:team field="matches" /></td>\r\n  <td><sport:team field="wins" /></td>\r\n  <td><sport:team field="draws" /></td>\r\n  <td><sport:team field="loses" /></td>\r\n  <td><sport:team field="s_score" /></td>\r\n  <td><sport:team field="r_score" /></td>\r\n  <td><sport:team field="points" /></td>\r\n</tr>'),
(22, '<tr class="<sport:match field="row" /> r<sport:match field="i" />">\r\n  <td> <sport:team field="name" match="home" /> </td><td> <sport:team field="name" match="away" /> </td>\r\n  <td> <sport:match field="h_score" /> : </td><td> <sport:match field="a_score" /> </td>\r\n  <td> ( <sport:match field="h_shoots" /> : </td><td> <sport:match field="a_shoots" /> )</td>\r\n  <td> [ <sport:match field="h_penalty" /> : </td><td> <sport:match field="a_penalty" /> ]</td>\r\n</tr>'),
(27, '<tr>\r\n  <td><sport:player field="i" /></td>\r\n  <td><sport:player field="name" /> <sport:player field="surname" /></td>\r\n  <td><sport:team field="name" /></td>\r\n  <td><sport:player field="season_matches" /></td>\r\n  <td><sport:player field="season_percentage" /></td>\r\n</tr>'),
(23, '<div class="round">\r\n  <div class="round-no">\r\n    <h1>Kolo č. <sport:match field="round" scope="session" /></h1>\r\n  </div>\r\n  <div class="machtes-in-round">\r\n    <table>\r\n      <sport:matches templateId="22" sorting="DESC" />\r\n    </table>\r\n  </div>\r\n</div>'),
(24, '<p>\r\n<sport:player field="number" /> - \r\n<sport:player field="name" /> <sport:player field="surname" />, \r\n<sport:player field="birthyear" /> - \r\n<sport:player field="total_matches" /> /\r\n<sport:player field="total_points" /> (\r\n<sport:player field="total_goals" /> +\r\n<sport:player field="total_assists" /> )\r\n</p>'),
(25, '<tr>\r\n  <td><sport:player field="i" /></td>\r\n  <td><sport:player field="name" /> <sport:player field="surname" /></td>\r\n  <td><sport:team field="name" /></td>\r\n  <td><sport:player field="season_matches" /></td>\r\n  <td><sport:player field="season_points" /></td>\r\n  <td>( <sport:player field="season_goals" /> + <sport:player field="season_assists" /> )</td>\r\n</tr>'),
(26, '<sport:season field="start_year" /> - <sport:season field="end_year" /><br />\r\n<sport:rounds templateId="23" sorting="DESC" />\r\n<hr />'),
(28, '<div class="player" onclick="showPlayerDetail(event, <sport:player field="id" />, <sport:player field="position" />);">\r\n<!--<div class="player" onclick="alert(''Hello!'');">-->\r\n  <div class="number"><sport:player field="number" /> </div>\r\n  <div class="name"><sport:player field="name" /> <sport:player field="surname" /></div>\r\n  <div class="clear"></div>\r\n</div>\r\n<div class="player-info" style="display: none;">\r\n\r\n</div>'),
(29, '<div class="player-info">\r\n  <table>\r\n    <sport:seasons templateId="30" sorting="desc" />\r\n  </table>\r\n</div>'),
(30, '<tr>\r\n  <td><sport:season field="start_year" /> - <sport:season field="end_year" /></td>\r\n  <td><sport:player field="season_matches" errMsg="-" /></td>\r\n  <td><sport:player field="season_goals" errMsg="-" /></td>\r\n  <td><sport:player field="season_assists" errMsg="-" /></td>\r\n  <td><sport:player field="season_points" errMsg="-" /></td>\r\n  <td><sport:player field="season_penalty" errMsg="-" /></td>\r\n</tr>'),
(31, '<tr>\r\n  <td><sport:season field="start_year" /> - <sport:season field="end_year" /></td>\r\n  <td><sport:player field="season_matches" errMsg="-" /></td>\r\n  <td><sport:player field="season_shoots" errMsg="-" /></td>\r\n  <td><sport:player field="season_goals" errMsg="-" /></td>\r\n  <td><sport:player field="season_average" errMsg="-" /></td>\r\n  <td><sport:player field="season_percentage" errMsg="-" /></td>\r\n  <td><sport:player field="season_assists" errMsg="-" /></td>\r\n  <td><sport:player field="season_penalty" errMsg="-" /></td>\r\n</tr>'),
(32, '<div class="player" onclick="showGolmanDetail(event, <sport:player field="id" />);">\r\n<!--<div class="player" onclick="alert(''Hello!'');">-->\r\n  <div class="number"><sport:player field="number" /> </div>\r\n  <div class="name"><sport:player field="name" /> <sport:player field="surname" /></div>\r\n  <div class="clear"></div>\r\n</div>\r\n<div class="player-info" style="display: none;">\r\n\r\n</div>'),
(33, 'Hello world Template ;)'),
(34, '<style type="text/css">\n    .ie-login-template {\n        position: absolute;\n        left: 450px;\n        top: 100px;\n        width: 200px;\n        font-weight: bold;\n        padding: 10px;\n        background: #bbbbbb;\n        border: 4px solid #a81616;\n    }\n</style>\n<div class="ie-login-template">\n    We recommended you not to use Internet Explorer for working with this system. <br />Try <a target="_blank" href="http://www.firefox.com/">Firefox</a>\n</div>'),
(35, '<strong>Search filter</strong>\n\n<pgng:name type="input" />\n\n<pgng:id type="input" />\n\n<pgng:pageurl type="input" />\n\n<pgng:timestamp type="input" />'),
(36, '<tr>\n    <td><pgng:id pageId="pgng:pageId" langId="pgng:language" type="value" /></td>\n    <td><pgng:name pageId="pgng:pageId" langId="pgng:language" type="value" /></td>\n    <td>\n        <pgng:actionEdit pageId="pgng:pageId" langId="pgng:language" type="image" detailPageId="171" />\n        <pgng:actionAddsub parentPageId="pgng:pageId" langId="pgng:language" type="image" detailPageId="171" />\n        <pgng:actionDelete pageId="pgng:pageId" langId="pgng:language" type="image" />\n    </td>\n</tr>'),
(38, 'Testing');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `template_right`
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
(11, 3, 101),
(12, 2, 102),
(12, 2, 103),
(12, 3, 101),
(13, 2, 102),
(13, 2, 103),
(13, 3, 101),
(14, 2, 102),
(14, 2, 103),
(14, 3, 101),
(15, 1, 102),
(15, 1, 103),
(15, 3, 101),
(16, 2, 102),
(16, 2, 103),
(16, 3, 101),
(17, 2, 102),
(17, 2, 103),
(17, 3, 101),
(18, 2, 102),
(18, 2, 103),
(18, 3, 101),
(19, 2, 102),
(19, 2, 103),
(19, 3, 101),
(20, 2, 102),
(20, 2, 103),
(20, 3, 101),
(22, 2, 102),
(22, 2, 103),
(22, 3, 101),
(23, 2, 102),
(23, 2, 103),
(23, 3, 101),
(24, 2, 102),
(24, 2, 103),
(24, 3, 101),
(25, 2, 102),
(25, 2, 103),
(25, 3, 101),
(26, 2, 102),
(26, 2, 103),
(26, 3, 101),
(27, 2, 102),
(27, 2, 103),
(27, 3, 101),
(28, 2, 102),
(28, 2, 103),
(28, 3, 101),
(29, 2, 102),
(29, 2, 103),
(29, 3, 101),
(30, 2, 102),
(30, 2, 103),
(30, 3, 101),
(31, 2, 102),
(31, 2, 103),
(31, 3, 101),
(32, 2, 102),
(32, 2, 103),
(32, 3, 101),
(33, 2, 102),
(33, 2, 103),
(33, 3, 101),
(34, 2, 102),
(34, 2, 103),
(34, 3, 101),
(35, 2, 102),
(35, 2, 103),
(35, 3, 101),
(36, 2, 102),
(36, 2, 103),
(36, 3, 101),
(38, 2, 102),
(38, 2, 103),
(38, 3, 101);

-- --------------------------------------------------------

--
-- Table structure for table `urlcache`
--

DROP TABLE IF EXISTS `urlcache`;
CREATE TABLE IF NOT EXISTS `urlcache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_url_def` tinytext COLLATE latin1_general_ci NOT NULL,
  `project_url` tinytext COLLATE latin1_general_ci NOT NULL,
  `url_def` tinytext COLLATE latin1_general_ci NOT NULL,
  `url` tinytext COLLATE latin1_general_ci NOT NULL,
  `page-ids` tinytext COLLATE latin1_general_ci NOT NULL,
  `language_id` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL DEFAULT '-1',
  `lastcache` int(11) NOT NULL DEFAULT '0',
  `wp` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=250 ;

--
-- Dumping data for table `urlcache`
--

INSERT INTO `urlcache` (`id`, `project_url_def`, `project_url`, `url_def`, `url`, `page-ids`, `language_id`, `cachetime`, `lastcache`, `wp`) VALUES
(182, 'smm.papayateam.cz/', 'smm.papayateam.cz', 'in/page-manager', 'in/page-manager', '2-150-5-6', 1, -1, 0, 6),
(203, 'smm.papayateam.cz/admin', 'smm.papayateam.cz/admin', 'login', 'login', '2-4', 1, -1, 0, 6),
(5, 'web:language.testing.webprojects.localhost/testing', 'cs.testing.webprojects.localhost/testing', 'parsing/url/next/page/web:language', 'parsing/url/next/page/cs', '121-123-124', 1, -1, 0, 8),
(202, 'smm.papayateam.cz/admin', 'smm.papayateam.cz/admin', 'in/system-setup/system-properties', 'in/system-setup/system-properties', '2-150-5-147-149', 1, -1, 0, 6),
(201, 'smm.papayateam.cz/admin', 'smm.papayateam.cz/admin', 'in/page-manager', 'in/page-manager', '2-150-5-6', 1, -1, 0, 6),
(80, 'vh4.webprojects.localhost/', 'vh4.webprojects.localhost', 'menu/about-fbm/about-authors', 'menu/about-fbm/about-authors', '131-141-142', 1, -1, 0, 18),
(9, 'galerie.webprojects.localhost/', 'galerie.webprojects.localhost', '', '', '127', 1, -1, 0, 19),
(79, 'vh4.webprojects.localhost/', 'vh4.webprojects.localhost', 'menu/about-fbm/about-game', 'menu/about-fbm/about-game', '131-141-143', 1, -1, 0, 18),
(78, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'login', 'login', '2-4', 1, -1, 0, 6),
(13, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', '', '', '95-103-104', 1, -1, 0, 17),
(14, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'home', 'home', '95-103-97', 1, -1, 0, 17),
(15, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'aktuality', 'aktuality', '95-103-98', 1, -1, 0, 17),
(16, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'hraci', 'hraci', '95-103-100', 1, -1, 0, 17),
(17, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'guestbook', 'guestbook', '95-103-101', 1, -1, 0, 17),
(18, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'dido-liga', 'dido-liga', '95-103-99', 1, -1, 0, 17),
(19, 'vh2.webprojects.localhost/', 'vh2.webprojects.localhost', 'parsing/url/next/page/web:language', 'parsing/url/next/page/cs', '121-123-124', 1, -1, 0, 8),
(20, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'parsing/url/testing/web:language', 'cs/parsing/url/testing/cs', '121', 2, -1, 0, 8),
(21, 'web:language.testing.webprojects.localhost/testing', 'cs.testing.webprojects.localhost/testing', 'parsing/url/testing/web:language', 'cs/parsing/url/testing/cs', '121', 2, -1, 0, 8),
(77, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-project-manager/edit', 'in/web-project-manager/edit', '2-150-5-25-27', 1, -1, 0, 6),
(76, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-project-manager', 'in/web-project-manager', '2-150-5-25-26', 1, -1, 0, 6),
(75, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/guestbook-manager', 'in/guestbook-manager', '2-150-5-17', 1, -1, 0, 6),
(74, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/user-manager', 'in/user-manager', '2-150-5-9-53', 1, -1, 0, 6),
(73, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/file-manager', 'in/file-manager', '2-150-5-8', 1, -1, 0, 6),
(72, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/article-manager', 'in/article-manager', '2-150-5-16-40', 1, -1, 0, 6),
(71, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/template-manager', 'in/template-manager', '2-150-5-44-45', 1, -1, 0, 6),
(70, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/text-file-manager', 'in/text-file-manager', '2-150-5-7', 1, -1, 0, 6),
(69, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/sport/table', 'in/sport/table', '2-150-5-105-111', 1, -1, 0, 6),
(68, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/sport/matches', 'in/sport/matches', '2-150-5-105-110', 1, -1, 0, 6),
(34, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'sponzori', 'sponzori', '95-103-102', 1, -1, 0, 17),
(35, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'info/player', 'info/player', '118-119', 1, -1, 0, 17),
(36, 'papaya.webprojects.localhost/', 'papaya.webprojects.localhost', 'info/golman', 'info/golman', '118-120', 1, -1, 0, 17),
(37, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'pp/obj1', 'pp/obj1', '83-84', 1, -1, 0, 8),
(38, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'pp/obj2', 'pp/obj2', '83-85', 1, -1, 0, 8),
(67, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/sport/players', 'in/sport/players', '2-150-5-105-109', 1, -1, 0, 6),
(49, 'vh3.webprojects.localhost/', 'vh3.webprojects.localhost', 'some-page', 'some-page', '130', 1, -1, 0, 18),
(48, 'vh4.webprojects.localhost/', 'vh4.webprojects.localhost', 'some-page', 'some-page', '130', 1, -1, 0, 18),
(246, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-settings/url-cache', 'in/web-settings/url-cache', '2-150-5-23-174', 1, -1, 0, 6),
(45, 'vh1.webprojects.localhost/hotproject', 'vh1.webprojects.localhost/hotproject', '', '', '145', 1, -1, 0, 20),
(46, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'frames', 'frames', '65', 1, -1, 0, 8),
(47, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'DirectLinkGallery', 'DirectLinkGallery', '146', 1, -1, 0, 8),
(66, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/sport/teams', 'in/sport/teams', '2-150-5-105-108', 1, -1, 0, 6),
(61, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-settings', 'in/web-settings', '2-150-5-23', 1, -1, 0, 6),
(62, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/page-manager', 'in/page-manager', '2-150-5-6', 1, -1, 0, 6),
(63, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/hint', 'in/hint', '2-150-5-125-126', 1, -1, 0, 6),
(64, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/system-setup/system-properties', 'in/system-setup/system-properties', '2-150-5-147-149', 1, -1, 0, 6),
(65, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/sport/seasons', 'in/sport/seasons', '2-150-5-105-107', 1, -1, 0, 6),
(181, 'smm.papayateam.cz/', 'smm.papayateam.cz', 'in', 'in', '2-150-5-56', 1, -1, 0, 6),
(180, 'smm.papayateam.cz/', 'smm.papayateam.cz', 'login', 'login', '2-4', 1, -1, 0, 6),
(88, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-project-manager/select', 'in/web-project-manager/select', '2-150-5-25-28', 1, -1, 0, 6),
(89, 'vh4.webprojects.localhost/ajax-testing', 'vh4.webprojects.localhost/ajax-testing', '', '', '151', 1, -1, 0, 21),
(178, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', '', '', '47', 1, -1, 0, 8),
(94, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in', 'in', '2-150-5-56', 1, -1, 0, 6),
(95, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', '', '', '2-3', 1, -1, 0, 6),
(96, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/template-manager/edit', 'in/template-manager/edit', '2-150-5-44-46', 1, -1, 0, 6),
(97, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/system-setup', 'in/system-setup', '2-150-5-147-148', 1, -1, 0, 6),
(98, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/article-manager/edit-article', 'in/article-manager/edit-article', '2-150-5-16-41', 1, -1, 0, 6),
(99, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/article-manager/lines', 'in/article-manager/lines', '2-150-5-16-39', 1, -1, 0, 6),
(100, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/article-manager/edit-line', 'in/article-manager/edit-line', '2-150-5-16-42', 1, -1, 0, 6),
(109, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/user-manager/groups', 'in/user-manager/groups', '2-150-5-9-52', 1, -1, 0, 6),
(242, 'smm.papayateam.cz/galerie', 'smm.papayateam.cz/galerie', '', '', '127', 1, -1, 0, 19),
(249, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-settings/show-and-truncate-log', 'in/web-settings/show-and-truncate-log', '2-150-5-23-177', 1, -1, 0, 6),
(248, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-settings/languages', 'in/web-settings/languages', '2-150-5-23-176', 1, -1, 0, 6),
(245, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/page-manager-edit-only', 'in/page-manager-edit-only', '2-150-5-173', 1, -1, 0, 6),
(244, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/system-setup/system-notes', 'in/system-setup/system-notes', '2-150-5-147-172', 1, -1, 0, 6),
(247, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/web-settings/keywords', 'in/web-settings/keywords', '2-150-5-23-175', 1, -1, 0, 6),
(200, 'smm.papayateam.cz/admin', 'smm.papayateam.cz/admin', 'in', 'in', '2-150-5-56', 1, -1, 0, 6),
(130, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'Property', 'Property', '155', 1, -1, 0, 18),
(131, 'vh1.webprojects.localhost/web-editation', 'vh1.webprojects.localhost/web-editation', '', '', '158', 1, -1, 0, 23),
(132, 'vh1.webprojects.localhost/web-editation', 'vh1.webprojects.localhost/web-editation', 'login', 'login', '157', 1, -1, 0, 23),
(133, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'Testovaci', 'cs/Testovaci', '166', 2, -1, 0, 18),
(134, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'Testik', 'cs/Testik', '167', 2, -1, 0, 18),
(167, 'cms.webprojects.localhost/', 'cms.webprojects.localhost', 'login', 'login', '2-4', 1, -1, 0, 6),
(205, 'smm.papayateam.cz/admin', 'smm.papayateam.cz/admin', 'in/sport/seasons', 'in/sport/seasons', '2-150-5-105-107', 1, -1, 0, 6),
(204, 'smm.papayateam.cz/admin', 'smm.papayateam.cz/admin', 'in/web-settings', 'in/web-settings', '2-150-5-23', 1, -1, 0, 6),
(206, 'smm.papayateam.cz/admin', 'smm.papayateam.cz/admin', 'in/user-manager', 'in/user-manager', '2-150-5-9-53', 1, -1, 0, 6),
(160, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'FileTest/fl:fileId', 'FileTest', '128', 1, -1, 0, 8),
(158, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'gallery/GalleryTest/fl:dirId', 'gallery/GalleryTest/54', '170-169', 1, -1, 0, 18),
(159, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'gallery/GalleryTest/fl:dirId', 'gallery/GalleryTest/55', '170-169', 1, -1, 0, 18),
(156, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'gallery/GalleryTest/fl:dirId', 'gallery/GalleryTest/52', '170-169', 1, -1, 0, 18),
(157, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'gallery/GalleryTest/fl:dirId', 'gallery/GalleryTest/53', '170-169', 1, -1, 0, 18),
(151, 'vh4.webprojects.localhost/copytest', 'vh4.webprojects.localhost/copytest', 'gallery/GalleryTest', 'gallery/GalleryTest', '170-168', 1, -1, 0, 18),
(168, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'err', 'err', '86-87', 1, -1, 0, 8),
(169, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'err/404', 'err/404', '86-88', 1, -1, 0, 8),
(170, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'parsing/url/next/page/web:language', 'parsing/url/next/page', '121-123-124', 1, -1, 0, 8),
(171, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'parsing/url/next/page/web:language', 'parsing/url/next/page', '121-123-124', 1, -1, 0, 8),
(172, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'parsing/url/next/page/web:language', 'parsing/url/next/page', '121-123-124', 1, -1, 0, 8),
(173, 'testing.webprojects.localhost/testing', 'testing.webprojects.localhost/testing', 'parsing/url/testing/web:language', 'cs/parsing/url/testing', '121', 2, -1, 0, 8),
(174, 'admin.webprojects.localhost/', 'admin.webprojects.localhost', 'in/sport', 'in/sport', '2-150-5-105-106', 1, -1, 0, 6);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
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
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `group_id`, `name`, `surname`, `login`, `password`, `enable`) VALUES
(1, 1, 'admin', 'admin', 'admin', '434ddd1afcf8ef4834d3900e20fb1bde966839de', 1),
(2, 0, 'HTML', 'koder', 'htmlkoder', 'e72aef7f14a3e8348ca7930b5f8b008b0ba94d2e', 1),
(3, 0, 'tester', 'tester', 'tester', '22269ed2e373e52780dd17e8ef15640115d23652', 1),
(4, 0, 'Megan', 'Fox', 'meganfox', 'afe951870214cac9ea2dd2b3b7268e7a77e9d308', 1),
(5, 0, 'ajaxtest', 'ajaxtest', 'ajaxtest', 'e7e38956bfaf82af4dc6d63014e7cc08c1c9692e', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_in_group`
--

DROP TABLE IF EXISTS `user_in_group`;
CREATE TABLE IF NOT EXISTS `user_in_group` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `user_in_group`
--

INSERT INTO `user_in_group` (`uid`, `gid`) VALUES
(1, 1),
(1, 2),
(1, 6),
(1, 12),
(2, 2),
(3, 2),
(3, 10),
(4, 10),
(5, 2);

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
  `used_group` tinytext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=618 ;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`id`, `session_id`, `user_id`, `timestamp`, `login_timestamp`, `logout_timestamp`, `used_group`) VALUES
(610, 177291, 1, 1261016278, 1261010802, 1261165283, 'web-admins'),
(617, 1882932, 1, 1262178449, 1262178032, 0, 'web-admins'),
(616, 1908735, 1, 1261500747, 1261498359, 1261500751, 'web-admins'),
(615, 567926, 1, 1261173174, 1261166216, 1261173176, 'web-admins'),
(614, 650668, 1, 1261172491, 1261165284, 0, 'web-admins'),
(612, 1533176, 1, 1261014900, 1261014695, 1261014910, 'web-admins'),
(613, 1073077, 1, 1261016452, 1261014910, 0, 'web-admins');

-- --------------------------------------------------------

--
-- Table structure for table `web_alias`
--

DROP TABLE IF EXISTS `web_alias`;
CREATE TABLE IF NOT EXISTS `web_alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `url` tinytext NOT NULL,
  `http` int(11) NOT NULL,
  `https` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `web_alias`
--

INSERT INTO `web_alias` (`id`, `project_id`, `url`, `http`, `https`) VALUES
(1, 1, 'vh1.webprojects.localhost', 1, 1),
(24, 17, 'www.papaya.webprojects.localhost', 1, 1),
(30, 8, 'vh2.webprojects.localhost', 1, 1),
(22, 6, 'admin.webprojects.localhost', 1, 1),
(32, 8, 'web:language.testing.webprojects.localhost/testing', 1, 1),
(33, 18, 'vh3.webprojects.localhost', 1, 1),
(38, 6, 'smm.papayateam.cz/admin', 1, 1),
(47, 19, 'smm.papayateam.cz/galerie', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `web_project`
--

DROP TABLE IF EXISTS `web_project`;
CREATE TABLE IF NOT EXISTS `web_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `url` tinytext NOT NULL,
  `http` int(11) NOT NULL DEFAULT '1',
  `https` int(11) NOT NULL DEFAULT '1',
  `error_all_pid` int(11) NOT NULL,
  `error_404_pid` int(11) NOT NULL,
  `error_403_pid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `web_project`
--

INSERT INTO `web_project` (`id`, `name`, `url`, `http`, `https`, `error_all_pid`, `error_404_pid`, `error_403_pid`) VALUES
(1, 'default', 'webprojects.localhost', 1, 1, 87, 88, 89),
(6, 'CMS', 'cms.webprojects.localhost', 1, 1, 0, 0, 0),
(8, 'Testing', 'testing.webprojects.localhost/testing', 1, 1, 87, 88, 89),
(18, 'Copytest', 'vh4.webprojects.localhost/copytest', 1, 1, 0, 0, 0),
(17, 'Papaya', 'papaya.webprojects.localhost', 1, 1, 0, 0, 0),
(19, 'Galerie', 'galerie.webprojects.localhost', 1, 1, 0, 0, 0),
(20, 'Hotproject', 'vh1.webprojects.localhost/hotproject', 1, 1, 0, 0, 0),
(21, 'Ajax testing', 'vh4.webprojects.localhost/ajax-testing', 1, 1, 0, 0, 0),
(22, 'PageNG Test', 'testing.webprojects.localhost/pageng', 1, 1, 0, 0, 0),
(23, 'Web Editation', 'vh1.webprojects.localhost/web-editation', 1, 1, 0, 0, 0);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `web_project_right`
--

INSERT INTO `web_project_right` (`wp`, `gid`, `type`) VALUES
(0, 1, 102),
(0, 1, 103),
(0, 3, 101),
(1, 1, 102),
(1, 1, 103),
(1, 3, 101),
(6, 1, 102),
(6, 1, 103),
(6, 3, 101),
(8, 1, 102),
(8, 1, 103),
(8, 3, 101),
(17, 1, 102),
(17, 1, 103),
(17, 3, 101),
(18, 1, 102),
(18, 1, 103),
(18, 3, 101),
(19, 1, 102),
(19, 1, 103),
(19, 3, 101),
(20, 1, 102),
(20, 1, 103),
(20, 3, 101),
(21, 1, 102),
(21, 1, 103),
(21, 3, 101),
(22, 1, 102),
(22, 1, 103),
(22, 3, 101),
(23, 1, 102),
(23, 1, 103),
(23, 3, 101);

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
(15, 'Frame.webprojects', 1, 1156, 20, 705, 438, 0),
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wp_wysiwyg_file`
--

INSERT INTO `wp_wysiwyg_file` (`wp`, `tf_id`) VALUES
(1, 2),
(18, 30),
(8, 6);

-- --------------------------------------------------------

--
-- Table structure for table `w_projection`
--

DROP TABLE IF EXISTS `w_projection`;
CREATE TABLE IF NOT EXISTS `w_projection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE latin1_general_ci NOT NULL,
  `subname` tinytext COLLATE latin1_general_ci NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `w_projection`
--

INSERT INTO `w_projection` (`id`, `name`, `subname`, `value`, `position`, `visible`) VALUES
(1, 'Novostavba 12', 'Bílý újezd 79/19', 3, 1, 1),
(2, 'Novostavba 2', 'Bílý újezd, p.č. 79/19', 5, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `w_reference`
--

DROP TABLE IF EXISTS `w_reference`;
CREATE TABLE IF NOT EXISTS `w_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `subname` tinytext NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `w_reference`
--

INSERT INTO `w_reference` (`id`, `name`, `subname`, `type`, `position`, `visible`) VALUES
(1, 'Reference', 'Nejaky popis reference', 0, 1, 1),
(2, 'Reference 2', 'Nejaky druhe reference', 0, 2, 0),
(3, 'Reference 3', 'A zase nejaky ten popis ...', 1, 3, 1);

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
  `comment` mediumtext NOT NULL,
  `season` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`season`,`a_team`,`h_team`),
  KEY `h_team` (`h_team`),
  KEY `a_team` (`a_team`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `w_sport_match`
--

INSERT INTO `w_sport_match` (`id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `round`, `in_table`, `comment`, `season`) VALUES
(16, 1, 2, 1, 1, 1, 1, 1, 1, 0, 0, 1, 2, '', 5),
(17, 1, 2, 2, 2, 2, 2, 2, 2, 0, 0, 2, 1, '', 5);

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_player`
--

DROP TABLE IF EXISTS `w_sport_player`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `w_sport_player`
--

INSERT INTO `w_sport_player` (`id`, `name`, `surname`, `birthyear`, `number`, `position`, `photo`, `season`, `team`) VALUES
(11, 'Ondrej', 'Turek', 1987, 6, 2, '', 5, 2),
(12, 'Jan', 'Dvořák', 1975, 69, 2, '~/file.php?rid=44', 5, 2),
(13, 'Jakub', 'Malý', 1990, 10, 3, '', 5, 2),
(14, 'Jan', 'Bartůšek', 1984, 77, 3, '', 5, 3),
(10, 'Martin', 'Beneš', 1980, 44, 3, '', 5, 4),
(9, 'Jan', 'Kovařík', 1965, 1, 3, '', 5, 3),
(8, 'Marek', 'Fišera', 1988, 79, 1, '', 5, 1),
(6, 'Miroslav', 'Zůna', 1986, 3, 3, '', 5, 1),
(7, 'Miloš', 'Matějka', 1970, 6, 2, '', 5, 1),
(15, 'Lukáš', 'Topš', 1991, 1, 1, '', 5, 2);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `w_sport_season`
--

INSERT INTO `w_sport_season` (`id`, `start_year`, `end_year`) VALUES
(3, 2006, 2007),
(4, 2007, 2008),
(5, 2008, 2009);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `w_sport_stats`
--

INSERT INTO `w_sport_stats` (`pid`, `mid`, `goals`, `assists`, `penalty`, `shoots`, `season`, `table_id`) VALUES
(8, 16, 1, 1, 1, 1, 5, 2),
(7, 16, 1, 1, 1, 1, 5, 2),
(11, 16, 1, 1, 1, 1, 5, 2),
(12, 16, 1, 1, 1, 1, 5, 2),
(13, 16, 1, 1, 1, 1, 5, 2),
(15, 16, 1, 1, 1, 11, 5, 2);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `w_sport_table`
--

INSERT INTO `w_sport_table` (`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `table_id`) VALUES
(2, 1, 0, 1, 0, 1, 1, 1, 5, 2),
(1, 1, 0, 1, 0, 1, 1, 1, 5, 2),
(3, 0, 0, 0, 0, 0, 0, 0, 5, 1),
(4, 0, 0, 0, 0, 0, 0, 0, 5, 1),
(2, 1, 0, 1, 0, 2, 2, 1, 5, 1),
(1, 1, 0, 1, 0, 2, 2, 1, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_tables`
--

DROP TABLE IF EXISTS `w_sport_tables`;
CREATE TABLE IF NOT EXISTS `w_sport_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3 ;

--
-- Dumping data for table `w_sport_tables`
--

INSERT INTO `w_sport_tables` (`id`, `name`) VALUES
(1, 'Prvn� tabulka'),
(2, 'Druh� tabulka');

-- --------------------------------------------------------

--
-- Table structure for table `w_sport_team`
--

DROP TABLE IF EXISTS `w_sport_team`;
CREATE TABLE IF NOT EXISTS `w_sport_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `logo` tinytext NOT NULL,
  `season` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`season`),
  KEY `season` (`season`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `w_sport_team`
--

INSERT INTO `w_sport_team` (`id`, `name`, `logo`, `season`) VALUES
(1, 'Papaya', '~/file.php?rid=30', 5),
(2, 'Papaya B', '~/file.php?rid=30', 5),
(3, 'Vodiči', '', 5),
(4, 'Vipers', '', 5);
