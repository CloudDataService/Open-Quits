<?php

function mysql_date_to_british($mysql_date)
{
	if( ! $mysql_date)
		return NULL;

	$mysql_date = explode('-', $mysql_date);

	$british_date = $mysql_date[2] . '/' . $mysql_date[1] . '/' . $mysql_date[0];

	return $british_date;
}

function parse_date($date)
{
	if( ! preg_match('/^[0-3]{1}[0-9]{1}\/[0-1]{1}[0-9]{1}\/[0-9]{4}$/', $date) )
		return false;

	$date = explode('/', $date);

	$mysql_date = $date[2] . '-' . $date[1] . '-' . $date[0];

	return $mysql_date;
}




function human_date($datetime = '', $default = 'D d/m/Y H:i')
{
	// Timestamp of supplied date
	$ts = strtotime($datetime);

	$today = date('Y-m-d');
	$tomorrow = date('Y-m-d', strtotime('+1 day'));

	$date = date('Y-m-d', $ts);
	$time = date('g:i A', $ts);

	if ($date === $today) return 'Today ' . $time;
	if ($date === $tomorrow) return 'Tomorrow ' . $time;

	return date($default, $ts);
}





/**
 * Format date in specified format (opposite of parse_date)
 *
 * @param string $date		Date to format, in Y-m-d format or anything recognised by strtotime
 * @param string $format		Format to use for formatting the date (for date())
 * @return string
 */
function date_fmt($date = '', $format = 'd/m/Y')
{
	if (empty($date) || $date === '0000-00-00') return NULL;
	return date($format, strtotime($date));
}