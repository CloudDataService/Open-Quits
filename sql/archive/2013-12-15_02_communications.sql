CREATE TABLE `communications` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_created` int(11) unsigned DEFAULT NULL,
  `c_updated` int(11) unsigned DEFAULT NULL,
  `c_text` text COLLATE utf8_unicode_ci,
  `c_notes` text COLLATE utf8_unicode_ci,
  `c_type` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 's' COMMENT 'Email/SMS...',
  `c_status` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'w' COMMENT 'Waiting/Processing/Complete',
  `c_total_clients` mediumint(5) unsigned NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci