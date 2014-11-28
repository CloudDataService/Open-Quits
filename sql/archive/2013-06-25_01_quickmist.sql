/* Add new enum value */

ALTER TABLE `monitoring_forms`
CHANGE `support_1` `support_1` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','Quickmist','NRT - Quickmist') COLLATE 'latin1_swedish_ci' NULL AFTER `intervention_type_other`,
CHANGE `support_2` `support_2` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','Quickmist','NRT - Quickmist') COLLATE 'latin1_swedish_ci' NULL AFTER `support_1`,
COMMENT=''; -- 0.357 s




/* Change values from old => new */

UPDATE monitoring_forms SET support_1 = "NRT - Quickmist" WHERE support_1 = "Quickmist";
UPDATE monitoring_forms SET support_2 = "NRT - Quickmist" WHERE support_2 = "Quickmist";




/* Remove old value */

ALTER TABLE `monitoring_forms`
CHANGE `support_1` `support_1` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','NRT - Quickmist') COLLATE 'latin1_swedish_ci' NULL AFTER `intervention_type_other`,
CHANGE `support_2` `support_2` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','NRT - Quickmist') COLLATE 'latin1_swedish_ci' NULL AFTER `support_1`,
COMMENT=''; -- 0.321 s
