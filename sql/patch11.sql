ALTER TABLE `service_provider_staff` ADD `advisor_code` CHAR(7)  NULL  DEFAULT NULL  AFTER `tickets`;
ALTER TABLE `service_provider_staff` ADD UNIQUE INDEX (`advisor_code`);
ALTER TABLE `service_provider_staff` ADD INDEX (`advisor_code`);
