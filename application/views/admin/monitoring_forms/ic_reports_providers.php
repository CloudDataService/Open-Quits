<style>
table.stats td.header {
	text-align:left;
	width:300px;
}

.percentage {
	font-size: 90%;
	margin-left: 10px;
}
</style>

<div class="header">
	<h2>Filter</h2>
</div>

<div class="item">

	<form action="" method="get">

		<table class="filter" style="width: 100%">

			<tr>
				<th><label for="quarter">Quarter</label></th>
				<th><label for="date_from">Date from</label></th>
				<th><label for="date_to">Date to</label></th>
				<th><label for="date_type">Date type</label></th>
				<th><label for="PCT">Local Authority</label></th>
				<th><label for="follow_up">Follow up</label></th>
			</tr>

			<tr>
				<td>
					<?php echo quarters_dropdown('quarter', 'quarter', '2010', 'd/m/Y', $this->input->get('quarter'), 'class="js-quarters"') ?>
				</td>
				<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
				<td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
				<td><select name="date_type">
					  <option value="qds" <?php if(@$_GET['date_type'] == 'qds') { echo 'selected="selected"'; } ?>>Agreed Quit Date</option>
					  <option value="dc" <?php if(@$_GET['date_type'] == 'dc') { echo('selected="selected"'); } ?>>Date Created</option>
				  </select></td>
				<td>
					<select name="pct_id" id="pct_id" <?php if ($pct_id) echo 'disabled="disabled"' ?>>
						<option value="">-- All --</option>
						<?php foreach($pcts_select as $pct) : ?>
						<option value="<?php echo $pct['id']; ?>" <?php if(@$_GET['pct_id'] == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name_truncated']; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<select name="follow_up">
						<option value="4" <?php echo (@$_GET['follow_up'] == 4) ? 'selected="selected"' : ''; ?>>4 week</option>
						<option value="12" <?php echo (@$_GET['follow_up'] == 12) ? 'selected="selected"' : ''; ?>>12 week</option>
					</select>
				</td>
			</tr>

			<tr>
				<td colspan="6" style="text-align: right;">
					<input type="image" src="/img/btn/filter.png" alt="Filter" /> <a href="/admin/monitoring-forms/ic-reports-providers"><img src="/img/btn/clear.png" alt="Clear" /></a>
				</td>
			</tr>

		</table>
	</form>

</div>

<?php

$follow_up = $this->input->get('follow_up');

foreach ($ic as $pct_name => $sps)
{
	echo '<h2>' . $pct_name . '</h2>';

	echo '<table class="stats">';

	echo '<tr>
			<th>Provider</th>
			<th>Total</th>
			<th colspan="2">Successful quit ' . $follow_up . ' week</th>
			<th colspan="2">CO verified</th>
			<th colspan="2">Lost to follow-up</th>
		</tr>';

	foreach ($sps as $sp)
	{
		echo '<tr>';

		echo '<td class="header">' . $sp['name'] . '</td>';
		echo '<td>' . $sp['total'] . '</td>';

		echo '<td title="Successful quit ' . $follow_up . ' week">' . $sp['successful_quit_' . $follow_up] . '</td>';
		echo '<td title="Successful quit ' . $follow_up . ' week quit ratio">' . percentage($sp['successful_quit_' . $follow_up], $sp['total'], 1) . '</td>';
		echo '<td title="Successful quit ' . $follow_up . ' week CO verified">' . $sp['successful_quit_co_' . $follow_up] . '</td>';
		echo '<td title="Successful quit ' . $follow_up . ' week CO verified percentage" class="percentage">' . percentage($sp['successful_quit_co_' . $follow_up], $sp['successful_quit_' . $follow_up], 1) . '</td>';

		echo '<td title="Lost to follow-up ' . $follow_up . ' week">' . $sp['lost_' . $follow_up] . '</td>';
		echo '<td title="Lost to follow-up ' . $follow_up . ' week percentage" class="percentage">' . percentage($sp['lost_' . $follow_up], $sp['total'], 1) . '</td>';

		echo '</tr>';
	}

	echo '</table>';

}
?>