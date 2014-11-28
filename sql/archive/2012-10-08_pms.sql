-- Adminer 3.6.1 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `appointment_clients` (
  `ac_id` int(11) unsigned NOT NULL,
  `ac_title` enum('Mr','Mrs','Miss','Ms','Other') NOT NULL,
  `ac_title_other` varchar(8) DEFAULT NULL,
  `ac_fname` varchar(32) NOT NULL,
  `ac_sname` varchar(32) NOT NULL,
  `ac_address` varchar(255) NOT NULL,
  `ac_post_code` varchar(8) NOT NULL,
  `ac_tel_daytime` varchar(15) NOT NULL,
  `ac_tel_mobile` varchar(15) NOT NULL,
  `ac_email` varchar(255) NOT NULL,
  PRIMARY KEY (`ac_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `appointment_options` (
  `ao_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ao_sp_id` smallint(5) unsigned NOT NULL COMMENT 'Service provider ID',
  `ao_first_appt_time` time DEFAULT NULL COMMENT 'First appointment time',
  `ao_last_appt_time` time DEFAULT NULL COMMENT 'Last appointment time',
  `ao_length` tinyint(2) unsigned NOT NULL COMMENT 'Length of each appointment in minutes',
  `ao_capacity` tinyint(1) unsigned NOT NULL COMMENT 'How many parallel appointments can be made',
  `ao_day_of_week` tinyint(1) unsigned NOT NULL COMMENT 'Day of week',
  PRIMARY KEY (`ao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `appointments` (
  `a_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a_sp_id` smallint(5) unsigned NOT NULL COMMENT 'Service provider',
  `a_ac_id` int(11) unsigned DEFAULT NULL COMMENT 'Appointment client details',
  `a_datetime` datetime NOT NULL COMMENT 'Date/time of appointment',
  `a_status` enum('Reserved','Confirmed','Cancelled (Client)','Cancelled (SP)','Attended','DNA') NOT NULL DEFAULT 'Reserved',
  `a_created_datetime` datetime NOT NULL COMMENT 'Date/time first created',
  `a_created_pmss_id` smallint(5) unsigned DEFAULT NULL COMMENT 'PMS staff who created the appointment',
  `a_created_sps_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Service provider staff who created the appointment',
  PRIMARY KEY (`a_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2012-10-08 17:06:42