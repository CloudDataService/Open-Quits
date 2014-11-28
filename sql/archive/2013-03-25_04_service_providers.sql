ALTER TABLE `service_providers`
ADD `pct_id` tinyint(3) unsigned NULL AFTER `pct`,
COMMENT='';

UPDATE service_providers sp
LEFT JOIN pcts ON sp.pct = pcts.pct_name
SET sp.pct_id = IF(pcts.id IS NULL, NULL, pcts.id)