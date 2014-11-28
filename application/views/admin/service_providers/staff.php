<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/admin/service-providers/staff" method="get" id="filter_form">

		<table class="filter">

			<tr>
				<th><label for="email">Email</label></th>
				<th><label for="inactive">Show inactive</label></th>
				<th><label for="pp">Results per page</label></th>
				<th></th>
			</tr>

			<tr>
				<td>
					<input type="text" name="email" id="email" value="<?php echo $this->input->get('email') ?>" class="text" size="20" />
				</td>
				<td><input type="checkbox" name="inactive" id="inactive" value="1" <?php if(@$_GET['inactive']) echo 'checked="checked"'; ?> /></td>
				<td>
					<select name="pp" id="pp">
						<?php foreach($pp as $pp) : ?>
						<option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td style="text-align: right">
					<input type="image" src="/img/btn/filter.png" alt="Filter" />
					<a href="/admin/service-providers/staff"><img src="/img/btn/clear.png" alt="Clear" /></a>
				</td>

			</tr>

		</table>

	</form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if($service_providers_staff) : ?>

	<table class="results">

		<tr class="order">
			<th><a href="?order=email<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'email') echo ' class="' . $_GET['sort'] . '"'; ?>>Email</a></th>
			<th><a href="?order=datetime_last_login<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'datetime_last_login') echo ' class="' . $_GET['sort'] . '"'; ?>>Last login</a></th>
			<th><a href="?order=name<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'name') echo ' class="' . $_GET['sort'] . '"'; ?>>Service provider</a></th>
			<th><a href="?order=post_code<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'post_code') echo ' class="' . $_GET['sort'] . '"'; ?>>Post code</a></th>
			<th><a href="?order=active<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'active') echo ' class="' . $_GET['sort'] . '"'; ?>>Active</a></th>
		</tr>

		<?php foreach($service_providers_staff as $sps) : ?>
		<tr class="row">
			<td><a href="/admin/service-providers/set-staff/<?php echo $sps['service_provider_id']; ?>/<?php echo $sps['id'] ?>"><?php echo $sps['email'] ?></a></td>
			<td><?php echo $sps['datetime_last_login_format']; ?></td>
			<td><?php echo $sps['name']; ?></td>
			<td><?php echo $sps['post_code']; ?></td>
			<td><img src="/img/icons/<?php echo (@$sps['active'] ? 'tick' : 'cross'); ?>.png" alt="" /></td>
		</tr>
		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">Your search returned no service providers.</p>

	<?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
