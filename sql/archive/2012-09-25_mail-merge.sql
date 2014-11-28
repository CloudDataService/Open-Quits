-- Adminer 3.6.1 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `mail_merge_documents` (
  `mmd_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Document ID',
  `mmd_sp_id` smallint(5) unsigned NOT NULL COMMENT 'Service provider ID',
  `mmd_created_sps_id` smallint(5) unsigned NOT NULL COMMENT 'User created by',
  `mmd_updated_sps_id` smallint(5) unsigned DEFAULT NULL COMMENT 'User updated by',
  `mmd_created_timestamp` datetime NOT NULL COMMENT 'Created timestamp',
  `mmd_updated_timestamp` datetime DEFAULT NULL COMMENT 'Last update timestamp',
  `mmd_title` varchar(128) NOT NULL COMMENT 'Document title',
  `mmd_content` text NOT NULL COMMENT 'Document content',
  PRIMARY KEY (`mmd_id`),
  KEY `mmd_sp_id` (`mmd_sp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `mail_merge_fields` (
  `mmf_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `mmf_sp_id` smallint(5) unsigned DEFAULT NULL,
  `mmf_name` varchar(32) NOT NULL,
  `mmf_description` varchar(255) DEFAULT NULL,
  `mmf_type` enum('monitoring_form','client','custom') DEFAULT NULL,
  `mmf_format` enum('single','multi') NOT NULL DEFAULT 'single',
  `mmf_value` text,
  PRIMARY KEY (`mmf_id`),
  UNIQUE KEY `mmf_name` (`mmf_name`),
  KEY `mmf_sp_id` (`mmf_sp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `monitoring_forms_mail_merges` (
  `mfmm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mfmm_mf_id` smallint(5) unsigned NOT NULL,
  `mfmm_sp_id` smallint(5) unsigned NOT NULL,
  `mfmm_mmd_id` smallint(5) unsigned NOT NULL,
  `mfmm_sps_id` smallint(5) unsigned NOT NULL,
  `mfmm_datetime` datetime NOT NULL,
  PRIMARY KEY (`mfmm_id`),
  KEY `mfmm_mmd_id` (`mfmm_mmd_id`),
  CONSTRAINT `monitoring_forms_mail_merges_ibfk_1` FOREIGN KEY (`mfmm_mmd_id`) REFERENCES `mail_merge_documents` (`mmd_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2012-09-25 15:16:36