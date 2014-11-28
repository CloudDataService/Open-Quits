<?php

// Client name
if ($appointment['ac_title'] && $appointment['ac_sname'])
{
	$title = ($appointment['ac_title'] === 'Other') ? $appointment['ac_title_other'] : $appointment['ac_title'];
	$name = $title . ' ' . $appointment['ac_sname'];
}
else
{
	$name = $appointment['ac_fname'];
}


// Service provider
$sp_parts = explode(',', $appointment['name']);
if (count($sp_parts) > 0)
{
	$sp_name = $sp_parts[0];
}
else
{
	$sp_name = $appointment['name'];
}


// Appointment
$ts = strtotime($appointment['a_datetime']);
$appt_date = date('l jS F Y', $ts);
$appt_time = date('g:i A', $ts);

?>

Dear <?php echo $name ?>,

Thanks for making an appointment to see a stop smoking advisor at <?php echo $sp_name ?>.

The details of your appointment are:

Date: <?php echo $appt_date ?>

Time: <?php echo $appt_time ?>

Location: <?php echo $appointment['name']?> <?php echo ($appointment['department'] === 'N/A') ? '' : $appointment['department']; ?> <?php echo $appointment['post_code'] ?>


If for some reason you are unable to make this appointment or would like to move it to another date and time, please get in touch as soon as possible on <?php echo $appointment['telephone'] ?>.


Regards
<?php echo $sp_name ?>