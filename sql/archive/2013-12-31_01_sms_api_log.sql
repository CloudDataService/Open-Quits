CREATE TABLE `sms_api_log` (
  `sal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sal_num` char(11) COLLATE utf8_unicode_ci NOT NULL,
  `sal_data` text COLLATE utf8_unicode_ci NOT NULL,
  `sal_sent` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`sal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci