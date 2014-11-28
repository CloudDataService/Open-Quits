<div class="functions">
	<a href="/service-providers/appointments/schedule"><img src="/img/btn/add-new.png" alt="Add new" /></a>
	<a href="/service-providers/appointments/options"><img src="/img/btn/options.png" alt="Options" /></a>
	<div class="clear"></div>
</div>




<?php if ( ! $this->input->get('x')): ?>

<div class="header">
	<h2>Upcoming appointments</h2>
</div>

<div class="item results">

	<?php if ($upcoming): ?>

	<table class="results">

		<tr class="order">
			<th>First name</th>
			<th>Last name</th>
			<th>Date</th>
			<th>View more</th>
		</tr>

		<?php foreach ($upcoming as $a): ?>

		<tr class="row">
			<td><?php echo $a['ac_fname'] ?></td>
			<td><?php echo $a['ac_sname'] ?></td>
			<td><?php echo human_date($a['a_datetime']) ?></td>
			<td><a href="<?php echo site_url('service-providers/appointments/set/' . $a['a_id']) ?>"><img src="/img/icons/magnifier.png" alt="View more" /></a></td>
		</tr>

		<?php endforeach; ?>

	</table>

	<?php else: ?>

	<p class="no_results">Your search returned no appointments.</p>

	<?php endif; ?>

</div>

<br><br>

<?php endif; ?>




<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="<?php echo site_url('service-providers/appointments') ?>" method="get">

		<table class="filter" style="width: 100%">

			<tr>
				<th><label for="date_from">Date from</label></th>
				<th><label for="date_to">Date to</label></th>
				<th><label for="ac_fname">Client first name</label></th>
				<th><label for="ac_sname">Client surname</label></th>
				<th><label for="pp">Results </label></th>
				<th>&nbsp;</th>
			</tr>

			<tr>
				<td><input type="text" name="date_from" id="date_from" value="<?php echo element('date_from', $filter) ?>" class="datepicker text" /></td>
				<td><input type="text" name="date_to" id="date_to" value="<?php echo element('date_to', $filter) ?>" class="datepicker text" /></td>
				<td><input type="text" name="ac_fname" id="ac_fname" value="<?php echo element('ac_fname', $filter) ?>" class="text" /></td>
				<td><input type="text" name="ac_sname" id="ac_sname" value="<?php echo element('ac_sname', $filter) ?>" class="text" /></td>
				<td>
					<select name="pp" id="pp">
						<?php foreach ($pp as $pp): ?>
						<option value="<?php echo $pp; ?>" <?php if (@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<input type="image" src="/img/btn/filter.png" alt="Filter" />
				</td>
			</tr>

			<tr>
				<th><label for="date_field">Date field</label></th>
				<th><label for="a_status">Status</label></th>
				<th><label for="post_code">Post code</label></th>
				<th colspan="3"></th>
			</tr>

			<tr>
				<td>
					<select name="date_field">
						<option value="a_datetime" <?php echo (element('date_field', $filter) === 'a_datetime') ? 'selected="selected"' : ''; ?>>Appointment</option>
						<option value="a_created_datetime" <?php echo (element('date_field', $filter) === 'a_created_datetime') ? 'selected="selected"' : ''; ?>>Created</option>
					</select>
				</td>
				<td><?php
					$statuses = array('' => '(Any)');
					$statuses += config_item('a_status');
					echo form_dropdown('a_status', $statuses, element('a_status', $filter));
				?></td>
				<td><input type="text" name="post_code" id="post_code" value="<?php echo element('post_code', $filter) ?>" class="text" style="text-transform: uppercase" /></td>
				<td colspan="2"></td>
				<td><a href="<?php echo current_url(); ?>"><img src="/img/btn/clear.png" alt="Clear" /></a></td>
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

	<?php if ($appointments): ?>

	<table class="results">

		<tr class="order">
			<th><a href="?order=ac_fname<?php echo $sort; ?>"<?php if (@$_GET['order'] == 'ac_fname') echo ' class="' . $_GET['sort'] . '"'; ?>>First name</a></th>
			<th><a href="?order=ac_sname<?php echo $sort; ?>"<?php if (@$_GET['order'] == 'ac_sname') echo ' class="' . $_GET['sort'] . '"'; ?>>Last name</a></th>
			<th><a href="?order=<?php echo $filter['date_field'] ?><?php echo $sort; ?>"<?php if(@$_GET['order'] == $filter['date_field']) echo ' class="' . $_GET['sort'] . '"'; ?>>Date</a></th>
			<th><a href="?order=a_status<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'a_status') echo ' class="' . $_GET['sort'] . '"'; ?>>Status</a></th>
			<th>View more</th>
		</tr>

		<?php foreach ($appointments as $a): ?>

		<tr class="row">
			<td><?php echo $a['ac_fname'] ?></td>
			<td><?php echo $a['ac_sname'] ?></td>
			<td><?php echo $a[$filter['date_field'] . '_format'] ?></td>
			<td><?php echo $a['a_status'] ?></td>
			<td><a href="<?php echo site_url('service-providers/appointments/set/' . $a['a_id']) ?>"><img src="/img/icons/magnifier.png" alt="View more" /></a></td>
		</tr>

		<?php endforeach; ?>

	</table>

	<?php else: ?>

	<p class="no_results">Your search returned no appointments.</p>

	<?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>



<script type="text/javascript">
$(document).ready(function() {
	$(".datepicker").datepicker();
})
</script>
