<?php if ($this->input->get('reschedule') === 'success'): ?>
<div class="action">
<p>This appointment has been rescheduled successfully.</p>
</div>
<?php endif; ?>

<div class="functions">

	<?php if (strtotime($appointment['a_datetime']) <= time()): ?>

		<?php if ($appointment['a_mf_id']): ?>
		<a href="<?php echo site_url('service-providers/monitoring-forms/info/' . $appointment['a_mf_id']) ?>"><img src="/img/btn/appt-mf-view.png" alt="View monitoring form"></a>
		<?php elseif (in_array($appointment['a_status'], array('Confirmed', 'Attended'))): ?>
		<a href="<?php echo site_url('service-providers/monitoring-forms/set?a_id=' . $appointment['a_id']) ?>"><img src="/img/btn/appt-mf-create.png" alt="Create new monitoring form"></a>
		<?php endif; ?>

	<?php endif; ?>


	<?php if ($appointment['a_status'] === 'Reserved'): ?>
	<a href="<?php echo site_url('service-providers/appointments/cancel/' . $appointment['a_id']) ?>"><img src="/img/btn/cancel.png" alt="Cancel"></a>
	<?php endif;?>


	<!-- <?php if ($appointment['a_status'] === 'Confirmed'): ?>
	<a href="<?php echo site_url('service-providers/appointments/cancel/' . $appointment['a_id']) ?>?source=client"><img src="/img/btn/cancel.png" alt="Cancel"></a>
	<?php endif; ?> -->

	<?php if (in_array($appointment['a_status'], array('Confirmed', 'Cancelled (SP)', 'Cancelled (Client)', 'DNA'))): ?>
	<a href="<?php echo site_url('service-providers/appointments/schedule?action=reschedule&amp;a_id=' . $appointment['a_id']) ?>"><img src="/img/btn/reschedule.png" alt="Reschedule"></a>
	<?php endif; ?>


	<?php
	$status_options = array('' => '-- New status --');
	//if ($appointment['a_status'] === 'Reserved') $status_options['Cancelled (SP)'] = 'Provider cancelled';
	if ($appointment['a_status'] === 'Confirmed')
	{
		$status_options['Cancelled (SP)'] = 'Provider cancelled';
		$status_options['Cancelled (Client)'] = 'Client cancelled';

		if (strtotime($appointment['a_datetime']) <= time())
		{
			$status_options['Attended'] = 'Attended';
			$status_options['DNA'] = 'Did not attend';
		}
	}
	?>

	<?php if (count($status_options) > 1): ?>

	<div style="margin-left: 100px; float: left">
		<?php echo form_open('service-providers/appointments/update', NULL, array('a_id' => $appointment['a_id'])) ?>

		<table class="filter">
			<tr>
				<td>
					<select name="a_status">
						<?php foreach ($status_options as $value => $label): ?>
						<option value="<?php echo $value ?>"><?php echo $label ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td><input type="image" src="/img/btn/update.png" alt="Update" /></td>
			</tr>
		</table>

		</form>
	</div>

	<?php endif; ?>


	<div class="clear"></div>
</div>

<div class="header">
	<h2>Appointment summary</h2>
</div>

<div class="item">

	<table class="form">

		<tr>
			<th>Date</th>
			<td><?php echo date('l jS F Y', strtotime($appointment['a_datetime'])) ?></td>
		</tr>

		<tr>
			<th>Time</th>
			<td><?php echo date('g:i A', strtotime($appointment['a_datetime'])) ?></td>
		</tr>

		<tr>
			<th>Status</th>
			<td><?php echo $appointment['a_status'] ?></td>
		</tr>

		<tr>
			<th>Created by</th>
			<td><?php
				if ($appointment['a_created_pmss_id']) echo $appointment['pmss_name'];
				if ($appointment['a_created_sps_id']) echo $appointment['sps_name'];
			?></td>
		</tr>

	</table>

</div>

<div class="clear"></div>

<div class="header">
	<h2>Client details</h2>
</div>

<div class="item">

	<form action="" method="post" id="ac_form">

		<table class="form">

			<tr>
				<th><label for="ac_title">Title</label></th>
				<td>
					<select name="ac_title" id="ac_title" class="other" style="margin-right: 20px;">
						<option value="">-- Please select --</option>
						<?php foreach ($form_elements['titles'] as $title): ?>
						<option value="<?php echo $title ?>" <?php echo ($title == element('ac_title', $appointment)) ? 'selected="selected"' : ''; ?>><?php echo $title ?></option>
						<?php endforeach; ?>
					</select>

					<label for="ac_title_other" class="other_label">Please specify</label>
					<?php echo form_input(array(
						'name' => 'ac_title_other',
						'id' => 'ac_title_other',
						'class' => 'text other_value',
						'size' => 8,
						'maxlength' => 8,
						'style' => 'text-transform: capitalize',
						'value' => set_value('ac_title_other', element('ac_title_other', $appointment)),
					)) ?>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="ac_fname">First name</label></th>
				<td><?php echo form_input(array(
					'name' => 'ac_fname',
					'id' => 'ac_fname',
					'class' => 'text',
					'size' => 20,
					'maxlength' => 32,
					'style' => 'text-transform: capitalize',
					'value' => set_value('ac_fname', element('ac_fname', $appointment)),
				)) ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="ac_sname">Surname</label></th>
				<td><?php echo form_input(array(
					'name' => 'ac_sname',
					'id' => 'ac_sname',
					'class' => 'text',
					'size' => 20,
					'maxlength' => 32,
					'style' => 'text-transform: capitalize',
					'value' => set_value('ac_sname', element('ac_sname', $appointment)),
				)) ?></td>
				<td class="e"></td>
			</tr>

			<tr class="vat">
				<th><label for="ac_address">Address</label></th>
				<td><?php echo form_textarea(array(
					'name' => 'ac_address',
					'id' => 'ac_address',
					'class' => 'text',
					'rows' => 4,
					'cols' => 40,
					'value' => set_value('ac_address', element('ac_address', $appointment)),
				)) ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="ac_post_code">Post code</label></th>
				<td><?php echo form_input(array(
					'name' => 'ac_post_code',
					'id' => 'ac_post_code',
					'class' => 'text',
					'size' => 10,
					'maxlength' => 8,
					'style' => 'text-transform: uppercase',
					'value' => set_value('ac_post_code', element('ac_post_code', $appointment, strtoupper($this->session->userdata('search_post_code')))),
				)) ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="ac_tel_daytime">Daytime tel</label></th>
				<td><?php echo form_input(array(
					'name' => 'ac_tel_daytime',
					'id' => 'ac_tel_daytime',
					'class' => 'text',
					'size' => 20,
					'maxlength' => 12,
					'value' => set_value('ac_tel_daytime', element('ac_tel_daytime', $appointment)),
				)) ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="ac_tel_mobile">Mobile tel</label></th>
				<td><?php echo form_input(array(
					'name' => 'ac_tel_mobile',
					'id' => 'ac_tel_mobile',
					'class' => 'text',
					'size' => 20,
					'maxlength' => 12,
					'value' => set_value('ac_tel_mobile', element('ac_tel_mobile', $appointment)),
				)) ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="ac_email">Email address</label></th>
				<td><?php echo form_input(array(
					'name' => 'ac_email',
					'id' => 'ac_email',
					'class' => 'text',
					'size' => 40,
					'maxlength' => 255,
					'value' => set_value('ac_email', element('ac_email', $appointment)),
				)) ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th></th>
				<td>
					<input type="hidden" name="send_email" value="0">
					<label for="send_email">
						<input type="checkbox" name="send_email" id="send_email" value="1" />
						Send email
					</label>
				</td>
			</tr>

			<tr>
				<td></td>
				<td><br><input type="image" src="/img/btn/save.png" alt="Save" id="save"></td>
			</tr>

		</table>

	</form>

</div>
