ALTER TABLE `monitoring_forms`
ADD `uncp` tinyint(1) unsigned NULL AFTER `support_none`,
ADD `uncp_method` tinyint(1) unsigned NULL AFTER `uncp`,
COMMENT='';