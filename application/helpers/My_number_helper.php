<?php

function percentage($number, $total, $decimal_places = 0)
{
	if($number == 0 || $total == 0)
		return '0%';

	return round(($number/$total * 100), $decimal_places) . '%';
}

function file_size($size)
{
	$filesizename = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", " ZB", "YB");

	return ($size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes');
}