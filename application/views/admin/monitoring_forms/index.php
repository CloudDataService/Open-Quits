<style>
.item .filter {
	width: 100%;
}
</style>

<div class="functions">

	<div class="panel_left">

		<form action="/admin/monitoring-forms/csv" method="get" id="export_to_csv_form">

			<?php
			$get_fields = array(
				'order',
				'sort',
				'date_from',
				'date_to',
				'treatment_outcome',
				'sp_id',
				'pct_id',
				'location',
				'date_type',
				'fname_like',
				'sname_like',
				'id',
			);

			echo form_hidden('export', 1);

			foreach ($get_fields as $field)
			{
				echo form_hidden($field, $this->input->get($field));
			}
			?>

			<table class="filter">

				<tr>
					<td><input type="image" src="/img/btn/export-to-csv.png" alt="Export to CSV" title="Click OK to export <?php echo $total_export . ($total_export == 1 ? ' monitoring form' : ' monitoring forms'); ?> to CSV." class="action" /></td>
					<td>
						<select name="schema_id">
							<option value="0">Entire data</option>
							<?php foreach($export_schemas as $schema) : ?>
							<option value="<?php echo $schema['id']; ?>"><?php echo $schema['title']; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

			</table>

		</form>

		<div class="clear"></div>

	</div>


	<div class="panel_right">

		<form action="/admin/communications/create_from_search" method="get" id="new_message_form">
			<?php
			$fields = array('date_from', 'date_to', 'treatment_outcome', 'sp_id', 'pct_id', 'location', 'date_type', 'follow_up');
			foreach ($fields as $field)
			{
				echo form_hidden($field, $this->input->get($field));
			}
			echo form_hidden('sms_valid', 1);
			?>

			<table class="filter" style="float: right">

				<tr>
					<td><input type="image" src="/img/btn/send-bulk-sms.png" alt="Send Bulk SMS" title="Click OK to start sending an SMS to those clients who have SMS support enabled." class="action" /></td>
					<td>
						<select name="sms_t_id">
							<option value="">-- Please select --</option>
							<?php foreach($sms_templates as $t) : ?>
							<option value="<?php echo $t['sms_t_id']; ?>"><?php echo $t['sms_t_title']; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

			</table>
		</form>


		<div class="clear"></div>
	</div>


	<div class="clear"></div>

</div>


<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="/admin/monitoring-forms" method="get">

		<?php
		if ( ! $pct_id)
		{
			$this->load->view('admin/monitoring_forms/index/filter.admin.php');
		}
		else
		{
			$this->load->view('admin/monitoring_forms/index/filter.local.php');
		}
		?>

	</form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<div class="item results">

	<?php if($monitoring_forms) : ?>

	<?php
	// Set which follow up date data point to use
	$follow_up = element('follow_up', $_GET, 4);
	?>

	<table class="results">

		<tr class="order">
			<?php
			echo sort_header('id', 'ID');
			if ( ! $pct_id)
			{
				echo sort_header('sname', 'Client name', 140);
			}
			echo sort_header('date_created', 'Date created', 100);
			echo sort_header('sp_name', 'Service provider');
			echo sort_header("date_of_{$follow_up}_week_follow_up", "Follow up date ({$follow_up})", 130);
			echo sort_header("treatment_outcome_{$follow_up}", "Outcome ({$follow_up})", 130);
			?>
			<th>View more</th>
		</tr>

		<?php foreach($monitoring_forms as $mf) : ?>
		<tr class="row">
			<td>#<?php echo $mf['id']; ?></td>
			<?php if ( ! $pct_id): ?>
				<td><?php echo $mf['client_name'] ?></td>
			<?php endif; ?>
			<td><?php echo $mf['date_created_format']; ?></td>
			<td><?php echo $mf['sp_name']; ?></td>
			<td><?php echo $mf['date_of_' . $follow_up . '_week_follow_up_format']; ?></td>
			<td><?php echo $mf['treatment_outcome_' . $follow_up]; ?></td>
			<td><a href="/admin/monitoring-forms/info/<?php echo $mf['id']; ?>"><img src="/img/icons/magnifier.png" alt="View more" /></a></td>
		</tr>
		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">Your search returned no monitoring forms.</p>

	<?php endif; ?>

</div>

<?php echo $this->pagination->create_links(); ?>