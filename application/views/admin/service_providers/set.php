<style type="text/css">
	table.form tr th {
		width:150px;
	}
</style>
<div class="header">
	<h2><?php echo $title; ?></h2>
</div>

<div class="item">

	<form action="" method="post" id="service_provider_form">

		<table class="form">

			<tr>
				<th><label for="name">Name</label></th>
				<td><input type="text" name="name" id="name" value="<?php echo htmlentities(@$service_provider['name'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="post_code">Post code</label></th>
				<td><input type="text" name="post_code" id="post_code" value="<?php echo htmlentities(@$service_provider['post_code'], ENT_QUOTES, 'UTF-8'); ?>" class="text" maxlength="8" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="location">Location/setting</label></th>
				<td>
					<select name="location" id="location" class="other_select">
						<option value="">-- Please select --</option>

						<?php foreach($locations as $location) : ?>

						<option value="<?php echo $location; ?>" <?php if(@$service_provider['location'] == $location) echo 'selected="selected"'; ?>><?php echo $location; ?></option>

						<?php endforeach; ?>

						</option>
					</select>

					<label for="location_other" class="other_label">Please specify</label>
					<input type="text" name="location_other" id="location_other" value="<?php echo set_value('location_other', element('location_other', $service_provider)) ?>" maxlength="64" class="other_value text" style="text-transform:capitalize;" /><span class="asterix">*</span>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="department">Department/ward</label></th>
				<td><input type="text" name="department" id="department" value="<?php echo htmlentities(@$service_provider['department'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="venue">Venue</label></th>
				<td><input type="text" name="venue" id="venue" value="<?php echo htmlentities(@$service_provider['venue'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="telephone">Telephone</label></th>
				<td><input type="text" name="telephone" id="telephone" value="<?php echo htmlentities(@$service_provider['telephone'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="advisor_code">Advisor code</label></th>
				<td><input type="text" name="advisor_code" id="advisor_code" value="<?php echo htmlentities(@$service_provider['advisor_code'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="provider_code">Provider code</label></th>
				<td><input type="text" name="provider_code" id="provider_code" value="<?php echo htmlentities(@$service_provider['provider_code'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="cost_code">Cost code</label></th>
				<td><input type="text" name="cost_code" id="cost_code" value="<?php echo htmlentities(@$service_provider['cost_code'], ENT_QUOTES, 'UTF-8'); ?>" class="text"></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pct_id">Local Authority</label></th>
				<td>
					<select name="pct_id" id="pct_id">

						<option value="">-- Unassigned --</option>

						<?php foreach($pcts as $pct) : ?>
						<option value="<?php echo $pct['id']; ?>" <?php if(@$service_provider['pct_id'] == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
						<?php endforeach; ?>

					</select>
				</td>
			</tr>

			<tr class="vat">
				<th>
					<label for="group_id">Group</label>
					<small>Ensure the group is for the correct local authority, or "All"</small>
				</th>
				<td>
					<select name="group_id" id="group_id">

						<option value="">-- Please Select --</option>

						<?php foreach($groups as $group) : ?>
						<option value="<?php echo $group['id']; ?>" <?php if(@$service_provider['group_id'] == $group['id']) echo 'selected="selected"'; ?>><?php echo $group['name']; ?></option>
						<?php endforeach; ?>

					</select>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th>
					<label for="claim_options_enabled">Assign specific claim costs</label>
					<small>This set specific claim options for this serivce provider.</small>
				</th>
				<td><input type="checkbox" name="claim_options_enabled" id="claim_options_enabled" value="1" <?php if(@$service_provider['claim_options']) echo 'checked="checked"'; ?> /></td>
			</tr>
			<tr class="claim_options_tr">
				<th><label for="claim_options_initial">Initial claim clost</label></th>
				<td>&pound; <input type="text" name="claim_options_initial" id="claim_options_initial" value="<?php echo @$service_provider['claim_options']['initial']; ?>" class="claim_options text" style="width:50px;" maxlength="5" /></td>
				<td class="e"></td>
			</tr>
			<tr class="claim_options_tr">
				<th><label for="claim_4_week">4 week quit claim</label></th>
				<td>&pound; <input type="text" name="claim_4_week" id="claim_4_week" value="<?php echo @$service_provider['claim_options']['claim_4_week']; ?>" class="claim_options text" style="width:50px;" maxlength="5" /></td>
				<td class="e"></td>
			</tr>
			<tr class="claim_options_tr">
				<th><label for="claim_12_week">12 week quit claim</label></th>
				<td>&pound; <input type="text" name="claim_12_week" id="claim_12_week" value="<?php echo @$service_provider['claim_options']['claim_12_week']; ?>" class="claim_options text" style="width:50px;" maxlength="5" /></td>
				<td class="e"></td>
			</tr>
			<tr class="claim_options_tr">
				<th>
					<label for="claim_options_do_not_claim">Do not claim</label>
					<small>If this option is selected, this specific service provider will not submit claims.</small>
				</th>
				<td><input type="checkbox" name="claim_options_do_not_claim" id="claim_options_do_not_claim" value="1" <?php if(@$service_provider['claim_options']['do_not_claim']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th>
					<label for="tier_3">Tier 3</label>
					<small>Set as a tier 3 service provider</small>
				</th>
				<td><input type="checkbox" name="tier_3" id="tier_3" value="1" <?php if(@$service_provider['tier_3']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align:right;"><input type="image" src="/img/btn/save.png" alt="Save" /></td>
			</tr>

		</table>

	</form>

</div>

<a href="/admin/service-providers" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
