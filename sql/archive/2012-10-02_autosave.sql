CREATE TABLE `autosave` (
  `as_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `as_sps_id` smallint(5) unsigned NOT NULL,
  `as_uri_string` varchar(128) NOT NULL,
  `as_data` text NOT NULL,
  `as_created_datetime` datetime NOT NULL,
  PRIMARY KEY (`as_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;