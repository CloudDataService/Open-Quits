<?php

/**
 * Format a postcode properly to AB12 6YZ format
 */
function format_postcode($post_code = '')
{
	if (empty($post_code)) return NULL;
	$post_code = trim(preg_replace('/\s+/', '', $post_code));
	$end = trim(substr($post_code, -3));
	$start = trim(str_replace($end, '', $post_code));
	return strtoupper($start . ' ' . $end);
}




function form_time($name = '', $selected = '')
{
	for ($h = 0; $h < 24; $h++)
	{
		$hr = sprintf("%02d", $h);
		$hours[$hr] = $hr;
	}

	for ($m = 0; $m < 60; $m += 5)
	{
		$min = sprintf("%02d", $m);
		$mins[$min] = $min;
	}

	$str = '';

	$str .= form_dropdown($name . '[hh]', $hours);
	$str .= ' : ';
	$str .= form_dropdown($name . '[mm]', $mins);

	return $str;
}




function quarters_dropdown($name = '', $id = '', $from = '2010', $format = 'd/m/Y', $selected = '', $attrs = '')
{
	$quarters = array(
		'Q1' => array('01-01', '03-31'),
		'Q2' => array('04-01', '06-30'),
		'Q3' => array('07-01', '09-30'),
		'Q4' => array('10-01', '12-31'),
	);

	$out = '';

	$out .= "<select name='$name' id='$id' $attrs>";

	$out .= '<option value="">-- Please select --</option>';

	$years = range($from, date('Y'), 1);
	foreach ($years as $year)
	{
		$out .= '<optgroup label="' . $year . '">';

		foreach ($quarters as $q => $range)
		{
			$date_from = date($format, strtotime("$year-{$range[0]}"));
			$date_to = date($format, strtotime("$year-{$range[1]}"));
			$value = "$year$q";
			$selected_str = ($selected == $value ? 'selected="selected"' : '');

			$out .= "<option value='$value' data-from='$date_from' data-to='$date_to' $selected_str>$q $year</option>";
		}

		$out .= '</optgroup>';
	}

	$out .= '</select>';

	return $out;
}




function form_support_dropdown($name = '', $selected = '', $attrs = '')
{
	$items = config_item('licensed_support');

	$out = '';

	$out .= "<select name='$name' $attrs>\n";

	$out .= '<option value="">-- Please select --</option>';

	foreach ($items as $k => $v)
	{
		if (is_array($v))
		{
			// Opt group
			$out .= "<optgroup label='$k'>";
			foreach ($v as $value => $label)
			{
				//only show 'Oral' if it's saved as that.
				if($value != 'NRT - oral strips' || $value == $selected)
				{
					$attr_sel = ($value == $selected ? 'selected="selected"' : '');
					$out .= "<option value='$value' $attr_sel>$label</option>\n";
				}
			}
			$out .= '</optgroup>';
		}
		else
		{
			$attr_sel = ($k == $selected ? 'selected="selected"' : '');
			$out .= "<option value='$k' $attr_sel>$v</option>\n";
		}
	}

	$out .= '</select>';


	return $out;
}