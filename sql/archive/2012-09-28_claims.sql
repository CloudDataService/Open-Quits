ALTER TABLE `monitoring_form_claims`
CHANGE `claim_type` `claim_type` enum('Initial','Follow up quit','4 week','12 week') COLLATE 'latin1_swedish_ci' NOT NULL AFTER `monitoring_form_id`,
COMMENT='';


ALTER TABLE `monitoring_forms`
ADD `treatment_outcome_4` enum('Not quit','Lost to follow-up','Referred to GP','Refer to tier 3','Quit self-reported','Quit CO verified') COLLATE 'latin1_swedish_ci' NULL AFTER `treatment_outcome`,
ADD `treatment_outcome_12` enum('Not quit','Lost to follow-up','Referred to GP','Refer to tier 3','Quit self-reported','Quit CO verified') COLLATE 'latin1_swedish_ci' NULL AFTER `treatment_outcome_4`


UPDATE monitoring_forms SET treatment_outcome_4 = treatment_outcome, treatment_outcome_12 = NULL;