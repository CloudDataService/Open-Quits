<?php

/**
 * Application HTML Helper
 */

// Return the HTML code for a sortable table heading cell
function sort_header($key, $title, $width = '')
{
	$str = '<th{thstyle}><a href="{uri}?{href}"{class}>{title}</a></th>';

	$CI =& get_instance();
	// Change sort order to reverse
	$sort = ($CI->input->get('sort') == 'asc') ? 'desc' : 'asc';
	// Add sort CSS class
	$cls = ($CI->input->get('order') == $key) ? ' class="' . $CI->input->get('sort') . '"' : '';

	// Width for column?
	$th_style = '';
	if ( ! empty($width))
	{
		$th_style = ' style="width: ' . $width . 'px" ';
	}

	// Get current GET params and build new string
	$query_data = $CI->input->get();
	$query_data['order'] = $key;
	$query_data['sort'] = $sort;
	$query_string = http_build_query($query_data, '', '&amp;');

	$search = array('{thstyle}', '{uri}', '{href}', '{key}', '{sort}', '{class}', '{title}');
	$replace = array($th_style, current_url(), $query_string, $key, $sort, $cls, $title);

	return str_replace($search, $replace, $str);
}