<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Put a list of items (usually a DB result array) into an array format that can be easily used for dropdown boxes, associating values to IDs.
 *
 * It would end up being used and producting HTML like this:
 *
 * <option value="$value_key">$label_key</option>
 *
 * @param array $result		DB result array to use
 * @param string $key		Key to use for the returned array key
 * @param string $value		Key to use for the returned array value
 * @param string $empty		Prepend an empty item at the start with this value.
 * @return array
 * @author CR
 */
function result_assoc($result = array(), $key = '', $value = '', $empty = NULL)
{
	$out = array();

	if ($empty !== NULL)
	{
		$out[''] = $empty;
	}

	foreach ($result as $row)
	{
		$_key = element($key, $row);
		$_value = element($value, $row);
		$out[ $_key ] = $_value;
	}

	return $out;
}