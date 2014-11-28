<!--[if lte IE 8]>
<link rel="stylesheet" href="/scripts/leaflet/leaflet.ie.css" />
<link rel="stylesheet" href="/scripts/leaflet/MarkerCluster.Default.ie.css" />
<![endif]-->

<div class="header">
	<h2>Search</h2>
</div>

<div class="item">

	<form action="<?php echo current_url() ?>" method="get">

		<?php
		// Add existing GET vars as hidden form inputs so they are maintained when the form is submitted.
		// Form values that should be retained are for rescheduling an appointment.
		if ($this->input->get())
		{
			foreach ($this->input->get() as $key => $value)
			{
				echo form_hidden($key, $value);
			}
		}
		?>

		<table class="filter">

			<tr>
				<th><label for="post_code">Post code</label></th>
				<th>&nbsp;</th>
			</tr>

			<tr>
				<td><input type="text" name="post_code" id="post_code" value="<?php echo @$_GET['post_code']; ?>" size="10" style="text-transform: uppercase" class="text" /></td>
				<td>
					<input type="image" src="/img/btn/search.png" alt="Search" />
					<a href="<?php echo current_url(); ?>" class="schedule"><img src="/img/btn/clear.png" alt="Clear" /></a>
				</td>
			</tr>

		</table>

	</form>

</div>


<?php if ($total > 0): ?>

<div class="clear"></div>


<div class="results">

	<div class="first panel key" style="width: 555px">

		<div class="key-type-map">
			<div class="key-item"><img src="/scripts/leaflet/images/marker-icon.png" width="13" height="21" alt="Service Provider icon"> Service Provider</div>
			<div class="key-item"><img src="/scripts/leaflet/images/marker-icon-red.png" width="13" height="21" alt="Client icon"> Client</div>
			<div class="clear"></div>
		</div>

		<div class="key-type-schedule">
			<div class="key-item"><div class="box free"></div> Free</div>
			<div class="key-item"><div class="box booked"></div> Fully booked</div>
			<div class="key-item"><div class="box unavailable"></div> Unavailable</div>
			<div class="key-item"><div class="box reschedule"></div> Rescheduling</div>
		</div>

	</div>

	<div class="last panel result-total" style="width: 330px; text-align: right">
		<?php echo $total ?> results
	</div>

</div>


<div class="results">

	<div class="first panel" style="width: 555px">
		<div id="map" style="height: 555px; width: 100%; border: 1px solid #ccc; "></div>
		<div id="schedule" style="width:100%">&nbsp;</div>
	</div>

	<div class="last panel" style="width: 330px; height: 555px; overflow-y: scroll">

		<?php if ($results): ?>

			<ul class="sp-list">

				<?php foreach ($results as $sp): ?>

					<li class="sp-list-item sp-id-<?php echo $sp['id'] ?>" rel="<?php echo $sp['id'] ?>">
						<div class="sp-list-item-heading">
							<p class="sp-list-item-title"><?php echo $sp['name'] ?></p>
							<p class="sp-list-item-distance"><?php
							$distance = round($sp['distance'], 1);
							echo "About $distance ";
							echo ($distance == 1) ? 'mile' : 'miles';
							?></p>
						</div>
						<div class="sp-list-item-info">

							<?php $opts = element($sp['id'], $ao); if ($opts): ?>

							<h2>Appointment times (first - last)</h2>
							<dl>
								<?php
								foreach (config_item('days_long') as $day_num => $name)
								{
									echo '<dt>' . $name . '</dt>';
									echo '<dd>';
									echo (element('ao_capacity', $opts[$day_num], 0) > 0)
										? element('ao_first_appt_time_format', $opts[$day_num]) . ' - ' . element('ao_last_appt_time_format', $opts[$day_num])
										: 'Closed';
									echo '</dd>';
								}
								?>
							</dl>

							<div style="text-align: left; margin-top: 10px;">
								<a href="<?php echo site_url('pms/service-providers/schedule/' . $sp['id']) ?>" class="schedule-btn" data-sp_id="<?php echo $sp['id'] ?>" data-when="now"><img src="/img/btn/schedule.png" alt="Schedule" /></a>
								<a href="<?php echo site_url('pms/service-providers/set/' . $sp['id']) ?>"><img src="/img/btn/update.png" alt="Update" /></a>
							</div>

							<?php else: ?>

							<p style="color: darkred">This provider has not set up their appointment times yet.</p>

							<div style="text-align: left; margin-top: 10px;">
								<a href="<?php echo site_url('pms/service-providers/set/' . $sp['id']) ?>"><img src="/img/btn/update.png" alt="Update" /></a>
							</div>

							<?php endif; ?>

						</div>
					</li>

				<?php endforeach; ?>

			</ul>

		<?php endif; ?>

	</div>

	<div class="clear"></div>

</div>

<?php endif; ?>



<script type="text/javascript">
$(document).ready(function() {

	<?php if ($results): ?>
	map.set_sps(<?php echo json_encode($results) ?>);
	<?php endif; ?>

	map.set_centre([<?php echo $lat ?>, <?php echo $lng ?>]);
	map.init();
	schedule.init();

	<?php if ($this->input->get('action') === 'reschedule'): ?>
	map.show_info(<?php echo $this->input->get('sp_id') ?>, true);
	schedule.reschedule(<?php echo $this->input->get('a_id') ?>);
	schedule.load(<?php echo $this->input->get('sp_id') ?>, "now");
	<?php endif; ?>

});
</script>
