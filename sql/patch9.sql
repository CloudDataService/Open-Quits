
--
-- Table structure for table `sp_staff_training`
--

CREATE TABLE IF NOT EXISTS `sp_staff_training` (
  `spst_id` int(11) NOT NULL AUTO_INCREMENT,
  `spst_sps_id` smallint(6) NOT NULL,
  `spst_date` date NOT NULL,
  `spst_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`spst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
