<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="<?php echo current_url() ?>" method="get" id="filter_form">

		<table class="filter" style="width: 100%">

			<tr>
				<th><label for="sp_id">Service provider</label></th>
				<th><label for="pct">PCT</label></th>
				<th><label for="post_code">Post code</label></th>
				<th><label for="inactive">Show inactive</label></th>
				<th><label for="pp">Results per page</label></th>
			</tr>

			<tr>
				<td>
					<select name="sp_id" id="sp_id" class="sp_id">

						<option value="">-- All --</option>

						<?php foreach($service_providers_select as $char => $sp_array) : ?>

						<optgroup label="<?php echo $char; ?>">

							<?php foreach($sp_array as $sp) : ?>

							<option value="<?php echo $sp['id']; ?>" <?php if(@$_GET['sp_id'] == $sp['id']) echo 'selected="selected"'; ?>><?php echo $sp['name']; ?></option>

							<?php endforeach; ?>

						</optgroup>

						<?php endforeach; ?>

					</select>
				</td>
				<td>
					<select name="pct" id="pct">

						<option value="">-- All --</option>

						<?php foreach($pcts_select as $pct) : ?>
						<option value="<?php echo $pct['pct_name']; ?>" <?php if(@$_GET['pct'] == $pct['pct_name']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name_truncated']; ?></option>
						<?php endforeach; ?>

					</select>
				</td>
				<td>
					<input type="text" name="post_code" id="post_code" value="<?php echo $this->input->get('post_code') ?>" class="text" size="10" style="text-transform: uppercase"/>
				</td>
				<td><input type="checkbox" name="inactive" id="inactive" value="1" <?php if(@$_GET['inactive']) echo 'checked="checked"'; ?> /></td>
				<td>
					<select name="pp" id="pp">
						<?php foreach($pp as $pp) : ?>
						<option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
						<?php endforeach; ?>
					</select>
				</td>

			</tr>

		</table>

		<div style="text-align: right; margin-top: 10px">
			<input type="image" src="/img/btn/filter.png" alt="Filter" />
			<a href="/admin/service-providers"><img src="/img/btn/clear.png" alt="Clear" /></a>
		</div>

	</form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if($service_providers) : ?>

	<table class="results">

		<tr class="order">
			<th><a href="?order=advisor_code<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'advisor_code') echo ' class="' . $_GET['sort'] . '"'; ?>>Advisor code</a></th>
			<th><a href="?order=name<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'name') echo ' class="' . $_GET['sort'] . '"'; ?>>Name</a></th>
			<th><a href="?order=location<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'location') echo ' class="' . $_GET['sort'] . '"'; ?>>Location/setting</a></th>
			<th><a href="?order=department<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'department') echo ' class="' . $_GET['sort'] . '"'; ?>>Department/ward</a></th>
			<th><a href="?order=pct<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'pct') echo ' class="' . $_GET['sort'] . '"'; ?>>PCT</a></th>
		</tr>

		<?php foreach($service_providers as $sp) : ?>
		<tr class="row">
			<td><a href="/pms/service-providers/set/<?php echo $sp['id']; ?>"><?php echo $sp['advisor_code']; ?></a></td>
			<td><?php echo $sp['name']; ?></td>
			<td><?php echo $sp['location']; ?></td>
			<td><?php echo $sp['department']; ?></td>
			<td><?php echo $sp['pct']; ?></td>
		</tr>
		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">Your search returned no service providers.</p>

	<?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>
