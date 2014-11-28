<div class="functions">

    <form action="/admin/claims/csv" method="get" id="export_to_csv_form">

    	<?php echo '<input type="hidden" name="export" value="1" />'; ?>
    	<?php echo '<input type="hidden" name="order" value="' . @$_GET['order'] . '" />'; ?>
        <?php echo '<input type="hidden" name="sort" value="' . @$_GET['sort'] . '" />'; ?>
        <?php echo '<input type="hidden" name="date_from" value="' . $_GET['date_from'] . '" />'; ?>
        <?php echo '<input type="hidden" name="date_to" value="' . $_GET['date_to'] . '" />'; ?>
        <?php echo '<input type="hidden" name="claim_type" value="' . @$_GET['claim_type'] . '" />'; ?>
        <?php echo '<input type="hidden" name="status" value="' . @$_GET['status'] . '" />'; ?>
        <?php echo '<input type="hidden" name="sp_id" value="' . @$_GET['sp_id'] . '" />'; ?>
        <?php echo '<input type="hidden" name="pct_id" value="' . @$_GET['pct_id'] . '" />'; ?>
        <?php echo '<input type="hidden" name="location" value="' . @$_GET['location'] . '" />'; ?>

    	<table class="filter">

        	<tr>
            	<td><input type="image" src="/img/btn/export-to-csv.png" alt="Export to CSV" title="Click OK to export <?php echo $total_export . ($total_export == 1 ? ' claim' : ' claims'); ?> to CSV." class="action" /></td>
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

<div class="header">
	<h2>Filter</h2>
</div>
<div class="item">

	<form action="/admin/claims" method="get">

        <table class="filter">

            <tr>
                <th><label for="date_from">Date from</label></th>
                <th><label for="date_to">Date to</label></th>
                <th><label for="claim_type">Claim type</label></th>
                <th><label for="status">Status</label></th>
                <th><label for="pp">Results per page</label></th>
            </tr>

            <tr>
            	<td><input type="text" name="date_from" id="date_from" value="<?php echo @$_GET['date_from']; ?>" class="datepicker text" /></td>
                <td><input type="text" name="date_to" id="date_to" value="<?php echo @$_GET['date_to']; ?>" class="datepicker text" /></td>
                <td>
                	<select name="claim_type" id="claim_type">

                        <option value="">-- All --</option>
                        <?php foreach($claim_types as $claim_type) : ?>
                        <option value="<?php echo $claim_type; ?>" <?php if(@$_GET['claim_type'] == $claim_type) echo 'selected="selected"'; ?>><?php echo $claim_type; ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
                <td>
                	<select name="status" id="status">

                        <option value="">-- All --</option>
                        <?php foreach($status as $status) : ?>
                        <option value="<?php echo $status; ?>" <?php if(@$_GET['status'] == $status) echo 'selected="selected"'; ?>><?php echo $status; ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
                <td>
                	<select name="pp" id="pp">
                   		<?php foreach($pp as $pp) : ?>
                        <option value="<?php echo $pp; ?>" <?php if(@$_GET['pp'] == $pp) echo 'selected="selected"'; ?>><?php echo $pp; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="image" src="/img/btn/filter.png" alt="Filter" /></td>
                <td><a href="/admin/claims"><img src="/img/btn/clear.png" alt="Clear" /></a></td>
            </tr>

        </table>

        <table class="filter" style="margin-top:10px;">

            <tr>
            	<th><label for="sp_id">Service provider</label></th>
                <th><label for="pct">Local Authority</label></th>
                <th><label for="location">Location/setting</label></th>
            </tr>

            <tr>
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

	</form>

</div>

<div class="total">
	<?php echo $total; ?>
</div>

<div class="header">
	<h2>Results</h2>
</div>

<form action="" method="post" id="claims_form">

    <div class="item results">

        <?php if($claims) : ?>

		<table class="results">

			<tr class="order">
				<th><input type="checkbox" id="check_all" /></th>
				<th><a href="?order=monitoring_form_id<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'monitoring_form_id') echo ' class="' . $_GET['sort'] . '"'; ?>>ID</a></th>
				<th><a href="?order=service_provider_id<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'service_provider_id') echo ' class="' . $_GET['sort'] . '"'; ?>>Service provider</a></th>
				<th><a href="?order=date_of_claim<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'date_of_claim') echo ' class="' . $_GET['sort'] . '"'; ?>>Date of claim</a></th>
				<th><a href="?order=claim_type<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'claim_type') echo ' class="' . $_GET['sort'] . '"'; ?>>Claim type</a></th>
				<th><a href="?order=cost<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'cost') echo ' class="' . $_GET['sort'] . '"'; ?>>Cost</a></th>
				<th><a href="?order=status<?php echo $sort; ?>"<?php if(@$_GET['order'] == 'status') echo ' class="' . $_GET['sort'] . '"'; ?>>Status</a></th>

			</tr>

			<?php foreach($claims as $claim) : ?>

			<tr class="row no_click">

				<td><input type="checkbox" name="claims[]" value="<?php echo $claim['monitoring_form_id'] . '|' . $claim['claim_type']; ?>" class="claim" /></td>
				<td>#<?php echo $claim['monitoring_form_id']; ?></td>
				<td><?php echo $claim['sp_name']; ?></td>
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

        <p class="no_results">Your search returned no claims.</p>

        <?php endif; ?>

    </div>

    <div class="item" style="padding:0px;">

		<table class="form" style="float:right;">

			<tr>
				<th><label for="status">Set claim status</label></th>
				<td>
					<select name="set_status" id="set_status">
						<option value="">-- Please select --</option>
						<option value="Pending">Pending</option>
						<option value="Passed to finance">Passed to finance</option>
						<option value="Rejected">Rejected</option>
					</select>
				</td>
				<td><input type="image" src="/img/btn/save.png" alt="Save" id="save" /></td>
			</tr>

		</table>

        <div class="clear"></div>

    </div>

</form>

<?php echo $this->pagination->create_links(); ?>
