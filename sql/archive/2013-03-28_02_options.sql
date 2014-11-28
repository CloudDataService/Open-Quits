ALTER TABLE `options`
ADD `pct_id` tinyint(3) unsigned NULL,
COMMENT='';


ALTER TABLE `options`
ADD PRIMARY KEY `option_name_pct_id` (`option_name`, `pct_id`),
DROP INDEX `PRIMARY`;


INSERT INTO options (option_name, option_value, pct_id)
SELECT option_name, option_value, pcts.id
FROM options, pcts
WHERE option_name != "total_sms_sent";


DELETE FROM options WHERE pct_id = 0 AND option_name != "total_sms_sent";