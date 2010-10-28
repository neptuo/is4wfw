ALTER TABLE  `article` ADD  `order` INT NOT NULL;
ALTER TABLE  `article` ADD  `visible` INT NOT NULL DEFAULT  '2';
update `article` set `order` = `id`;