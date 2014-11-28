CREATE TABLE `sms` (
  `s_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `s_c_id` int(11) unsigned DEFAULT NULL COMMENT 'Communication entry ID',
  `s_message_id` int(10) unsigned NOT NULL COMMENT 'Textmagic ID number',
  `s_mf_id` int(11) unsigned DEFAULT NULL COMMENT 'Request form ID',
  `s_sms_t_id` int(11) unsigned DEFAULT NULL COMMENT 'SMS template ID',
  `s_message` text NOT NULL COMMENT 'Message text',
  `s_to_number` char(11) NOT NULL COMMENT 'Number to send it to',
  `s_status` char(1) DEFAULT NULL COMMENT 'CiQ/TextMagic status',
  `s_created` int(11) unsigned DEFAULT NULL COMMENT 'Datetime created',
  `s_updated` int(11) unsigned DEFAULT NULL COMMENT 'Datetime updated',
  `s_type` enum('Outgoing','Incoming') DEFAULT 'Outgoing' COMMENT 'Type of SMS',
  PRIMARY KEY (`s_id`),
  KEY `s_c_id` (`s_c_id`),
  KEY `s_mf_id` (`s_mf_id`),
  KEY `s_status` (`s_status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8