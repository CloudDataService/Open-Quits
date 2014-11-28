ALTER TABLE `service_provider_staff` CHANGE `adviser_code` `advisor_code` CHAR(7)  CHARACTER SET latin1  COLLATE latin1_swedish_ci  NULL  DEFAULT NULL;
ALTER TABLE `service_providers` CHANGE `adviser_code` `advisor_code` varchar(30) COLLATE 'latin1_swedish_ci' NOT NULL AFTER `provider_code`;
