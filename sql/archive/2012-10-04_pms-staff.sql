CREATE TABLE `pms_staff` (
  `pmss_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `pmss_ip` varchar(16) NOT NULL,
  `pmss_datetime_last_login` datetime DEFAULT NULL,
  `pmss_email` varchar(255) NOT NULL,
  `pmss_password` varchar(50) NOT NULL,
  `pmss_fname` varchar(50) NOT NULL,
  `pmss_sname` varchar(50) NOT NULL,
  PRIMARY KEY (`pmss_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;