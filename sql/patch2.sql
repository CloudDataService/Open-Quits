ALTER TABLE `news` ADD `nc_id` smallint(5) unsigned NULL AFTER `id`;

CREATE TABLE `news_categories` (
  `nc_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nc_title` varchar(255) NOT NULL,
  `nc_active` tinyint(1) unsigned NOT NULL,
  `nc_created` datetime NOT NULL,
  `nc_updated` datetime NOT NULL
) COMMENT='' ENGINE='MyISAM' COLLATE 'utf8_unicode_ci';

INSERT INTO `news_categories` (`nc_id`, `nc_title`, `nc_active`, `nc_created`, `nc_updated`) VALUES
(1,	'General',	1,	'2014-03-07 12:45:23',	'2014-03-07 12:45:23'),
(2,	'Training',	1,	'2014-03-07 12:45:47',	'2014-03-07 12:45:47');

ALTER TABLE `news_categories` COMMENT='' AUTO_INCREMENT=100;

UPDATE `news` SET `nc_id` = 1;