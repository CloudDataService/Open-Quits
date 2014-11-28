ALTER TABLE `resources` ADD `link` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `file_size`;
ALTER TABLE `resources` CHANGE `file_name` `file_name` VARCHAR(255)  CHARACTER SET latin1  COLLATE latin1_swedish_ci  NULL  DEFAULT NULL;
ALTER TABLE `resources` CHANGE `file_size` `file_size` FLOAT  NULL;
