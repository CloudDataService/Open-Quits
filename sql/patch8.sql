ALTER TABLE `monitoring_forms`
CHANGE `support_1` `support_1` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','NRT - Quickmist','NRT - nasal spray','NRT - mouth spray','NRT - oral strips') COLLATE 'latin1_swedish_ci' NULL AFTER `intervention_type_other`,
CHANGE `support_2` `support_2` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','NRT - Quickmist','NRT - nasal spray','NRT - mouth spray','NRT - oral strips') COLLATE 'latin1_swedish_ci' NULL AFTER `support_1`,
COMMENT=''; -- 0.402 s

ALTER TABLE `monitoring_forms`
ADD `support_method` tinyint(1) unsigned NULL AFTER `support_2`,
COMMENT=''; -- 0.360 s