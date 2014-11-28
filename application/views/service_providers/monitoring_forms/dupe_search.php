<?php

if ($results)
{
	echo '<div class="dupe_header">';

	if (count($results) === 1)
	{
		echo '<p>There is 1 existing record that is a potential duplicate of the information you have entered so far.</p>';
	}
	else
	{
		echo '<p>There are ' . count($results) . ' existing records that are potential duplicates of the information that you have entered so far.</p>';
	}

	echo '<p>Please contact the hub to check the details before continuing with this form.</p>';

	echo '</div>';

	echo '<ul class="dupe_records">';

	foreach ($results as $row)
	{
		echo '<li class="dupe_record">';

		echo '<p>';
		echo '<span class="dr_name">' . $row['fname'] . ' ' . $row['sname'] . '</span>';
		echo '<span class="dr_gender">' . $row['gender'] . '</span>';

		echo '<span class="dr_id">';
		if ($row['service_provider_id'] == $sp_id)
		{
			echo anchor('service-providers/monitoring-forms/info/' . $row['id'], '#' . $row['id'], 'class="dupe_view" target="_blank"');
		}
		else
		{
			echo '#' . $row['id'];
		}
		echo '</span>';
		echo '</p>';

		echo '<p>';
		echo '<span class="dr_dob"><em>DOB</em> ' . date_fmt($row['date_of_birth'], 'j M Y') . '</span>';
		echo '<span class="dr_postcode"><em>Post Code</em> ' . format_postcode($row['post_code']) . '</span>';
		echo '</p>';

		echo '</li>';
	}

	echo '</ul>';

}