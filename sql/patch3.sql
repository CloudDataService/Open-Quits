CREATE TABLE `marketing_sources` (
  `ms_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ms_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ms_active` tinyint(1) unsigned NOT NULL,
  `ms_created` datetime NOT NULL,
  `ms_updated` datetime NOT NULL,
  PRIMARY KEY (`ms_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `marketing_sources` (`ms_id`, `ms_title`, `ms_active`, `ms_created`, `ms_updated`) VALUES
(1,	'GP',	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(2,	'Other health professional',	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(3,	'Friend or relative',	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(4,	'Advertising',	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(5,	'Pharmacy',	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00');

UPDATE marketing_sources SET ms_created = NOW(), ms_updated = NOW();

ALTER TABLE `marketing_sources` COMMENT='' AUTO_INCREMENT=100;

ALTER TABLE `monitoring_forms` ADD `ms_id` smallint(5) unsigned NULL AFTER `date_created`;

UPDATE monitoring_forms SET ms_id = (SELECT ms_id FROM marketing_sources WHERE ms_title = marketing LIMIT 1);