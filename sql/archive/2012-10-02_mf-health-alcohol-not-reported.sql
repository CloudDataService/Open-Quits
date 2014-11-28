ALTER TABLE `monitoring_forms`
ADD `health_problems_not_reported` tinyint(1) unsigned NULL AFTER `health_problems_other`,
ADD `alcohol_not_reported` tinyint(1) NULL;