ALTER TABLE  `w_sport_match` ADD  `refs2` TINYTEXT NOT NULL AFTER  `refs`;
ALTER TABLE  `w_sport_match` CHANGE  `datetime`  `date` TINYTEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL;
ALTER TABLE  `w_sport_match` ADD  `time` TINYTEXT NOT NULL AFTER  `date`;
ALTER TABLE  `w_sport_match` ADD  `main_stuff` TINYTEXT NOT NULL AFTER  `place` , ADD  `stuff` TINYTEXT NOT NULL DEFAULT  '' AFTER  `main_stuff` , ADD  `stuff2` TINYTEXT NOT NULL DEFAULT  '' AFTER  `stuff`;