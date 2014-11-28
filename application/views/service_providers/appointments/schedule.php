<?php if ($sp): ?>

	<?php if (isset($reschedule)): ?>
	<?php
	$date = date('D jS M', strtotime($reschedule['a_datetime']));
	$time = date('g:i A', strtotime($reschedule['a_datetime']));
	?>
	<div class="error" style="background-color: #FFE680; border: 1px solid #fc0; margin-bottom: 10px;">
		You are currently re-scheduling the <strong><?php echo $time ?></strong>
		appointment on <strong><?php echo $date ?></strong> for
		<strong><?php echo $reschedule['ac_fname'] ?> <?php echo $reschedule['ac_sname'] ?></strong>.
	</div>
	<?php endif; ?>

	<table class="schedule nav">
		<tr class="header">
			<th colspan="2"><?php
				if ($start === 'next')
				{
					echo '<a class="schedule-btn" data-sp_id="' . $sp['id'] . '" data-when="now" href="' . site_url('service-providers/appointments/schedule/now') . '" class="schedule-btn" rel="' . $sp['id'] . '">&lt; This week</a>';
				}
			?></th>
			<th colspan="4">
				<?php echo $title ?>
			</th>
			<th colspan="2"><?php
				if ($start === 'now')
				{
					echo '<a class="right schedule-btn" data-sp_id="' . $sp['id'] . '" data-when="next" href="' . site_url('service-providers/appointments/schedule/next') . '" class="schedule-btn" rel="' . $sp['id'] . '">Next week &gt;</a>';
				}
			?></th>
		</tr>
	</table>

	<br>

	<table class="schedule">

		<colgroup></colgroup>
		<?php foreach ($days as $day): ?>
		<colgroup></colgroup>
		<?php endforeach; ?>

		<thead>
			<tr>
				<th>&nbsp;</th>
				<?php foreach ($days as $day): ?>
				<th><?php echo $day->format('D') . '<br>' . $day->format('jS M') ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($slots as $slot): ?>

			<tr>
				<!-- time -->
				<th><?php echo $slot->format('g:i A') ?></th>

				<?php
				foreach ($days as $day)
				{
					$class = 'free';
					$day_num = $day->format('N');

					if ($ao[$day_num]['ao_capacity'] == 0)
					{
						// This day is not available - no capacity/closed
						$class = 'unavailable';
					}
					else
					{
						// Cells before the first appointment on this day
						$start_of_day = strtotime($ao[$day_num]['ao_first_appt_time']);
						if (strtotime($slot->format('H:i:s')) < $start_of_day)
						{
							$class = 'unavailable';
						}

						// Cells after the last appointment on this day
						$end_of_day = strtotime($ao[$day_num]['ao_last_appt_time']);
						if (strtotime($slot->format('H:i:s')) > $end_of_day)
						{
							$class = 'unavailable';
						}

						// Cells before current time on today
						$now = time();
						if (strtotime($slot->format('H:i:s')) < $now && date('Y-m-d') == $day->format('Y-m-d'))
						{
							$class = 'unavailable';
						}
					}

					// Unique ref for cell - date & time
					$cell_id = $day->format('Ymd') . $slot->format('Hi');

					// Number of appointments at this time
					$cell_appt_count = element($cell_id, $appt_count, 0);

					// Capacity available but num of appts exceed it
					if ($ao[$day_num]['ao_capacity'] > 0 && $ao[$day_num]['ao_capacity'] <= $cell_appt_count)
					{
						$class = 'booked';
					}

					// Rescheduling an appointment?
					if (isset($reschedule) && $reschedule['cell_id'] == $cell_id)
					{
						$class = 'reschedule';
					}

					$text = '';	//$cell_appt_count . ' / ' . $ao[$day_num]['ao_capacity'];

					// Attributes for table cell. So many of them it's easier to put them in an array to implode later
					$attrs = array(
						'title' => $day->format('l jS F Y') . ' at ' . $slot->format('g:i A'),
						'id' => $cell_id,
						'class' => $class,
						'data-sp_id' => $sp['id'],
						'data-date' => $day->format('Y-m-d'),
						'data-time' => $slot->format('H:i:s'),
					);

					// Add the appointment ID that is being rescheduled, if set
					if (isset($reschedule))
					{
						$attrs['data-a_id'] = $reschedule['a_id'];
					}

					// Array to string conversion for cell attributes
					$attr_strings = array();
					foreach ($attrs as $name => $value)
					{
						$attr_strings[] = $name . '="' . $value . '"';
					}

					echo '<td ' . implode(' ', $attr_strings) . '</td>' . "\n";
				}
				?>

			</tr>

			<?php endforeach; ?>

		</tbody>

	</table>

<?php else: ?>

	<p class="no_results">Error loading your provider details.</p>

<?php endif; ?>
