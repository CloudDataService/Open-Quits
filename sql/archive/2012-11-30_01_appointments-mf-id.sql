ALTER TABLE `appointments`
ADD `a_mf_id` int(11) unsigned NULL COMMENT 'Monitoring form ID created from this appointment' AFTER `a_ac_id`,
COMMENT='';