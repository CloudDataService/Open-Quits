<style type="text/css">
	h2 {
		margin:5px 0px;
	}
	table.form tr th {
		width: 250px;
	}

	table.form tr th,
	table.form tr td {
		padding: 6px 7px;
	}
</style>
<h1>Monitoring form #<?php echo $monitoring_form['id']; ?></h1>

<h2>Service provider</h2>

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
		<th>Department/ward</th>
		<td><?php echo ($service_provider['department'] ? $service_provider['department'] : 'N/A'); ?></td>
	</tr>

	<tr>
		<th>Telephone</th>
		<td><?php echo ($service_provider['telephone'] ? $service_provider['telephone'] : 'N/A'); ?></td>
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
		<th>PCT</th>
		<td><?php echo $service_provider['pct']; ?></td>
	</tr>

	<tr>
		<th>Venue</th>
		<td><?php echo ($service_provider['venue'] ? $service_provider['venue'] : 'N/A'); ?></td>
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

</table>

<h2>Client information</h2>

<table class="form vat">

	<?php if ( ! $pct_id): ?>
	<tr>
		<th>NHS number</th>
		<td><?php echo $monitoring_form['nhs_number']; ?></td>
	</tr>

	<tr>
		<th>Name</th>
		<td><?php echo ($monitoring_form['title_other'] ? $monitoring_form['title_other'] : $monitoring_form['title']) . ' ' . $monitoring_form['fname'] . ' ' . $monitoring_form['sname'] ?></td>
	</tr>

	<tr>
		<th>Gender</th>
		<td><?php echo $monitoring_form['gender']; ?></td>
	</tr>

	<tr>
		<th>Date of birth</th>
		<td><?php echo $monitoring_form['date_of_birth_format']; ?></td>
		<td class="e"></td>
	</tr>

	<tr>
		<th>Address</th>
		<td><?php echo nl2br($monitoring_form['address']); ?></td>
	</tr>
	<?php endif; ?>

	<tr>
		<th>Post code</th>
		<td>
			<?php
			if ($pct_id)
			{
				$pc = format_postcode($monitoring_form['post_code']);
				if ($pc)
				{
					echo current(explode(' ', $pc));
				}
			}
			else
			{
				echo $monitoring_form['post_code'];
			}
			?>
		</td>
	</tr>

	<?php if ( ! $pct_id): ?>
	<tr>
		<th>Daytime tel</th>
		<td><?php echo ($monitoring_form['tel_daytime'] ? $monitoring_form['tel_daytime'] : 'N/A'); ?></td>
		<td class="e"></td>
	</tr>

	<tr>
		<th>Mobile tel</th>
		<td><?php echo ($monitoring_form['tel_mobile'] ? $monitoring_form['tel_mobile'] : 'N/A'); ?></td>
	</tr>
	<?php endif; ?>

	<tr>
		<th>SMS support</th>
		<td><?php echo ($monitoring_form['sms'] ? 'Yes' : 'No'); ?></td>
	</tr>

	<?php if ( ! $pct_id): ?>
	<tr>
		<th>Alternative tel</th>
		<td><?php echo ($monitoring_form['tel_alt'] ? $monitoring_form['tel_alt'] : 'N/A'); ?></td>
	</tr>

	<tr>
		<th>Email address</th>
		<td><?php echo ($monitoring_form['email'] ? $monitoring_form['email'] : 'N/A'); ?></td>
	</tr>
	<?php endif; ?>

	<tr>
		<th>Exempt from prescription charge</th>
		<td><?php echo ($monitoring_form['exempt_from_prescription_charge'] ? 'Yes' : 'No'); ?></td>
	</tr>

	<tr>
		<th>Pregnant</th>
		<td><?php echo ($monitoring_form['pregnant'] ? 'Yes' : 'No'); ?></td>
	</tr>

	<tr>
		<th>Breastfeeding</th>
		<td><?php echo ($monitoring_form['breastfeeding'] ? 'Yes' : 'No'); ?></td>
	</tr>

	<tr>
		<th>Occupation code</th>
		<td><?php echo $monitoring_form['occupation_code']; ?></td>
	</tr>

	<tr>
		<th>Ethnic group</th>
		<td><?php echo $monitoring_form['ethnic_group']; ?></td>
	</tr>

	<tr>
		<th>GP practice</th>
		<td><em><?php echo ($monitoring_form['gp_name'] ? $monitoring_form['gp_name'] : 'N/A'); ?></em>
			<br /><?php echo ($monitoring_form['gp_surgery'] ? nl2br($monitoring_form['gp_surgery']) : 'N/A'); ?>
			<br /><?php echo ($monitoring_form['gp_code'] ? $monitoring_form['gp_code'] : 'N/A'); ?>
		</td>
	</tr>

	<tr>
		<th>GP name</th>
		<td><?php echo ($monitoring_form['gp_personal_name'] ? $monitoring_form['gp_personal_name'] : 'N/A'); ?></td>
	</tr>

</table>

<h2>Monitoring information</h2>

<table class="form vat">

	<tr>
		<th>How client heard about service</th>
		<td><?php echo ($monitoring_form['marketing_other'] ? $monitoring_form['marketing_other'] : $monitoring_form['ms_title']); ?></td>
	</tr>

	<tr>
		<th>Date of last tobacco use</th>
		<td><?php echo $monitoring_form['date_of_last_tobacco_use_format']; ?></td>
	</tr>

	<tr>
		<th>Agreed quit date</th>
		<td><?php echo $monitoring_form['agreed_quit_date_format']; ?></td>
	</tr>

	<tr>
		<th>When was the quit date set</th>
		<td><?php echo $monitoring_form['quit_date_set']; ?></td>
	</tr>

	<tr>
		<th>Date of 4 week follow up</th>
		<td><?php echo $monitoring_form['date_of_4_week_follow_up_format']; ?></td>
	</tr>

	<tr>
		<th>Type of intervention delivered</th>
		<td><?php echo ($monitoring_form['intervention_type_other'] ? $monitoring_form['intervention_type_other'] : $monitoring_form['intervention_type']); ?></td>
	</tr>

	<tr>
		<th>Type of pharmacological support used</th>
		<td>
			<?php
			$supports = array($monitoring_form['support_1'], $monitoring_form['support_2']);
			$supports = array_filter($supports, 'strlen');
			$method = $this->config->item("{$monitoring_form['support_method']}", 'support_methods');

			if (count($supports) > 0)
			{
				echo implode('<br>', $supports);
			}

			if (count($supports) == 2 && $method) {
				echo '<br><br>' . $method['name'] . '</p>';
			}

			?>

			<?php if($monitoring_form['mf_prescribing_gp']) echo '<br><p><small>Prescribed by ' . $monitoring_form['mf_prescribing_gp'] . '</small></p>'; ?>

			<?php if ($monitoring_form['support_none'] == 1) : ?>

			No product prescribed.

			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th>Use of Unlicensed Nicotine Containing Products</th>
		<td>
			<?php
			if (strlen($monitoring_form['uncp']) == 0)
			{
				echo 'N/A';
			}
			elseif ($monitoring_form['uncp'] == 1)
			{
				echo 'Yes - Used ';
				echo lcfirst($this->config->item($monitoring_form['uncp_method'], 'uncp_methods'));
			}
			elseif ($monitoring_form['uncp'] == 0)
			{
				echo 'No';
			}
			?>
		</td>
	</tr>

	<tr>
		<th>Treatment outcome</th>
		<td><?php echo ($monitoring_form['treatment_outcome'] ? $monitoring_form['treatment_outcome'] : 'N/A - Follow up required'); ?></td>
	</tr>

	<?php if($monitoring_form['referral_source']) : ?>

	<tr>
		<th>Referral source</th>
		<td><?php echo $monitoring_form['referral_source']; ?></td>
	</tr>

	<tr>
		<th>Function</th>
		<td><?php echo $monitoring_form['function']; ?></td>
	</tr>

	<tr>
		<th>Previously treated</th>
		<td><?php echo ($monitoring_form['previously_treated'] ? 'Yes' : 'No'); ?></td>
	</tr>

	<?php endif; ?>

	<tr>
		<th>Any notes relating to client, monitoring form or pharmacological support.</th>
		<td><?php echo ($monitoring_form['notes'] ? nl2br($monitoring_form['notes']) : 'N/A'); ?></td>
	</tr>

	<tr>
		<th>Client consents to treatment, follow up and pass on of outcome data to GP</th>
		<td><?php echo ($monitoring_form['consent'] ? 'Yes' : 'No'); ?></td>
	</tr>

</table>
