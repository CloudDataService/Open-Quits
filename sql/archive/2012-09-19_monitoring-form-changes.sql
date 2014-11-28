ALTER TABLE `monitoring_forms`
    ADD COLUMN `date_of_12_week_follow_up` date NULL AFTER `date_of_4_week_follow_up`;

ALTER TABLE `monitoring_forms`
    ADD COLUMN `a_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `previously_treated`;

ALTER TABLE `monitoring_forms`
    ADD COLUMN `health_problems_other` varchar(255) COLLATE 'latin1_swedish_ci' NULL;

ALTER TABLE `monitoring_forms`
	ADD COLUMN `alcohol` TINYINT(1) NULL DEFAULT NULL AFTER `health_problems_other`;


CREATE TABLE `health_problems` (
  `hp_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `hp_name` varchar(64) NOT NULL,
  PRIMARY KEY (`hp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `health_problems` (`hp_id`, `hp_name`) VALUES
(1,	'Heart Disease/problems'),
(2,	'Stroke'),
(3,	'Diabetes'),
(4,	'Gastritis/Stomach Ulcer'),
(5,	'Liver Disease'),
(6,	'Epilepsy'),
(7,	'Kidney Disease'),
(8,	'Bronchitis/Emphysema'),
(9,	'Eating Disorder'),
(10,	'Asthma'),
(11,	'Head Injury'),
(12,	'Bipolar Disorder'),
(13,	'Brain Tumor'),
(14,	'Cancer'),
(15,	'High Blood Pressure'),
(16,	'Depression');

CREATE TABLE `mf2hp` (
  `mf2hp_hp_id` tinyint(3) unsigned NOT NULL,
  `mf2hp_mf_id` int(11) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
