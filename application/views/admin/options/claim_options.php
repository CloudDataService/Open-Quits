<style type="text/css">
	table.form tr th {
        width:200px;
    }
</style>

<?php if ( ! $pct_id): ?>

<div class="header">
    <h2>Local Authority</h2>
</div>

<form method="get" action="<?php echo current_url() ?>">

	<div class="item">

		<table class="form">

			<tr>
				<th>
					<label for="initial">Local Authority</label>
				</th>
				<td>
					<select name="pct_id" id="pct_id" <?php if ($pct_id): ?> disabled="disabled" <?php endif; ?>>
						<option value="">-- Please Select --</option>
						<?php foreach($pcts as $pct) : ?>
						<option value="<?php echo $pct['id']; ?>" <?php if($this->input->get('pct_id') == $pct['id']) echo 'selected="selected"'; ?>><?php echo $pct['pct_name']; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td class="e"></td>
			</tr>

		</table>

	</div>

</form>

<?php endif; ?>


<?php if ($options_pct_id): ?>

<form action="" method="post" id="claim_options_form">

	<?php echo form_hidden('pct_id', $options_pct_id); ?>

    <div class="header">
        <h2>Default claim costs</h2>
    </div>

    <div class="item">

        <p>If a service provider is not assigned to a group and does not have its own claim costs, the following default costs will be used when a claim is submitted.</p>

        <table class="form">

            <tr>
                <th>
                    <label for="initial">Initial claim</label>
                    <small>The default cost of the inital claim</small>
                </th>
                <td>&pound; <input type="text" name="initial" id="initial" value="<?php echo $claim_options['initial']; ?>" class="text" style="width:50px;" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th>
                    <label for="claim_4_week">Follow up 4 week claim</label>
                    <small>The default cost of a 4 week follow up claim</small>
                </th>
                <td>&pound; <input type="text" name="claim_4_week" id="claim_4_week" value="<?php echo $claim_options['claim_4_week']; ?>" class="text" style="width:50px;" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

            <tr>
                <th>
                    <label for="claim_12_week">12 week claim</label>
                    <small>The default cost of a 12 week claim</small>
                </th>
                <td>&pound; <input type="text" name="claim_12_week" id="claim_12_week" value="<?php echo $claim_options['claim_12_week']; ?>" class="text" style="width:50px;" maxlength="5" /></td>
                <td class="e"></td>
            </tr>

        </table>

    </div>

    <div class="header">
    	<h2>Claims processing</h2>
    </div>

    <div class="item">

    	<p>Claims can be automatically processed by turning on "automatic pass to finance". Claims that have been submitted by service providers will be automatically collated into a CSV file, sent to specific email addresses and marked as passed to finance. If you do not wish to use this feature, claims can still be processed manually.</p>

    	<table class="form">

        	<tr>
            	<th>
                	<label for="interval">Process interval</label>
                    <small>The interval at which all claims will be processed. This date starts from the last date claims were processed.</small>
                </th>
                <td>
                	<select name="interval" id="interval">
                    	<?php foreach($intervals as $interval => $days) : ?>
                        <option value="<?php echo $days; ?>" <?php if($days == $claim_options['interval']) echo 'selected="selected"'; ?>><?php echo $interval; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="e"></td>
            </tr>


            <tr>
            	<th>
                	<label for="export_schema_id">Export schema</label>
                    <small>The export schema you would like to use to create the CSV file.</small>
                </th>
                <td>
                	<select name="export_schema_id" id="export_schema_id">

                        <option value="0">Entire data</option>

                    	<?php foreach($export_schemas as $export_schema) : ?>
                        <option value="<?php echo $export_schema['id']; ?>" <?php if($export_schema['id'] == @$claim_options['export_schema_id']) echo 'selected="selected"'; ?>><?php echo htmlentities($export_schema['title'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
                <td class="e"></td>
            </tr>

        	<tr>
            	<th>
                	<label for="automatic_pass_to_finance">Turn on automatic pass to finance</label>
                    <small>Claims submitted will be automatically marked as passed to finance.</small>
                </th>
                <td><input type="checkbox" name="automatic_pass_to_finance" id="automatic_pass_to_finance" <?php if($claim_options['automatic_pass_to_finance']) echo 'checked="checked"'; ?> /></td>
                <td class="e"></td>

            </tr>

            <tr>
            	<th>
                	<label for="automatic_email">Email claims</label>
                    <small>Submitted claims will be emailed to specific addresses.</small>
                </th>
                <td><input type="checkbox" name="automatic_email" id="automatic_email" <?php if($claim_options['automatic_email']) echo 'checked="checked"'; ?> /></td>
                <td class="e"></td>

            </tr>

            <tr>
            	<th>
                	<label for="automatic_emails">Email addresses</label>
                    <small>The email addresses that the CSV file will be emailed to. Addresses must be seperated using a comma.</small>
                </th>
                <td><input type="text" name="automatic_emails" id="automatic_emails" value="<?php echo $claim_options['automatic_emails']; ?>" class="text" style="width:500px;" /></td>
                <td class="e"></td>
            </tr>

        </table>

    </div>

    <div class="header">
    	<h2>Rejected claims email</h2>
    </div>
    <div class="item">

    	<p>If a claim or a set of claims are marked as rejected, an email will be sent to the service provider containing a list of the claims rejected as well as a small note.</p>

    	<table class="form">

        	<tr>
            	<th><label for="rejected_claims_email_enabled">Enable rejected claims email</label></th>
                <td><input type="checkbox" name="rejected_claims_email_enabled" id="rejected_claims_email_enabled" value="1" <?php if($claim_options['rejected_claims_email']['enabled']) echo 'checked="checked"'; ?> /></td>
                <td class="e"></td>
            </tr>

            <tr class="vat">
            	<th>
                	<label for="rejected_claims_email_note">Note</label>
                    <small>E.g "Below is a list of claims that were rejected by our finance department, there may be several different reasons as to why. Please can you contact our claims department on 123456789 if you think there may be a problem."</small>
                </th>
                <td><textarea name="rejected_claims_email_note" id="rejected_claims_email_note" style="width:500px; height:125px;"><?php echo htmlentities($claim_options['rejected_claims_email']['note'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                <td class="e"></td>
            </tr>

        </table>

    </div>

    <input type="image" src="/img/btn/save.png" alt="Save" id="save" style="float:right;" />

</form>

<a href="/admin/options" class="back"><img src="/img/btn/back.png" alt="Back" /></a>

<?php endif; ?>
