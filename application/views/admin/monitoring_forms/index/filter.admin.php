<table class="filter">

	<tr>
		<th><label for="quarter">Quarter</label></th>
		<th><label for="date_from">Date from</label></th>
		<th><label for="date_to">Date to</label></th>
		<th><label for="id">Record ID</label></th>
		<th><label for="fname_like">Client first name</label></th>
		<th><label for="sname_like">Client surname</label></th>
	</tr>

	<tr>
		<td>
			<?php echo quarters_dropdown('quarter', 'quarter', '2010', 'd/m/Y', $this->input->get('quarter'), 'class="js-quarters"') ?>
		</td>
		<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
		<td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
		<td><input type="text" name="id" id="id" value="<?php echo @$_GET['id']; ?>" class="text" size="5" /></td>
		<td><input type="text" name="fname_like" id="fname_like" value="<?php echo $this->input->get('fname_like'); ?>" class="text" size="10" /></td>
		<td><input type="text" name="sname_like" id="sname_like" value="<?php echo $this->input->get('sname_like'); ?>" class="text" size="10" /></td>
	</tr>

</table>

<table class="filter" style="margin-top:10px;">

	<tr>

		<th><label for="date_type">Date type</label></th>
		<th><label for="sp_id">Service provider</label></th>
		<th><label for="group_id">Local Authority</label></th>
		<th><label for="location">Location/setting</label></th>
	</tr>

	<tr>

		<td><select name="date_type">
			<option value="qds" <?php if(@$_GET['date_type'] == 'qds') { echo 'selected="selected"'; } ?>>Agreed Quit Date</option>
			<option value="dc" <?php if(@$_GET['date_type'] == 'dc') { echo('selected="selected"'); } ?>>Date Created</option>
		</select></td>

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
			<select name="pct_id" id="pct_id" <?php if ($pct_id) echo 'disabled="disabled"' ?>>
				<option value="">-- All --</option>
				<?php foreach($pcts_select as $pct) : ?>
					<option value="<?php echo $pct['id']; ?>" <?php if(@$_GET['pct_id'] == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name_truncated']; ?></option>
				<?php endforeach; ?>
			</select>
		</td>

		<td>
			<select name="location" id="location">
				<option value="">-- All --</option>
				<?php foreach($locations as $location) : ?>
					<option value="<?php echo $location; ?>" <?php if(@$_GET['location'] == $location) echo 'selected="selected"'; ?>><?php echo $location; ?></option>
				<?php endforeach; ?>
			</select>
		</td>


	</tr>

</table>

<table class="filter" style="margin-top:10px; width: 100%">

	<tr>
		<th><label for="follow_up">Follow up outcome</label></th>
		<th><label for="treatment_outcome">Treatment outcome</label></th>
		<th><label for="advisor_code">Advisor Code</label></th>
		<th><label for="pp">Results per page</label></th>
		<th colspan="2"></th>
	</tr>

	<tr>
		<td>
			<select name="follow_up">
				<option value="" <?php echo (element('follow_up', $_GET, NULL) === NULL) ? 'selected="selected"' : ''; ?>>(Not set)</option>
				<option value="4" <?php echo (@$_GET['follow_up'] == 4) ? 'selected="selected"' : ''; ?>>4 week</option>
				<option value="12" <?php echo (@$_GET['follow_up'] == 12) ? 'selected="selected"' : ''; ?>>12 week</option>
			</select>
		</td>
		<td>
			<select name="treatment_outcome" id="treatment_outcome">
				<option value="">-- Please select --</option>
				<?php foreach($treatment_outcomes as $treatment_outcome) : ?>
					<option value="<?php echo $treatment_outcome; ?>" <?php if($treatment_outcome == @$_GET['treatment_outcome']) echo 'selected="selected"'; ?>><?php echo $treatment_outcome; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<input type="text" name="advisor_code" id="advisor_code" value="<?php echo $this->input->get('advisor_code'); ?>" class="text" size="10" maxlength="7" />

			<?php /* HEY LOOK! ITS A DROPDOWN FOR ALLL THE ADVISORS!!! YAY!
			<select name="advisor_code" id="advisor_code">
				<option value="">-- Please select --</option>
				<?php foreach($advisors as $advisor) : ?>
					<option value="<?php echo $advisor['advisor_code']; ?>" <?php if($advisor['advisor_code'] == @$_GET['advisor_code']) echo 'selected="selected"'; ?>>
						<?php echo $advisor['advisor']; ?>
					</option>
				<?php endforeach; ?>
			</select>
			*/ ?>
		</td>
		<td>
			<select name="pp" id="pp">
				<?php foreach($pp as $pp) : ?>
					<option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td><input type="image" src="/img/btn/filter.png" alt="Filter" /></td>
		<td><a href="/admin/monitoring-forms"><img src="/img/btn/clear.png" alt="Clear" /></a></td>
	</tr>

</table>


