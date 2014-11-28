<style type="text/css">
	ul.sortable {
		margin-top:10px;
		margin-bottom:15px;
		list-style:none;
	}

		ul.sortable li {
			height:25px;
			line-height:25px;
			padding:0px 10px;
			background-color:#eaeaea;
			margin:5px 10px;
			border:1px solid #ccc;
		}

			ul.sortable li div {
				background:url(/img/icons/move.png) no-repeat left;
				padding-left:23px;
			}

				ul.sortable li div img {
					float:right;
					margin-top:5px;
				}

	table.form label {
		display:block;
	}
</style>
<div class="header">
	<h2>Export schemas</h2>
</div>

<div class="item">

	<p>Export schemas allow you to specify which fields of information you would like to appear in a CSV output. For example, you may wish to create a "Non-patient identifiable" schema which does not include any fields that contain patient information. By default, you will always have the option to export the entire database to CSV.

</div>

<div class="panel_left">

	<div class="header">
		<h2>Monitoring form schemas</h2>
	</div>

	<div class="item">

		<form action="" method="get">

			<select name="mf_schema_id" class="schemas">

				<option value="">-- Please select --</option>

				<?php foreach($monitoring_form_schemas as $schema) : ?>

				<option value="<?php echo $schema['id']; ?>" <?php if($schema['id'] == @$_GET['mf_schema_id']) echo 'selected="selected"'; ?>><?php echo htmlentities($schema['title'], ENT_QUOTES, 'UTF-8'); ?></option>

				<?php endforeach; ?>

			</select>

		</form>

	</div>

</div>

<div class="panel_right">

	<div class="header">
		<h2>Claim schemas</h2>
	</div>

	<div class="item">

		<form action="" method="get">

			<select name="claim_schema_id" class="schemas">

				<option value="">-- Please select --</option>

				<?php foreach($claim_schemas as $schema) : ?>

				<option value="<?php echo $schema['id']; ?>" <?php if($schema['id'] == @$_GET['claim_schema_id']) echo 'selected="selected"'; ?>><?php echo htmlentities($schema['title'], ENT_QUOTES, 'UTF-8'); ?></option>

				<?php endforeach; ?>

			</select>

		</form>

	</div>

</div>

<div class="panel_left">

	<div class="header">
		<h2>Monitoring forms</h2>
	</div>

	<div class="item">

		<form action="" method="post" id="mf_form">

			<input type="hidden" name="type" value="monitoring_forms" />

			<table class="form">

				<tr>
					<td><label>Name</label><input type="text" name="title" class="text" style="width:250px;" maxlength="32" value="<?php echo htmlentities(@$mf_schema['title'], ENT_QUOTES, 'UTF-8'); ?>" /></td>
				</tr>

				<tr>
					<td>
						<label>Local Authority</label>

						<?php if ($pct_id) echo form_hidden('pct_id', $pct_id); ?>
						<select name="pct_id" id="pct_id" <?php if ($pct_id): ?> disabled="disabled" <?php endif; ?>>

							<option value="">-- All --</option>

							<?php foreach($pcts as $pct) : ?>
							<option value="<?php echo $pct['id']; ?>" <?php if(@$mf_schema['pct_id'] == $pct['id'] || $pct_id == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
							<?php endforeach; ?>

						</select>
					</td>
				</tr>

				<tr>
					<td>
						<label>Fields</label>
						<select name="mf_field_name" id="mf_field_name" class="va_middle">

							<option value="">-- Please select --</option>

							<optgroup label="Monitoring form">

								<option value="id">Monitoring form ID</option>
								<option value="date_created_format">Date created</option>
								<option value="marketing">Marketing</option>
								<option value="date_of_last_tobacco_use_format">Date of last tobacco use</option>
								<option value="agreed_quit_date_format">Agreed quit date</option>
								<option value="date_of_4_week_follow_up_format">Date of 4 week follow up</option>
								<option value="date_of_12_week_follow_up_format">Date of 12 week follow up</option>
								<option value="intervention_type">Intervention type</option>
								<option value="support_1">Pharmacological support 1</option>
								<option value="support_2">Pharmacological support 2</option>
								<option value="support_none">No product prescribed</option>
								<option value="uncp">Unlicensed NCP</option>
								<option value="treatment_outcome_4">Treatment outcome (4 weeks)</option>
								<option value="treatment_outcome_12">Treatment outcome (12 weeks)</option>
								<option value="referral_source">Referral source</option>
								<option value="function">Function</option>
								<option value="previously_treated">Previously treated</option>
								<option value="notes">Notes</option>
								<option value="consent">Client consents to pass on of data to GP</option>

							</optgroup>

							<optgroup label="Service provider">

								<option value="sp_name">Provider name</option>
								<option value="advisor_code">Advisor code</option>
								<option value="provider_code">Provider code</option>
								<option value="cost_code">Cost code</option>
								<option value="sp_post_code">Provider post code</option>
								<option value="group_name">Group</option>
								<option value="pct">Local Authority</option>
								<option value="department">Department/ward</option>
								<option value="location">Location/setting</option>
								<option value="venue">Venue</option>
								<option value="telephone">Telephone</option>

							</optgroup>

							<optgroup label="Client">

								<option value="nhs_number">NHS number</option>
								<option value="client_name">Client name</option>
								<option value="gender">Gender</option>
								<option value="date_of_birth">Date of birth</option>
								<option value="address">Address</option>
								<option value="post_code">Post code</option>
								<option value="tel_daytime">Daytime telephone</option>
								<option value="tel_mob">Mobile telephone</option>
								<option value="tel_alt">Alternative telephone</option>
								<option value="exempt_from_prescription_charge">Exempt from prescription charge</option>
								<option value="pregnant">Pregnant</option>
								<option value="breastfeeding">Breastfeeding</option>
								<option value="occupation_code">Occupation code</option>
								<option value="ethnic_group">Ethnic group</option>
								<option value="gp_name">GP name</option>
								<option value="gp_address">GP address</option>
								<option value="gp_code">GP code</option>

							</optgroup>

						</select>

						<a href="#" class="field_add" rel="mf"><img src="/img/btn/add.png" alt="Add" class="va_middle" /></a>
				</tr>

			</table>

			<ul id="mf_sortable" class="sortable">

				<?php
				if(@$mf_schema)
				{
					foreach($mf_schema['export_schema'] as $field_name => $description)
					{
						echo '<li><div><input type="hidden" name="fields[' . $field_name . ']" value="' . $description . '" />' . $description . '<a href="#"><img src="/img/icons/cross.png" alt="Delete" /></a></div></li>';
					}
				}
				?>

			</ul>

			<input type="image" src="/img/btn/save.png" alt="Save" style="float:right;" />

			<?php if(@$mf_schema) : ?>

			<a href="?delete_schema=<?php echo $mf_schema['id']; ?>" class="action" title="Click OK to delete this schema." style="float:right; margin-right:15px;"><img src="/img/btn/delete.png" alt="Delete" /></a>

			<?php endif; ?>

		</form>

		<div class="clear"></div>

	</div>

</div>

<div class="panel_right">

	<div class="header">
		<h2>Claims</h2>
	</div>

	<div class="item">

		<form action="" method="post" id="mf_form">

			<input type="hidden" name="type" value="monitoring_form_claims" />

			<table class="form">

				<tr>
					<td><label>Name</label><input type="text" name="title" class="text" style="width:250px;" maxlength="32" value="<?php echo htmlentities(@$claim_schema['title'], ENT_QUOTES, 'UTF-8'); ?>" /></td>
				</tr>

				<tr>
					<td>
						<label>Local Authority</label>

						<?php if ($pct_id) echo form_hidden('pct_id', $pct_id); ?>
						<select name="pct_id" id="pct_id" <?php if ($pct_id): ?> disabled="disabled" <?php endif; ?>>

							<option value="">-- All --</option>

							<?php foreach($pcts as $pct) : ?>
							<option value="<?php echo $pct['id']; ?>" <?php if(@$claim_schema['pct_id'] == $pct['id'] || $pct_id == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
							<?php endforeach; ?>

						</select>
					</td>
				</tr>

				<tr>
					<td>
						<label>Fields</label>
						<select name="mfc_field_name" id="mfc_field_name" class="va_middle">

							<option value="">-- Please select --</option>

							<optgroup label="Service provider">

								<option value="sp_name">Provider name</option>
								<option value="advisor_code">Advisor code</option>
								<option value="provider_code">Provider code</option>
								<option value="cost_code">Cost code</option>
								<option value="sp_post_code">Provider post code</option>
								<option value="group_name">Group</option>
								<option value="pct">Local Authority</option>
								<option value="department">Department/ward</option>
								<option value="location">Location/setting</option>
								<option value="venue">Venue</option>
								<option value="telephone">Telephone</option>

							</optgroup>

							<optgroup label="Claim">

								<option value="monitoring_form_id">Monitoring form ID</option>
								<option value="claim_type">Claim type</option>
								<option value="date_of_claim_format">Date of claim</option>
								<option value="status">Status</option>
								<option value="cost">Cost</option>

							</optgroup>

						</select>

						<a href="#" class="field_add" rel="mfc"><img src="/img/btn/add.png" alt="Add" class="va_middle" /></a>
				</tr>

			</table>

			<ul id="mfc_sortable" class="sortable">

				<?php
				if(@$claim_schema)
				{
					foreach($claim_schema['export_schema'] as $field_name => $description)
					{
						echo '<li><div><input type="hidden" name="fields[' . $field_name . ']" value="' . $description . '" />' . $description . '<a href="#"><img src="/img/icons/cross.png" alt="Delete" /></a></div></li>';
					}
				}
				?>

			</ul>

			<input type="image" src="/img/btn/save.png" alt="Save" style="float:right;" />

			<?php if(@$claim_schema) : ?>

			<a href="?delete_schema=<?php echo $claim_schema['id']; ?>" class="action" title="Click OK to delete this schema." style="float:right; margin-right:15px;"><img src="/img/btn/delete.png" alt="Delete" /></a>

			<?php endif; ?>

		</form>

		<div class="clear"></div>

	</div>

</div>

<div class="clear"></div>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
