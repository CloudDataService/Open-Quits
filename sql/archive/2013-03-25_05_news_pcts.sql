CREATE TABLE `news_pcts` (
  `news_id` smallint(5) unsigned NOT NULL,
  `pct_id` tinyint(3) unsigned NOT NULL
) COMMENT='' ENGINE='MyISAM' COLLATE 'utf8_unicode_ci';

ALTER TABLE `news_pcts`
ADD PRIMARY KEY `news_id_pct_id` (`news_id`, `pct_id`);