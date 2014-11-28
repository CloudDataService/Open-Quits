<?php

$config['days_short'] = array(
	1 => 'Mon',
	2 => 'Tue',
	3 => 'Wed',
	4 => 'Thu',
	5 => 'Fri',
	6 => 'Sat',
	7 => 'Sun',
);

$config['days_long'] = array(
	1 => 'Monday',
	2 => 'Tuesday',
	3 => 'Wednesday',
	4 => 'Thursday',
	5 => 'Friday',
	6 => 'Saturday',
	7 => 'Sunday',
);

// Appointment statuses
$config['a_status'] = array(
	'Reserved'           => 'Reserved',
	'Confirmed'          => 'Confirmed',
	'Cancelled (Client)' => 'Cancelled (Client)',
	'Cancelled (SP)'     => 'Cancelled (SP)',
	'Attended'           => 'Attended',
	'DNA'                => 'DNA',
);

// Service Provider location/setting
$config['provider_locations'] = array(
	'Community',
	'Community psychiatric',
	'Hospital ward',
	'Psychiatric hospital',
	'Pharmacy',
	'Dental practice',
	'General practice',
	'Maternity',
	'Children\'s centre',
	'School',
	'Prison',
	'Workplace',
	'Military base setting',
	'Workplace',
	'Stop smoking services',
	'Primary care',
	'Other',
);

// Unlicensed NCP usage methods
$config['uncp_methods'] = array(
	1 => 'Instead of licensed medication',
	2 => 'At the same time as licensed medication',
	3 => 'Consecutively to licensed medication (i.e. client switched)',
);

// Array of licensed pharmacological support products
$config['licensed_support'] = array(
	'NRT' => array(
		'NRT - patch'       => 'Patch',
		'NRT - gum'         => 'Gum',
		'NRT - lozenge'     => 'Lozenge',
		// 'NRT - spray'       => 'Spray',
		'NRT - nasal spray' => 'Nasal spray',
		'NRT - mouth spray' => 'Mouth spray',
		'NRT - oral strips' => 'Oral strips',
		'NRT - inhalator'   => 'Inhalator',
		'NRT - microtab'    => 'Microtab',
		// 'NRT - Quickmist'   => 'Quickmist',
	),
	'Other' => array(
		'Champix' => 'Champix',
		'Zyban'   => 'Zyban',
	),
);

// Licensed phamacological support methods
$config['support_methods'] = array(
	1 => array(
		'name'        => 'Used at the same time',
		'description' => 'Used at the same time',
	),
	2 => array(
		'name'        => 'Used consecutively',
		'description' => 'Used consecutively <small>(i.e. the client switched use as part of a single quit attempt but not used at the same time)</small>',
	),
);

// Treatment outcomes
$config['treatment_outcomes'] = array(
	'Quit CO verified',
	'Quit self-reported',
	'Not quit',
	'Lost to follow-up',
	'Referred to GP',
);

