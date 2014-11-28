ALTER TABLE `sms`
	ADD `s_a_id` INT NULL DEFAULT NULL COMMENT 'Admin sending the message' AFTER `s_to_number`,
	ADD `s_sps_id` INT NULL DEFAULT NULL COMMENT 'SP staff sending the message' AFTER `s_a_id`;

