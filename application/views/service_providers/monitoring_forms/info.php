<style type="text/css">
	table.form tr th {
		width:150px;
	}
</style>

<div class="functions">

	<a href="/service-providers/monitoring-forms/set/<?php echo $monitoring_form['id']; ?>#treatment_outcome"><img src="/img/btn/update.png" alt="Update" /></a>

	<a href="?export=print" class="window"><img src="/img/btn/print.png" alt="Print" /></a>

	<div class="clear"></div>

</div>

<div class="panel_left">

	<div class="header">
		<h2>Client information</h2>
	</div>

	<div class="item">

		<table class="form vat">

			<tr>
				<th>NHS number</th>
				<td><?php echo ($monitoring_form['nhs_number'] ? $monitoring_form['nhs_number'] : 'N/A'); ?></td>
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

			<tr>
				<th>Post code</th>
				<td><?php echo $monitoring_form['post_code']; ?></td>
			</tr>

			<tr>
				<th>Daytime tel</th>
				<td><?php echo ($monitoring_form['tel_daytime'] ? $monitoring_form['tel_daytime'] : 'N/A'); ?></td>
				<td class="e"></td>
			</tr>

			<tr>
				<th>Mobile tel</th>
				<td><?php echo ($monitoring_form['tel_mobile'] ? $monitoring_form['tel_mobile'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>SMS consent</th>
				<td><?php echo ($monitoring_form['sms'] ? 'Yes' : 'No'); ?></td>
			</tr>

			<tr>
				<th>Alternative tel</th>
				<td><?php echo ($monitoring_form['tel_alt'] ? $monitoring_form['tel_alt'] : 'N/A'); ?></td>
			</tr>

			<tr>
				<th>Email address</th>
				<td><?php echo ($monitoring_form['email'] ? $monitoring_form['email'] : 'N/A'); ?></td>
			</tr>

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

	</div>

</div>

<div class="panel_right">

	<div class="header">
		<h2>Monitoring information</h2>
	</div>

	<div class="item">

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
				<th>Date of 12 week follow up</th>
				<td><?php echo $monitoring_form['date_of_12_week_follow_up_format']; ?></td>
			</tr>

			<tr>
				<th>Type of intervention delivered</th>
				<td><?php echo ($monitoring_form['intervention_type_other'] ? $monitoring_form['intervention_type_other'] : $monitoring_form['intervention_type']); ?></td>
			</tr>

			<tr>
				<th>Pharmacological support</th>
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
				<th>Treatment outcome (4)</th>
				<td><?php echo ($monitoring_form['treatment_outcome_4'] ? $monitoring_form['treatment_outcome_4'] : 'N/A - Follow up required'); ?></td>
			</tr>

			<?php if ($monitoring_form['treatment_outcome_4'] == 'Quit CO verified'): ?>
			<tr>
				<th>Quit CO reading</th>
				<td><?php
				$reading = element('co_quit', $monitoring_form);
				if ( ! empty($reading))
				{
					echo "$reading ppm";
				}
				else
				{
					echo "N/A";
				}
				?></td>
			</tr>
			<?php endif; ?>

			<tr>
				<th>Treatment outcome (12)</th>
				<td><?php echo ($monitoring_form['treatment_outcome_12'] ? $monitoring_form['treatment_outcome_12'] : 'N/A - Follow up required'); ?></td>
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

			<tr>
				<th>Advisor</th>
				<td><?php echo element('advisor_code', $monitoring_form, element('advisor', $monitoring_form, 'N/A')) ?></td>
			</tr>

		</table>

	</div>


	<div class="header">
		<h2>Health</h2>
	</div>

	<div class="item">

		<table class="form vat">

			<tr>
				<th>Health problems</th>
				<td><?php
				$hps = element('health_problems_array', $monitoring_form, array());
				$hps[] = $monitoring_form['health_problems_other'];
				$hps = array_filter($hps, 'strlen');
				echo (count($hps) > 0) ? implode(', ', $hps) : 'N/A';
				echo ($monitoring_form['health_problems_not_reported'] == 1) ? '<p style="color:#666">Not reported</strong></p>' : '';
				?></td>
			</tr>

			<tr>
				<th>Drinks alcohol</th>
				<td><?php
				$alcohol = array(0 => 'No', 1 => 'Yes');
				echo ($monitoring_form['alcohol'] == NULL) ? 'N/A' : $alcohol[ (int) $monitoring_form['alcohol'] ];
				echo ($monitoring_form['alcohol_not_reported'] == 1) ? '<p style="color:#666">Not reported</strong></p>' : '';
				?></td>
			</tr>

		</table>

	</div>

</div>


<div class="clear"></div>


<div class="header" id="claims">
	<h2>Claims</h2>
</div>
<div class="item results">

<?php if($claims) : ?>

	<table class="results">

		<tr class="order">

			<th>Date of claim</th>
			<th>Claim type</th>
			<th>Cost</th>
			<th>Status</th>

		</tr>

		<?php foreach($claims as $claim) : ?>

		<tr class="row no_click">

			<td><?php echo $claim['date_of_claim_format']; ?></td>
			<td><?php echo $claim['claim_type']; ?></td>
			<td><?php echo $claim['cost']; ?></td>
			<td>
			<?php
			switch($claim['status'])
			{
				case 'Pending' :
					echo '<span style="color:#555;">' . $claim['status'] . '</span>';
				break;
				case 'Passed to finance' :
					echo '<span style="color:#2e841a;">' . $claim['status'] . '</span>';
				break;
				case 'Rejected' :
					echo '<span style="color:#f00;">' . $claim['status'] . '</span>';
				break;
			}
			?>
			</td>

		</tr>

		<?php endforeach; ?>

	</table>

	<?php else : ?>

	<p class="no_results">There are no claims for this monitoring form.</p>

	<?php endif; ?>

</div>


<div class="clear"></div>



<div class="header" id="claims">
	<h2>SMS Record</h2>
</div>
<div class="item results">
	<?php if($monitoring_form['sms'] != 'Yes'): ?>
		<form action="/service-providers/monitoring-forms/info/<?= $monitoring_form['id']; ?>" method="post" id="new_message_form">
			<?php
			echo form_hidden('mf_id', $monitoring_form['id']);
			echo form_hidden('sms_valid', 1);
			?>
			<table class="filter" style="float: left">
				<tr>
					<td>
						Template:
						<select name="sms_t_id" class="sms_t_id">
							<option value="">-- Please select --</option>
							<?php foreach($sms_templates as $t) : ?>
							<option value="<?php echo $t['sms_t_id']; ?>" data-text="<?php echo $t['sms_t_text']; ?>"><?php echo $t['sms_t_title']; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input type="image" src="/img/btn/send-sms.png" alt="Send SMS" title="Click OK to send an SMS to this client." class="action" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Content: <span class="js-sms_preview"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						&nbsp;
					</td>
				</tr>
			</table>
		</form>
	<?php else: ?>
		<p class="no_results">This client has not consented to SMS.</p>
	<?php endif; ?>

<?php if($smses) : ?>
	<table class="results">
		<tr class="order">
			<th>Date sent</th>
			<th>Message</th>
			<th>Sent by</th>
			<th>Status</th>
		</tr>

		<?php foreach($smses as $sms) : ?>
		<tr class="row no_click">
			<td><?php echo date('dS M Y', $sms['s_created']); ?></td>
			<td><?php echo $sms['s_message']; ?></td>
			<td><?php echo ($sms['s_a_id'] != '' ? $sms['admin_name'] : $sms['sps_name']); ?></td>
			<td><?php echo ($sms['s_status'] == '' ? 'Pending' : $sms['s_status']); ?></td>
		</tr>
		<?php endforeach; ?>

	</table>

<?php else : ?>
	<p class="no_results">No messages have been sent for this monitoring form.</p>
<?php endif; ?>

</div>

<a href="/service-providers/monitoring-forms" class="back"><img src="/img/btn/back.png" alt="Back" /></a>


<script type="text/javascript">
$(document).ready(function () {
	$('form#new_message_form .sms_t_id').on('change', function() {
		//show a preview
		$('.js-sms_preview').text( $(this).find(':selected').data('text') );
	});
});
</script>
