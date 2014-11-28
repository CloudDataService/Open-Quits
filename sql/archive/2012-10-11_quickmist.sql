ALTER TABLE `monitoring_forms`
CHANGE `support_1` `support_1` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','Quickmist') COLLATE 'latin1_swedish_ci' NULL AFTER `intervention_type_other`,
CHANGE `support_2` `support_2` enum('NRT - lozenge','NRT - microtab','NRT - inhalator','NRT - spray','NRT - gum','NRT - patch','Champix','Zyban','Quickmist') COLLATE 'latin1_swedish_ci' NULL AFTER `support_1`,
COMMENT='';