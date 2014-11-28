<?php echo validation_errors() ?><style type="text/css">
	table.form tr th {
		width:175px;
	}

	.hint {
		font-size: 11px;
		color: #999;
	}
</style>


<div class="autosave-control" style="display: none;">

	<div class="header">
		<h2>Autosave</h2>
	</div>

	<div class="item">

		<div class="autosave-multi">
			<p>This form has been automatically saved. Choose which version of the details to use and click Restore.</p>
			<br>
			<table class="filter">
				<tr><td><select name="as_id"></select><td><input class="autosave-restore" value="multi" type="image" src="/img/btn/restore.png" alt="Restore" /></td></tr>
			</table>
		</div>

		<div class="autosave-single">
			<input type="hidden" name="as_id" value="">
			<table class="filter" style="width: 100%">
				<tr>
					<td>This form was automatically saved on <span class="date"></span>. Click Restore to use the saved details for this form.</td>
					<td><input class="autosave-restore" value="single" type="image" src="/img/btn/restore.png" alt="Restore" style="float:right" /></td>
				</tr>
			</table>
		</div>

	</div>

</div>


<form action="" method="post" id="monitoring_form_form" data-mode="<?php echo ( ! empty($monitoring_form) ? 'edit' : 'add') ?>">

	<?php if($this->session->userdata('tier_3')) : ?>

	<div class="header">
		<h2>Set on behalf of tier 2</h2>
	</div>

	<div class="item">

		<p>If you would like to set the following monitoring form on behalf of a tier 2 provider, please select the name of the provider below.</p>

		<table class="form">
			<tr>
				<th><label for="sp_id">Tier 2 provider</label></th>
				<td>
					<select name="sp_id" id="sp_id" class="sp_id">

						<option value="">-- I do not wish to set on behalf of a tier 2 provider --</option>

						<?php foreach($service_providers_select as $char => $sp_array) : ?>

						<optgroup label="<?php echo $char; ?>">

							<?php foreach($sp_array as $sp) : ?>

							<option value="<?php echo $sp['id']; ?>" <?php if(@$_GET['sp_id'] == $sp['id']) echo 'selected="selected"'; ?>><?php echo $sp['name']; ?></option>

							<?php endforeach; ?>

						</optgroup>

						<?php endforeach; ?>

					</select>
				</td>
			</tr>
		</table>

	</div>

	<?php endif; ?>

	<div class="header">
		<h2>Client information</h2>
	</div>

	<div class="item" style="position: relative">

		<div class="dupe_wrapper"></div>

		<p>Required fields are marked with an asterisk *</p>

		<table class="form form_narrow">

			<tr>
				<th><label for="nhs_number">NHS number</label></th>
				<td><input type="text" name="nhs_number" id="nhs_number" value="<?php echo htmlentities(@$monitoring_form['nhs_number'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="10" class="text" style="text-transform:uppercase;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="title">Title</label></th>
				<td>
					<select name="title" id="title" class="other_select">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['titles'] as $title) : ?>
						<option value="<?php echo $title; ?>" <?php if($title == @$monitoring_form['title']) echo 'selected="selected"'; ?>><?php echo $title; ?></option>
						<?php endforeach; ?>
					</select>

					<label for="title_other" class="other_label">Please specify</label>
					<input type="text" name="title_other" id="title_other" value="<?php echo htmlentities(@$monitoring_form['title_other'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="8" class="other_value text" style="text-transform:capitalize;" /><span class="asterix">*</span>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="fname">First name</label></th>
				<td><input type="text" name="fname" id="fname" value="<?php echo htmlentities(@$monitoring_form['fname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="text-transform:capitalize;" /><span class="asterix">*</span></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="sname">Surname</label></th>
				<td><input type="text" name="sname" id="sname" value="<?php echo htmlentities(@$monitoring_form['sname'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="text-transform:capitalize;" /><span class="asterix">*</span></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="gender">Gender</label></th>
				<td>
					<select name="gender" id="gender">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['genders'] as $gender) : ?>
						<option value="<?php echo $gender; ?>" <?php if($gender == @$monitoring_form['gender']) echo 'selected="selected"'; ?>><?php echo $gender; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="asterix">*</span>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="date_of_birth">Date of birth</label></th>
				<td>
					<select name="date_of_birth[day]" id="dob_date">
						<option value="">--</option>
						<?php for($i = 1; $i <= 31; $i++) : ?>
						<option value="<?php echo $i; ?>" <?php if($i == @$monitoring_form['date_of_birth_day']) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					 </select>

					 <select name="date_of_birth[month]" id="dob_month">
						<option value="">--</option>
						<?php foreach($form_elements['months'] as $month_number => $month_name) : ?>
						<option value="<?php echo $month_number + 1; ?>" <?php if(($month_number + 1) == @$monitoring_form['date_of_birth_month']) echo 'selected="selected"'; ?>><?php echo $month_name; ?></option>
						<?php endforeach; ?>
					 </select>

					 <select name="date_of_birth[year]" id="dob_year">
						<option value="">--</option>
						<?php for($i = (date('Y', time()) - 12); $i >= (date('Y', time()) - 100); $i--) : ?>
						<option value="<?php echo $i; ?>" <?php if($i == @$monitoring_form['date_of_birth_year']) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					 </select>
					 <span class="asterix">*</span>
				</td>
				<td class="e"></td>
			</tr>

			<tr class="vat">
				<th><label for="address">Address</label></th>
				<td><textarea name="address" id="address" cols="30" rows="4"><?php echo htmlentities(@$monitoring_form['address'], ENT_QUOTES, 'UTF-8'); ?></textarea><span class="asterix">*</span></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="post_code">Post code</label></th>
				<td><input type="text" name="post_code" id="post_code" value="<?php echo htmlentities(@$monitoring_form['post_code'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="text-transform:uppercase;" maxlength="8" /><span class="asterix">*</span></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="tel_daytime">Daytime tel</label></th>
				<td><input type="text" name="tel_daytime" id="tel_daytime" value="<?php echo htmlentities(@$monitoring_form['tel_daytime'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="tel_mobile">Mobile tel</label></th>
				<td><input type="text" name="tel_mobile" id="tel_mobile" value="<?php echo htmlentities(@$monitoring_form['tel_mobile'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="sms">SMS consent for any purpose</label></th>
				<td><input type="checkbox" name="sms" id="sms" value="1" <?php if(@$monitoring_form['sms']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="tel_alt">Alternative tel</label></th>
				<td><input type="text" name="tel_alt" id="tel_alt" value="<?php echo htmlentities(@$monitoring_form['tel_alt'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="email">Email address</label></th>
				<td><input type="text" name="email" id="email" value="<?php echo element('email', $monitoring_form) ?>" class="text" size="40" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="exempt_from_prescription_charge">Exempt from prescription charge</label></th>
				<td>
					<input type="checkbox" name="exempt_from_prescription_charge" id="exempt_from_prescription_charge" value="1" <?php if(@$monitoring_form['exempt_from_prescription_charge']) echo 'checked="checked"'; ?> />
					<label for="exempt_from_prescription_charge"><span class="hint">Record here only those able to prove that they are eligible to receive free prescriptions</span></label>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="pregnant">Pregnant</label></th>
				<td><input type="checkbox" name="pregnant" id="pregnant" value="1" <?php if(@$monitoring_form['pregnant']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="breastfeeding">Breastfeeding</label></th>
				<td><input type="checkbox" name="breastfeeding" id="breastfeeding" value="1" <?php if(@$monitoring_form['breastfeeding']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th>
					<label for="occupation_code">Occupation code</label>
					<small><a href="/img/style/occupation_codes.jpg" class="fancybox">Help with this</a></small>
				</th>
				<td>
					<select name="occupation_code" id="occupation_code">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['occupation_codes'] as $occupation_code) : ?>
						<option value="<?php echo $occupation_code; ?>" <?php if($occupation_code == @$monitoring_form['occupation_code']) echo 'selected="selected"'; ?>><?php echo $occupation_code; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="asterix">*</span>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="ethnic_group">Ethnic group</label></th>
				<td>
					<select name="ethnic_group" id="ethnic_group">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['ethnic_groups'] as $category => $ethnic_groups) : ?>

						<optgroup label="<?php echo $category; ?>">

							<?php foreach($ethnic_groups as $ethnic_group) : ?>
							<option value="<?php echo $ethnic_group; ?>" <?php if($ethnic_group == @$monitoring_form['ethnic_group']) echo 'selected="selected"'; ?>><?php echo $ethnic_group; ?></option>
							<?php endforeach; ?>

						</optgroup>

						<?php endforeach; ?>
					</select>

					<span class="asterix">*</span>
				</td>
				<td class="e"></td>
			</tr>
			<tr>
				<th><label for="ci_gp">GP practice</label></th>
				<td><input type="text" name="ci_gp" id="ci_gp" value="<?php echo htmlentities(@$monitoring_form['ci_gp'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:350px;" /></td>
				<td class="e"></td>
			</tr>
			<tr>
				<th><label for="gp_personal_name">GP name</label></th>
				<td><input type="text" name="gp_personal_name" id="gp_personal_name" value="<?php echo htmlentities(@$monitoring_form['gp_personal_name'], ENT_QUOTES, 'UTF-8'); ?>" class="text" /></td>
				<td class="e"></td>
			</tr>
		</table>

	</div>

	<div class="header">
		<h2>Health</h2>
	</div>

	<div class="item">

		<table class="form">

			<tr class="vat">
				<th><label>Health problems</label></th>
				<td class="checks">
					<?php foreach ($form_elements['health_problems'] as $hp): ?>
					<label for="hp_<?php echo $hp['hp_id'] ?>">
						<?php
						echo form_checkbox(array(
							'name' => 'health_problems[]',
							'id' => 'hp_' . $hp['hp_id'],
							'value' => $hp['hp_id'],
							'checked' => in_array($hp['hp_id'], element('health_problems_ids_array', $monitoring_form, array())),
						));
						echo $hp['hp_name'];
						?>
					</label>
					<?php endforeach; ?>
				</td>
			</tr>

			<tr>
				<th><label for="health_problems_other">Other</label></th>
				<td><?php echo form_input(array(
					'name' => 'health_problems_other',
					'id' => 'health_problems_other',
					'class' => 'text',
					'value' => element('health_problems_other', $monitoring_form),
				)); ?></td>
			</tr>

			<tr>
				<th></th>
				<td>
					<input type="hidden" name="health_problems_not_reported" value="0">
					<label for="health_problems_not_reported">
						<input type="checkbox" name="health_problems_not_reported" id="health_problems_not_reported" value="1" <?php if(@$monitoring_form['health_problems_not_reported']) echo 'checked="checked"'; ?> />
						Not Reported
					</label>
				</td>
			</tr>

			<tr>
				<td colspan="2"><hr size="1" color="#dddddd" /></td>
			</tr>

			<tr>
				<th><label for="alcohol">Drinks alcohol</label></th>
				<td>
					<input type="hidden" name="alcohol" value="0">
					<label for="alcohol">
						<input type="checkbox" name="alcohol" id="alcohol" value="1" <?php if(@$monitoring_form['alcohol']) echo 'checked="checked"'; ?> />
						Yes
					</label>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th></th>
				<td>
					<input type="hidden" name="alcohol_not_reported" value="0">
					<label for="alcohol_not_reported">
						<input type="checkbox" name="alcohol_not_reported" id="alcohol_not_reported" value="1" <?php if(@$monitoring_form['alcohol_not_reported']) echo 'checked="checked"'; ?> />
						Not Reported
					</label>
				</td>
			</tr>

		</table>

	</div>

	<div class="header">
		<h2>Monitoring information</h2>
	</div>

	<div class="item">

		<table class="form" style="width: 100%">

			<tr>
				<th><label for="marketing">How client heard about service</label></th>
				<td>
					<?php
					$value = element('ms_id', $monitoring_form);
					if ($monitoring_form && empty($value))
					{
						$value = 'Other';
					}

					$form_elements['marketing_sources'] += array('Other' => 'Other&hellip;');
					echo form_dropdown('ms_id', $form_elements['marketing_sources'], set_value('ms_id', $value), 'class="other_select" id="ms_id"');
					?>

					<label for="marketing_other" class="other_label">Please specify</label>
					<input type="text" name="marketing_other" id="marketing_other" value="<?php echo htmlentities(@$monitoring_form['marketing_other'], ENT_QUOTES, 'UTF-8'); ?>" class="other_value text" /><span class="asterix">*</span>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="date_of_last_tobacco_use">Date of last tobacco use</label></th>
				<td><input type="text" name="date_of_last_tobacco_use" id="date_of_last_tobacco_use" value="<?php echo htmlentities(@$monitoring_form['date_of_last_tobacco_use_format'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:100px;" /><span class="asterix">*</span></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="agreed_quit_date">Agreed quit date</label></th>
				<td><input type="text" name="agreed_quit_date" id="agreed_quit_date" value="<?php echo htmlentities(@$monitoring_form['agreed_quit_date_format'], ENT_QUOTES, 'UTF-8'); ?>" class="datepicker text" /><span class="asterix">*</span></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="date_of_4_week_follow_up">Date of 4 week follow up</label></th>
				<td><input type="text" name="date_of_4_week_follow_up" id="date_of_4_week_follow_up" value="<?php echo htmlentities(@$monitoring_form['date_of_4_week_follow_up_format'], ENT_QUOTES, 'UTF-8'); ?>" class="datepicker text" /><span class="asterix">*</span></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="date_of_12_week_follow_up">Date of 12 week follow up</label></th>
				<td><input type="text" name="date_of_12_week_follow_up" id="date_of_12_week_follow_up" value="<?php echo str_replace('N/A', '', htmlentities(@$monitoring_form['date_of_12_week_follow_up_format'], ENT_QUOTES, 'UTF-8')); ?>" class="datepicker text" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th><label for="intervention_type">Type of intervention delivered</label></th>
				<td>
					<select name="intervention_type" id="intervention_type" class="other_select">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['intervention_types'] as $intervention_type) : ?>
						<option value="<?php echo $intervention_type; ?>" <?php if($intervention_type == @$monitoring_form['intervention_type']) echo 'selected="selected"'; ?>><?php echo $intervention_type; ?></option>
						<?php endforeach; ?>
					</select>

					<span class="asterix">*</span>

					<label for="intervention_type_other" class="other_label">Please specify</label>
					<input type="text" name="intervention_type_other" id="intervention_type_other" value="<?php echo htmlentities(@$monitoring_form['intervention_type_other'], ENT_QUOTES, 'UTF-8'); ?>" class="other_value text" />
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="3"><hr size="1" color="#dddddd" /></td>
			</tr>

			<tr>
				<th><label>Type of pharmacological support used</label></th>
				<td>
					<?php echo form_support_dropdown('support_1', set_value('support_1', element('support_1', $monitoring_form)), 'id="support_1"'); ?>
					<?php echo form_support_dropdown('support_2', set_value('support_2', element('support_2', $monitoring_form)), 'id="support_2"'); ?>
				</td>
				<td class="e"></td>
			</tr>

			<tr>
				<th></th>
				<td>
					<input type="hidden" name="support_none" value="0">
					<label for="support_none">
						<input type="checkbox" name="support_none" id="support_none" value="1" <?php if (element('support_none', $monitoring_form)) echo 'checked="checked"'; ?> class="tickbox_activator" data-group="support_method" data-not="true" />
						No licensed product prescribed
					</label>
				</td>
			</tr>

			<tr class="vat tickbox_activate_content" data-group="support_method">
				<th><label>How they were used</label></th>
				<td>
					<p>Where more than one pharmacotherapy has been used were these:</p><br>
					<input type="hidden" name="support_method" value="">
					<?php foreach ($form_elements['support_methods'] as $value => $method): ?>
					<label for="support_method_<?php echo $value ?>" class="check">
						<input type="radio" name="support_method" id="support_method_<?php echo $value ?>" value="<?php echo $value ?>" <?php echo set_radio('support_method', $value, element('support_method', $monitoring_form, 2) == $value) ?> />
						<?php echo $method['description'] ?>
					</label><br>
					<?php endforeach ?>
				</td>
				<td class="e"></td>
			</tr>

			<tr id="mf_gp_code_tr">
				<th><label>Prescribing GP practice code</label></th>

				<td><input type="text" name="mf_gp_code" id="mf_gp_code" value="<?php echo htmlentities(@$monitoring_form['mf_prescribing_gp'], ENT_QUOTES, 'UTF-8'); ?>" class="text" style="width:350px;" /></td>
				<td class="e"></td>
			</tr>

			<tr>
				<td colspan="3"><hr size="1" color="#dddddd" /></td>
			</tr>

			<tr class="vat">
				<th>Unlicensed Nicotine Containing Product
				<br><span class="hint">e.g. e-cigarettes</span></th>
				<td>
					<input type="hidden" name="uncp" value="0">
					<label for="uncp">
						<input type="checkbox" name="uncp" id="uncp" value="1" <?php if (element('uncp', $monitoring_form) == 1) echo 'checked="checked"'; ?> class="tickbox_activator" data-group="uncp" />
						Unlicensed NCP used
					</label>
					<br>
					<select name="uncp_method" id="uncp_method" class="tickbox_activate_content" data-group="uncp" style="margin-top: 10px">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['uncp_methods'] as $value => $label) : ?>
						<option value="<?php echo $value; ?>" <?php if($value == element('uncp_method', $monitoring_form)) echo 'selected="selected"'; ?>><?php echo $label; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>


			<tr>
				<td colspan="3"><hr size="1" color="#dddddd" /></td>
			</tr>

			<?php
			// Time checking for treatment outcomes - only valid on or after the quit dates
			$now = time();
			$date_4_week = ( ! empty($monitoring_form['date_of_4_week_follow_up'])) ? strtotime(@$monitoring_form['date_of_4_week_follow_up']) : NULL;
			$date_12_week = ( ! empty($monitoring_form['date_of_12_week_follow_up'])) ? strtotime(@$monitoring_form['date_of_12_week_follow_up']) : NULL;
			?>

			<?php if ($date_4_week !== NULL && $now >= $date_4_week): ?>
			<tr>
				<th><label for="treatment_outcome_4">Treatment outcome (4 weeks)</label></th>
				<td>
					<select name="treatment_outcome_4" id="treatment_outcome_4">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['treatment_outcomes'] as $treatment_outcome) : ?>
						<option value="<?php echo $treatment_outcome; ?>" <?php if($treatment_outcome == @$monitoring_form['treatment_outcome_4']) echo 'selected="selected"'; ?>><?php echo $treatment_outcome; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td class="e"></td>
			</tr>
			<?php endif; ?>

			<tr class="js-co-quit hidden">
				<th><label for="co_quit">CO reading</label></th>
				<td>
					<input type="text" name="co_quit" id="co_quit" value="<?php echo element('co_quit', $monitoring_form) ?>" class="text" size="2" />
					<span>ppm</span>
				</td>
			</tr>

			<?php if ($date_12_week !== NULL && $now >= $date_12_week): ?>
			<tr>
				<th><label for="treatment_outcome_12">Treatment outcome (12 weeks)</label></th>
				<td>
					<select name="treatment_outcome_12" id="treatment_outcome_12">
						<option value="">-- Please select --</option>
						<?php foreach($form_elements['treatment_outcomes'] as $treatment_outcome) : ?>
						<option value="<?php echo $treatment_outcome; ?>" <?php if($treatment_outcome == @$monitoring_form['treatment_outcome_12']) echo 'selected="selected"'; ?>><?php echo $treatment_outcome; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td class="e"></td>
			</tr>
			<?php endif; ?>

			<tr>
				<th><label for="advisor">Advisor</label></th>
				<td>
					<?php $advisor_code = $this->session->userdata('advisor_code')?>
					<?php if (!empty($advisor_code)): ?>
						<input type="hidden" name="advisor_code" id="advisor_code" value="<?php echo $advisor_code ?>" />
						<input type="text" name="advisor" id="advisor" value="<?php echo $this->session->userdata('advisor_code') ?>" class="text" size="30" disabled />
					<?php else: ?>
						<input type="hidden" name="a_id" id="a_id" value="<?php echo element('a_id', $monitoring_form) ?>" />
						<input type="text" name="advisor" id="advisor" value="<?php echo element('advisor', $monitoring_form) ?>" class="text" size="30" />
					<?php endif ?>
				</td>
				<td class="e"></td>
			</tr>

		</table>

	</div>

	<?php if($this->session->userdata('tier_3')) : ?>

	<div id="tier_3">

		<div class="header">
			<h2>Tier 3</h2>
		</div>

		<div class="item">

			<p>As a tier 3 provider, you are required to provide the following extra information.</p>

			<table class="form">

				<tr>
					<th><label for="referral_source">Referral source</label></th>
					<td>
						<select name="referral_source" id="referral_source">
							<option value="">-- Please select --</option>
							<?php foreach($form_elements['referral_sources'] as $referral_source) : ?>
							<option value="<?php echo $referral_source; ?>" <?php if($referral_source == @$monitoring_form['referral_source']) echo 'selected="selected"'; ?>><?php echo $referral_source; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="asterix">*</span>
					</td>
					<td class="e"></td>
				</tr>

				<tr>
					<th><label for="function">Function</label></th>
					<td>
						<select name="function" id="function">
							<option value="">-- Please select --</option>
							<?php foreach($form_elements['functions'] as $function) : ?>
							<option value="<?php echo $function; ?>" <?php if($function == @$monitoring_form['function']) echo 'selected="selected"'; ?>><?php echo $function; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="asterix">*</span>
					</td>
					<td class="e"></td>
				</tr>

				<tr>
					<th><label for="previously_treated">Previously treated</label></th>
					<td><input type="checkbox" name="previously_treated" id="previously_treated" value="1" <?php if(@$monitoring_form['previously_treated']) echo 'checked="checked"'; ?> /></td>
					<td class="e"></td>
				</tr>

			</table>

		</div>

	</div>

	<?php endif; ?>

	<div class="header">
		<h2>Notes</h2>
	</div>

	<div class="item">

		<table class="form">

			<tr class="vat">
				<th><label for="notes">Any notes relating to client, monitoring form or pharmacological support.</label></th>
				<td><textarea name="notes" id="notes" cols="80" rows="11"><?php echo htmlentities(@$monitoring_form['notes'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
				<td class="e"></td>
			</tr>

		</table>

	</div>

	<div class="header">
		<h2>Save</h2>
	</div>

	<div class="item">

		<table class="form">

			<tr>
				<th><label for="consent">Client consents to treatment, follow up and pass on of outcome data to GP</label></th>
				<td><input type="checkbox" name="consent" id="consent" value="1" <?php if(@$monitoring_form['consent']) echo 'checked="checked"'; ?> /></td>
				<td class="e"></td>
			</tr>

		</table>

		<img class="loading-save" src="/img/style/ajax-load-schedule.gif" alt="Saving..." style="display:none; float:right;" />
		<input type="image" class="btn-save" src="/img/btn/save.png" alt="Save" style="float:right;" />

		<div class="clear"></div>

	</div>

</form>

<a href="/service-providers/monitoring-forms" class="back"><img src="/img/btn/back.png" alt="Back" /></a>
