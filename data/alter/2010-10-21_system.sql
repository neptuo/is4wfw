CREATE TABLE  `system_property` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`key` TINYTEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL ,
`value` TINYTEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_czech_ci;

INSERT INTO  `system_property` (`id` ,`key` ,`value`) VALUES (NULL ,  'db_version',  '1');