<style>
.inactive td, .inactive-key {
	color: darkred;
	font-style: italic;
}
</style>

<div class="functions">

	<a href="/admin/service-providers/set/<?php echo $service_provider['id']; ?>"><img src="/img/btn/update.png" alt="Update" /></a>

	<a href="/admin/service-providers/set-staff/<?php echo $service_provider['id']; ?>"><img src="/img/btn/add-staff.png" alt="Add staff" /></a>

	<?php if($service_provider['active']) : ?>
	<a href="?active_status=0" class="action" title="Click OK to deactivate this service provider."><img src="/img/btn/deactivate.png" alt="Deactivate" /></a>
	<?php else : ?>
	<a href="?active_status=1" class="action" title="Click OK to activate this service provider."><img src="/img/btn/activate.png" alt="Activate" /></a>
	<?php endif; ?>

	<?php if($this->session->userdata('master')) : ?>
	<a href="?delete=1" class="action" title="Click OK to permanently delete this service provider. This will also delete all related staff, monitoring forms, claims and logs."><img src="/img/btn/delete.png" alt="Delete" /></a>
	<?php endif; ?>

	<div class="clear"></div>

</div>

<div class="panel_left">

	<div class="header">
		<h2>Service provider</h2>
	</div>

	<div class="item">

		<table class="form">

			<tr>
				<th>Name</th>
				<td><?php echo $service_provider['name']; ?></td>
			</tr>

			<tr>
				<th>Post code</th>
				<td><?php echo $service_provider['post_code']; ?></td>
			</tr>

			<tr>
				<th>Location/setting</th>
				<td>
					<?php
					echo ($service_provider['location'] ? $service_provider['location'] : 'N/A');
					echo ($service_provider['location'] == 'Other' && ! empty($service_provider['location_other']) ? " ({$service_provider['location_other']})" : '');
					?>
				</td>
			</tr>

			<tr>
				<th>Department/ward</th>
				<td><?php echo ($service_provider['department'] ? $service_provider['department'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>Venue</th>
				<td><?php echo ($service_provider['venue'] ? $service_provider['venue'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>Telephone</th>
				<td><?php echo ($service_provider['telephone'] ? $service_provider['telephone'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>Advisor code</th>
				<td><?php echo ($service_provider['advisor_code'] ? $service_provider['advisor_code'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>Provider code</th>
				<td><?php echo ($service_provider['provider_code'] ? $service_provider['provider_code'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>Cost code</th>
				<td><?php echo ($service_provider['cost_code'] ? $service_provider['cost_code'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>Group</th>
				<td><?php echo $service_provider['group_name']; ?></td>
			</tr>

			<tr>
				<th>Local Authority</th>
				<td><?php echo ($service_provider['pct_name'] ? $service_provider['pct_name'] : 'Unassigned'); ?></td>
			</tr>

			<tr>
				<th>Tier 3</th>
				<td><?php echo ($service_provider['tier_3'] ? 'Yes' : 'No'); ?></td>
			</tr>

			<tr>
				<th>Default password</th>
				<td><?php echo $default_password; ?></td>
			</tr>

		</table>

	</div>

</div>

<div class="panel_right">

	<div class="header">
		<h2>Staff</h2>
	</div>

	<div class="item results">

		<?php if($service_provider_staff) : ?>

		<table class="results">

			<tr class="order">
				<th>Name</th>
				<th>Email</th>
				<th>Admin</th>
			</tr>

			<?php foreach($service_provider_staff as $sps) : ?>
			<tr class="row <?php if ($sps['active'] == 0) echo 'no_click inactive' ?>">

				<?php if ($sps['active'] == 1): ?>
				<td><a href="/admin/service-providers/set-staff/<?php echo $service_provider['id'] . '/' . $sps['id']; ?>"><?php echo $sps['fname'] . ' ' . $sps['sname']; ?></a></td>
				<?php else: ?>
				<td><?php echo $sps['fname'] . ' ' . $sps['sname']; ?> *</td>
				<?php endif; ?>

				<td><?php echo $sps['email']; ?></td>
				<td><?php if($sps['master']) echo '<img src="/img/icons/tick.png" alt="Admin" />'; ?></td>
			</tr>
			<?php endforeach; ?>

		</table>

		<?php else : ?>

		<p class="no_results">There are currently no staff members linked to this service provider.</p>

		<?php endif; ?>

	</div>

	<p class="inactive-key">* Deleted staff</p>

</div>

<div class="clear"></div>

<a href="/admin/service-providers" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
