<?php

// get define the branch from teh url
if (preg_match("/\.(local|dev)$/u", $_SERVER['HTTP_HOST']) !== false)
{
	define('branch', 'develop');
}
elseif (preg_match("/^(demo.|staging.|test.)/u", $_SERVER['HTTP_HOST']) !== false)
{
	define('branch', 'develop');
}
else
{
	define('branch', 'production');
}

// define the revision file
define('REV_FILE', '../.site_revision');

// do we have this file created?
if ( ! file_exists(REV_FILE))
{
	// make it
	touch(REV_FILE);
}

// check for our payload data
if (array_key_exists('payload', $_POST))
{
	// extract the JSON
	$deploy_data = json_decode(stripslashes($_POST['payload']));

	// are we getting data for our branch?
	if ($deploy_data->servers[0]->preferred_branch == BRANCH) {
		// open the rev file
		$handle = fopen(REV_FILE, 'w') or die("Can't open file");

		// just get a bit of the revision sha1
		$rev = substr($deploy_data->end_revision->ref, 0, 8);

		// write the revision hash to the file and close
		fwrite($handle, $rev);
		fclose($handle);
	}
}
