ALTER TABLE `monitoring_forms` ADD `sps_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `alcohol_not_reported`;
ALTER TABLE `monitoring_forms` ADD `advisor_code` char(7) NULL DEFAULT NULL  AFTER `sps_id`;
