CREATE TABLE `sms_templates` (
  `sms_t_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sms_t_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sms_t_title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `sms_t_text` text COLLATE utf8_unicode_ci NOT NULL,
  `sms_t_created` int(11) unsigned DEFAULT NULL,
  `sms_t_updated` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`sms_t_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci