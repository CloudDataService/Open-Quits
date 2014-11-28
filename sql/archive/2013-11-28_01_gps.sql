ALTER TABLE `gps` ADD `gp_active` tinyint(1) unsigned NOT NULL DEFAULT '1';

UPDATE `gps` SET `gp_active` = '0' WHERE `gp_code` = 'A85015';

UPDATE `gps` SET `gp_name` = 'Wrekenton Health Centre' WHERE `gp_code` = 'A85016';